<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Services\ActivityLogger;
use App\Services\CampaignSender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(Request $request): View
    {
        return view('campaigns.index', [
            'campaigns' => $request->user()->campaigns()->with('template')->latest()->paginate(15),
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->ensureCanSend($request)) {
            return $redirect;
        }

        $user = $request->user();

        return view('campaigns.create', [
            'templates' => $user->templates()->orderBy('name')->get(),
            'tags' => $this->userTags($user),
            'activeContacts' => $user->contacts()->active()->count(),
            // Picker list for the "selected contacts" option. Capped so the page
            // stays light; large lists should use "all" or a tag instead.
            'pickable' => $user->contacts()->active()->orderBy('email')->limit(500)->get(['id', 'email', 'first_name', 'last_name']),
        ]);
    }

    public function store(Request $request, CampaignSender $sender): RedirectResponse
    {
        if ($redirect = $this->ensureCanSend($request)) {
            return $redirect;
        }

        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'template_id' => ['required', 'integer', 'exists:email_templates,id'],
            'subject_override' => ['nullable', 'string', 'max:255'],
            'selection' => ['required', 'in:all,selected,tag'],
            'contact_ids' => ['array'],
            'contact_ids.*' => ['integer'],
            'tag' => ['nullable', 'string', 'max:255'],
        ]);

        $template = $user->templates()->findOrFail($data['template_id']);
        $smtp = $user->smtpSetting;

        $campaign = $user->campaigns()->create([
            'template_id' => $template->id,
            'name' => $data['name'],
            'subject_override' => $data['subject_override'] ?? null,
            'status' => Campaign::STATUS_SENDING,
            'sender_email' => $smtp->from_email,
            'smtp_summary' => $smtp->summary(),
            'started_at' => now(),
        ]);

        $total = $sender->materializeRecipients(
            $campaign,
            $data['selection'],
            $data['contact_ids'] ?? [],
            $data['tag'] ?? null,
        );

        if ($total === 0) {
            $campaign->delete();

            return back()->withInput()->with('error', 'No contacts matched your selection. Add contacts or pick a different audience.');
        }

        ActivityLogger::user($user->id, 'campaign_started', $campaign->name);

        return redirect()->route('campaigns.send', $campaign);
    }

    /** Sending status page. Resumable: reclaims any stalled recipients first. */
    public function send(Request $request, CampaignSender $sender, Campaign $campaign): View|RedirectResponse
    {
        $this->authorizeCampaign($request, $campaign);

        if ($campaign->isFinished()) {
            return redirect()->route('campaigns.show', $campaign);
        }

        $sender->reclaimStale($campaign);

        return view('campaigns.send', ['campaign' => $campaign]);
    }

    /** AJAX: process one send batch. */
    public function batch(Request $request, CampaignSender $sender, Campaign $campaign): JsonResponse
    {
        $this->authorizeCampaign($request, $campaign);

        $progress = $sender->processBatch($campaign);

        return response()->json($progress);
    }

    public function show(Request $request, Campaign $campaign): View
    {
        $this->authorizeCampaign($request, $campaign);
        $campaign->load('template');

        $breakdown = CampaignRecipient::where('campaign_id', $campaign->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('campaigns.show', compact('campaign', 'breakdown'));
    }

    public function recipients(Request $request, Campaign $campaign): View
    {
        $this->authorizeCampaign($request, $campaign);

        $query = $campaign->recipients()->latest('id');

        if ($search = trim((string) $request->get('q'))) {
            $query->where('email', 'like', "%{$search}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        return view('campaigns.recipients', [
            'campaign' => $campaign,
            'recipients' => $query->paginate(25)->withQueryString(),
            'search' => $search,
            'status' => $status,
        ]);
    }

    private function ensureCanSend(Request $request): ?RedirectResponse
    {
        if (! $request->user()->canSend()) {
            return redirect()->route('setup.index')
                ->with('warning', 'Finish setup and pass a test email before sending campaigns.');
        }

        return null;
    }

    private function userTags($user): array
    {
        $tags = [];
        $user->contacts()->whereNotNull('tags')->pluck('tags')->each(function ($t) use (&$tags) {
            foreach (array_filter(array_map('trim', explode(',', $t))) as $tag) {
                $tags[$tag] = true;
            }
        });

        return array_keys($tags);
    }

    private function authorizeCampaign(Request $request, Campaign $campaign): void
    {
        abort_if($campaign->user_id !== $request->user()->id, 403);
    }
}
