<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\ContactImport;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Streamed, chunked CSV contact importer.
 *
 * The file is stored privately under storage/app/imports/{import_id}.csv (never
 * web-accessible) and processed in small row batches via repeated AJAX calls,
 * mirroring the campaign sender so a single request never hits PHP's execution
 * limit on shared hosting. The temp file is deleted when the import completes.
 */
class CsvImporter
{
    /** Recognised header names mapped to contact fields. */
    private const FIELDS = ['email', 'first_name', 'last_name', 'phone', 'company', 'tags'];

    /**
     * Validate + store an upload and create the import record.
     *
     * @return array{import: ContactImport, total: int}
     *
     * @throws \RuntimeException when the header row has no email column.
     */
    public function prepare(UploadedFile $file, User $user): array
    {
        $import = ContactImport::create([
            'user_id' => $user->id,
            'filename' => $file->getClientOriginalName(),
            'status' => 'processing',
        ]);

        $path = $this->path($import);
        Storage::put($path, file_get_contents($file->getRealPath()));

        $handle = fopen(Storage::path($path), 'r');
        $header = $this->normaliseHeader(fgetcsv($handle) ?: []);

        if (! in_array('email', $header, true)) {
            fclose($handle);
            Storage::delete($path);
            $import->update(['status' => 'failed']);
            throw new \RuntimeException('The CSV must include an "email" column.');
        }

        // Count data rows (excludes header).
        $total = 0;
        while (fgetcsv($handle) !== false) {
            $total++;
        }
        fclose($handle);

        $import->update(['total_rows' => $total]);

        return ['import' => $import, 'total' => $total];
    }

    /**
     * Process up to $limit data rows starting after $offset already-processed rows.
     *
     * @return array{processed: int, offset: int, done: bool}
     */
    public function processBatch(ContactImport $import, int $offset, int $limit): array
    {
        $path = $this->path($import);

        if (! Storage::exists($path)) {
            $import->update(['status' => 'failed']);

            return ['processed' => 0, 'offset' => $offset, 'done' => true];
        }

        $handle = fopen(Storage::path($path), 'r');
        $header = $this->normaliseHeader(fgetcsv($handle) ?: []);
        $index = array_flip($header);

        // Skip rows already processed in earlier batches.
        for ($i = 0; $i < $offset; $i++) {
            if (fgetcsv($handle) === false) {
                break;
            }
        }

        $processed = 0;
        $counts = ['imported' => 0, 'skipped_duplicates' => 0, 'invalid_emails' => 0, 'failed_rows' => 0];

        while ($processed < $limit && ($row = fgetcsv($handle)) !== false) {
            $processed++;
            $this->importRow($import->user_id, $row, $index, $counts);
        }

        $done = feof($handle) || $processed < $limit;
        fclose($handle);

        // Persist incremental counters.
        $import->increment('imported', $counts['imported']);
        $import->increment('skipped_duplicates', $counts['skipped_duplicates']);
        $import->increment('invalid_emails', $counts['invalid_emails']);
        $import->increment('failed_rows', $counts['failed_rows']);

        $newOffset = $offset + $processed;

        if ($newOffset >= $import->total_rows || $done) {
            $import->update(['status' => 'completed']);
            Storage::delete($path); // never leave contact data on disk
            $done = true;
        }

        return ['processed' => $processed, 'offset' => $newOffset, 'done' => $done];
    }

    /**
     * @param  array<int, string>  $row
     * @param  array<string, int>  $index
     * @param  array<string, int>  $counts
     */
    private function importRow(int $userId, array $row, array $index, array &$counts): void
    {
        $get = fn (string $field) => isset($index[$field]) ? trim((string) ($row[$index[$field]] ?? '')) : null;

        $email = strtolower((string) $get('email'));

        if (blank($email)) {
            $counts['failed_rows']++;

            return;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $counts['invalid_emails']++;

            return;
        }

        $exists = Contact::where('user_id', $userId)->where('email', $email)->exists();
        if ($exists) {
            $counts['skipped_duplicates']++;

            return;
        }

        try {
            Contact::create([
                'user_id' => $userId,
                'email' => $email,
                'first_name' => $get('first_name'),
                'last_name' => $get('last_name'),
                'phone' => $get('phone'),
                'company' => $get('company'),
                'tags' => $get('tags'),
            ]);
            $counts['imported']++;
        } catch (\Throwable $e) {
            $counts['failed_rows']++;
        }
    }

    /** @return array<int, string> */
    private function normaliseHeader(array $header): array
    {
        return array_map(function ($h) {
            $key = Str::of((string) $h)->trim()->lower()->replace(' ', '_')->toString();

            return in_array($key, self::FIELDS, true) ? $key : $key;
        }, $header);
    }

    private function path(ContactImport $import): string
    {
        return "imports/{$import->id}.csv";
    }
}
