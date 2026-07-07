<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Unsubscribe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Public, signed unsubscribe flow. The link in each email is a signed URL, so
 * it cannot be forged for another contact. Confirming marks the contact
 * unsubscribed and records a suppression-list entry for the owning user.
 */
class UnsubscribeController extends Controller
{
    public function show(Contact $contact): View
    {
        return view('unsubscribe', [
            'contact' => $contact,
            'done' => $contact->is_unsubscribed,
            // Re-use the same signed URL for the confirm POST.
            'action' => request()->fullUrl(),
        ]);
    }

    public function confirm(Request $request, Contact $contact): RedirectResponse|View
    {
        if (! $contact->is_unsubscribed) {
            $contact->update(['is_unsubscribed' => true]);

            Unsubscribe::updateOrCreate(
                ['user_id' => $contact->user_id, 'email' => $contact->email],
                [
                    'contact_id' => $contact->id,
                    'token' => Str::random(40),
                    'reason' => 'user_unsubscribed',
                    'unsubscribed_at' => now(),
                ]
            );
        }

        return view('unsubscribe', [
            'contact' => $contact->refresh(),
            'done' => true,
            'action' => null,
        ]);
    }
}
