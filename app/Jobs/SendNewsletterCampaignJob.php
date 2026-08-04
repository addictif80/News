<?php

namespace App\Jobs;

use App\Mail\NewsletterMail;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendNewsletterCampaignJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public NewsletterCampaign $campaign) {}

    public function handle(): void
    {
        $recipientsCount = 0;

        NewsletterSubscriber::query()
            ->where('is_confirmed', true)
            ->whereNull('unsubscribed_at')
            ->chunkById(200, function ($subscribers) use (&$recipientsCount) {
                foreach ($subscribers as $subscriber) {
                    // Each email becomes its own queued job so one bad address
                    // (SMTP timeout, bounce) can't block or retry the whole batch.
                    Mail::to($subscriber->email)->queue(new NewsletterMail($this->campaign, $subscriber));
                    $recipientsCount++;
                }
            });

        $this->campaign->update([
            'status' => 'sent',
            'sent_at' => now(),
            'recipients_count' => $recipientsCount,
        ]);
    }
}
