<?php

namespace Tests\Feature;

use App\Jobs\SendNewsletterCampaignJob;
use App\Mail\NewsletterMail;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NewsletterCampaignTest extends TestCase
{
    use RefreshDatabase;

    public function test_sending_a_campaign_emails_confirmed_subscribers_only(): void
    {
        Mail::fake();

        $confirmed = NewsletterSubscriber::create([
            'email' => 'confirmed@example.com',
            'is_confirmed' => true,
            'subscribed_at' => now(),
        ]);

        NewsletterSubscriber::create([
            'email' => 'unconfirmed@example.com',
            'is_confirmed' => false,
        ]);

        NewsletterSubscriber::create([
            'email' => 'unsubscribed@example.com',
            'is_confirmed' => true,
            'subscribed_at' => now()->subDays(10),
            'unsubscribed_at' => now(),
        ]);

        $campaign = NewsletterCampaign::create([
            'subject' => 'Les actus de la semaine',
            'body_html' => '<p>Bonjour</p>',
            'status' => 'draft',
        ]);

        (new SendNewsletterCampaignJob($campaign))->handle();

        Mail::assertQueued(NewsletterMail::class, 1);
        Mail::assertQueued(NewsletterMail::class, fn (NewsletterMail $mail) => $mail->hasTo($confirmed->email));

        $this->assertSame('sent', $campaign->fresh()->status);
        $this->assertSame(1, $campaign->fresh()->recipients_count);
    }
}
