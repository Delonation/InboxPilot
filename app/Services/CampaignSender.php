<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\Contact;
use App\Models\Unsubscribe;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

/**
 * Browser-driven, chunked campaign sender.
 *
 * There is no queue or cron on the target host. The sending status page POSTs
 * processBatch() repeatedly; each call claims a small set of pending recipients,
 * sends them via the user's SMTP, and writes each result to the database before
 * returning. Because every recipient's state is durable, closing the tab pauses
 * (never corrupts) the campaign and it can be resumed later.
 */
class CampaignSender
{
    public function __construct(
        private readonly SmtpMailer $mailer,
        private readonly PlaceholderRenderer $renderer,
        private readonly HtmlSanitizer $sanitizer,
    ) {}

    /**
     * Create the campaign_recipients rows for the chosen audience. Unsubscribed
     * contacts and invalid emails are pre-marked as skipped so they are never
     * attempted. Returns the total number of recipients created.
     *
     * @param  'all'|'selected'|'tag'  $selection
     * @param  array<int, int>  $contactIds
     */
    public function materializeRecipients(Campaign $campaign, string $selection, array $contactIds = [], ?string $tag = null): int
    {
        $query = Contact::where('user_id', $campaign->user_id);

        $query = match ($selection) {
            'selected' => $query->whereIn('id', $contactIds),
            'tag' => $query->withTag((string) $tag),
            default => $query,
        };

        // Suppression list for this user, lower-cased for comparison.
        $suppressed = Unsubscribe::where('user_id', $campaign->user_id)
            ->pluck('email')
            ->map(fn ($e) => strtolower($e))
            ->flip();

        $total = 0;

        $query->select(['id', 'email', 'first_name', 'last_name', 'is_unsubscribed'])
            ->chunkById(500, function ($contacts) use ($campaign, $suppressed, &$total) {
                $rows = [];
                foreach ($contacts as $contact) {
                    $status = CampaignRecipient::STATUS_PENDING;

                    if ($contact->is_unsubscribed || $suppressed->has(strtolower($contact->email))) {
                        $status = CampaignRecipient::STATUS_SKIPPED_UNSUBSCRIBED;
                    } elseif (! filter_var($contact->email, FILTER_VALIDATE_EMAIL)) {
                        $status = CampaignRecipient::STATUS_SKIPPED_INVALID;
                    }

                    $rows[] = [
                        'campaign_id' => $campaign->id,
                        'contact_id' => $contact->id,
                        'email' => $contact->email,
                        'name' => trim("{$contact->first_name} {$contact->last_name}") ?: null,
                        'status' => $status,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $total++;
                }
                if ($rows) {
                    CampaignRecipient::insert($rows);
                }
            });

        $campaign->update([
            'total_recipients' => $total,
            'total_skipped' => CampaignRecipient::where('campaign_id', $campaign->id)
                ->whereIn('status', [CampaignRecipient::STATUS_SKIPPED_UNSUBSCRIBED, CampaignRecipient::STATUS_SKIPPED_INVALID])
                ->count(),
        ]);

        return $total;
    }

    /**
     * Reset any recipients orphaned in the "processing" state back to pending.
     * Called when the send/status page (re)loads so an interrupted batch resumes.
     */
    public function reclaimStale(Campaign $campaign): void
    {
        CampaignRecipient::where('campaign_id', $campaign->id)
            ->where('status', CampaignRecipient::STATUS_PROCESSING)
            ->update(['status' => CampaignRecipient::STATUS_PENDING]);
    }

    /**
     * Process one batch. Returns live progress for the status page.
     *
     * @return array{sent:int, failed:int, remaining:int, done:bool, totals:array}
     */
    public function processBatch(Campaign $campaign): array
    {
        $smtp = $campaign->user->smtpSetting;

        if (! $smtp) {
            $this->finalize($campaign);

            return $this->progress($campaign, 0, 0, true);
        }

        $chunk = max(1, (int) config('inboxpilot.send.chunk_size'));
        $delayMicro = max(0, (int) config('inboxpilot.send.delay_ms')) * 1000;

        // Atomically claim a batch of pending recipients so overlapping requests
        // (e.g. two open tabs) never grab the same rows.
        $ids = DB::transaction(function () use ($campaign, $chunk) {
            $ids = CampaignRecipient::where('campaign_id', $campaign->id)
                ->where('status', CampaignRecipient::STATUS_PENDING)
                ->orderBy('id')
                ->limit($chunk)
                ->lockForUpdate()
                ->pluck('id');

            if ($ids->isNotEmpty()) {
                CampaignRecipient::whereIn('id', $ids)
                    ->update(['status' => CampaignRecipient::STATUS_PROCESSING]);
            }

            return $ids;
        });

        if ($ids->isEmpty()) {
            $this->finalize($campaign);

            return $this->progress($campaign, 0, 0, true);
        }

        $template = $campaign->template;
        $subjectTemplate = $campaign->effectiveSubject();
        $sent = 0;
        $failed = 0;

        foreach (CampaignRecipient::whereIn('id', $ids)->with('contact')->get() as $recipient) {
            $contact = $recipient->contact;

            // Build a signed, per-recipient unsubscribe link.
            $unsubUrl = $contact
                ? URL::signedRoute('unsubscribe', ['contact' => $contact->id])
                : URL::to('/');

            $data = $contact
                ? $this->renderer->dataForContact($contact, $unsubUrl)
                : ['email' => $recipient->email, 'unsubscribe_url' => $unsubUrl];

            $subject = $this->renderer->render($subjectTemplate, $data);

            $html = null;
            $text = null;
            if ($template) {
                if ($template->isHtml()) {
                    $html = $this->sanitizer->clean($this->renderer->render((string) $template->html_body, $data));
                }
                $text = $this->renderer->render((string) ($template->plain_body ?? ''), $data) ?: null;
            }

            $result = $this->mailer->send($smtp, $recipient->email, $recipient->name, $subject, $html, $text);

            $recipient->update([
                'status' => $result['success'] ? CampaignRecipient::STATUS_SENT : CampaignRecipient::STATUS_FAILED,
                'smtp_response' => $result['response'],
                'error_message' => $result['error'],
                'sent_at' => $result['success'] ? now() : null,
            ]);

            if ($result['success']) {
                $sent++;
            } else {
                $failed++;
                ActivityLogger::smtp($campaign->user_id, 'campaign', false, null, $result['error'], $campaign->id);
            }

            if ($delayMicro > 0) {
                usleep($delayMicro);
            }
        }

        // Update rolling campaign counters.
        $campaign->increment('total_attempted', $sent + $failed);
        $campaign->increment('total_sent', $sent);
        $campaign->increment('total_failed', $failed);

        $remaining = $this->pendingCount($campaign);
        if ($remaining === 0) {
            $this->finalize($campaign);
        }

        return $this->progress($campaign, $sent, $failed, $remaining === 0);
    }

    private function pendingCount(Campaign $campaign): int
    {
        return CampaignRecipient::where('campaign_id', $campaign->id)
            ->whereIn('status', [CampaignRecipient::STATUS_PENDING, CampaignRecipient::STATUS_PROCESSING])
            ->count();
    }

    private function finalize(Campaign $campaign): void
    {
        if ($campaign->isFinished()) {
            return;
        }

        $failed = CampaignRecipient::where('campaign_id', $campaign->id)
            ->where('status', CampaignRecipient::STATUS_FAILED)->count();
        $sent = CampaignRecipient::where('campaign_id', $campaign->id)
            ->where('status', CampaignRecipient::STATUS_SENT)->count();

        $status = match (true) {
            $sent === 0 && $failed > 0 => Campaign::STATUS_FAILED,
            $failed > 0 => Campaign::STATUS_COMPLETED_WITH_ERRORS,
            default => Campaign::STATUS_COMPLETED,
        };

        $campaign->update([
            'status' => $status,
            'completed_at' => now(),
        ]);
    }

    private function progress(Campaign $campaign, int $sent, int $failed, bool $done): array
    {
        $campaign->refresh();

        return [
            'sent' => $sent,
            'failed' => $failed,
            'remaining' => $this->pendingCount($campaign),
            'done' => $done,
            'totals' => [
                'recipients' => $campaign->total_recipients,
                'sent' => $campaign->total_sent,
                'failed' => $campaign->total_failed,
                'skipped' => $campaign->total_skipped,
                'status' => $campaign->status,
            ],
        ];
    }
}
