<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = $user->contacts()->latest();

        if ($search = trim((string) $request->get('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($tag = trim((string) $request->get('tag'))) {
            $query->withTag($tag);
        }

        $contacts = $query->paginate(20)->withQueryString();

        return view('contacts.index', [
            'contacts' => $contacts,
            'total' => $user->contacts()->count(),
            'search' => $search,
            'tag' => $tag,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('contacts')->where('user_id', $user->id)],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'string', 'max:255'],
        ]);

        $user->contacts()->create($data + ['email' => strtolower($data['email'])]);
        ActivityLogger::user($user->id, 'contact_added');

        return back()->with('success', 'Contact added.');
    }

    public function edit(Request $request, Contact $contact): View
    {
        $this->authorizeContact($request, $contact);

        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact): RedirectResponse
    {
        $this->authorizeContact($request, $contact);

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('contacts')->where('user_id', $request->user()->id)->ignore($contact->id)],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'string', 'max:255'],
        ]);

        $contact->update($data + ['email' => strtolower($data['email'])]);

        return redirect()->route('contacts.index')->with('success', 'Contact updated.');
    }

    public function destroy(Request $request, Contact $contact): RedirectResponse
    {
        $this->authorizeContact($request, $contact);
        $contact->delete();

        return back()->with('success', 'Contact deleted.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']]);

        $deleted = $request->user()->contacts()->whereIn('id', $data['ids'])->delete();

        return back()->with('success', "{$deleted} contact(s) deleted.");
    }

    public function export(Request $request): StreamedResponse
    {
        $user = $request->user();
        $filename = 'contacts-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($user) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['email', 'first_name', 'last_name', 'phone', 'company', 'tags', 'unsubscribed']);

            $user->contacts()->chunkById(500, function ($contacts) use ($out) {
                foreach ($contacts as $c) {
                    fputcsv($out, [$c->email, $c->first_name, $c->last_name, $c->phone, $c->company, $c->tags, $c->is_unsubscribed ? 'yes' : 'no']);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function authorizeContact(Request $request, Contact $contact): void
    {
        abort_if($contact->user_id !== $request->user()->id, 403);
    }
}
