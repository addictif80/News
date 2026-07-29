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
        $subscribers = NewsletterSubscriber::query()
            ->where('is_confirmed', true)
            ->whereNull('unsubscribed_at')
            ->get();

        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)->send(new NewsletterMail($this->campaign, $subscriber));
        }

        $this->campaign->update([
            'status' => 'sent',
            'sent_at' => now(),
            'recipients_count' => $subscribers->count(),
        ]);
    }
}
