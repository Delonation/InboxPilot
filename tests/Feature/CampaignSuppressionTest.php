<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\Contact;
use App\Models\Unsubscribe;
use App\Models\User;
use App\Services\CampaignSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CampaignSuppressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_unsubscribed_and_invalid_contacts_are_skipped_when_building_recipients(): void
    {
        $user = User::factory()->create();

        // A normal contact, an unsubscribed contact, and one on the suppression list.
        $ok = Contact::create(['user_id' => $user->id, 'email' => 'ok@example.com']);
        $unsub = Contact::create(['user_id' => $user->id, 'email' => 'gone@example.com', 'is_unsubscribed' => true]);
        $suppressed = Contact::create(['user_id' => $user->id, 'email' => 'blocked@example.com']);

        Unsubscribe::create([
            'user_id' => $user->id,
            'contact_id' => $suppressed->id,
            'email' => $suppressed->email,
            'token' => Str::random(40),
            'unsubscribed_at' => now(),
        ]);

        $campaign = Campaign::create([
            'user_id' => $user->id,
            'name' => 'Test',
            'status' => Campaign::STATUS_SENDING,
        ]);

        $total = app(CampaignSender::class)->materializeRecipients($campaign, 'all');

        $this->assertSame(3, $total);

        $this->assertSame(CampaignRecipient::STATUS_PENDING, $this->statusFor($campaign, $ok->email));
        $this->assertSame(CampaignRecipient::STATUS_SKIPPED_UNSUBSCRIBED, $this->statusFor($campaign, $unsub->email));
        $this->assertSame(CampaignRecipient::STATUS_SKIPPED_UNSUBSCRIBED, $this->statusFor($campaign, $suppressed->email));

        // Only the deliverable contact should remain pending.
        $this->assertSame(1, $campaign->pendingRecipients()->count());
    }

    private function statusFor(Campaign $campaign, string $email): string
    {
        return CampaignRecipient::where('campaign_id', $campaign->id)->where('email', $email)->value('status');
    }
}
