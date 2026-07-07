<?php

namespace App\Http\Controllers;

use App\Models\ContactImport;
use App\Services\ActivityLogger;
use App\Services\CsvImporter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactImportController extends Controller
{
    public function create(): View
    {
        return view('contacts.import', [
            'recent' => request()->user()->imports()->latest()->limit(5)->get(),
        ]);
    }

    /**
     * Validate + store the upload, create the import record, and hand back the
     * import id + total rows. The browser then drives processing in batches.
     */
    public function store(Request $request, CsvImporter $importer): RedirectResponse
    {
        $request->validate([
            'file' => [
                'required', 'file', 'mimes:csv,txt', 'mimetypes:text/csv,text/plain,application/csv',
                'max:'.config('inboxpilot.csv.max_kb'),
            ],
        ]);

        try {
            ['import' => $import, 'total' => $total] = $importer->prepare($request->file('file'), $request->user());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        ActivityLogger::user($request->user()->id, 'contacts_import_started', $import->filename);

        return redirect()->route('contacts.import.show', $import);
    }

    /** AJAX: process one batch of rows. */
    public function batch(Request $request, CsvImporter $importer, ContactImport $import): JsonResponse
    {
        abort_if($import->user_id !== $request->user()->id, 403);

        $offset = (int) $request->integer('offset');
        $result = $importer->processBatch($import, $offset, config('inboxpilot.csv.import_chunk'));

        return response()->json($result + [
            'total' => $import->total_rows,
            'summary' => $import->only(['imported', 'skipped_duplicates', 'invalid_emails', 'failed_rows']),
        ]);
    }

    public function show(Request $request, ContactImport $import): View
    {
        abort_if($import->user_id !== $request->user()->id, 403);

        return view('contacts.import-progress', ['import' => $import]);
    }
}
