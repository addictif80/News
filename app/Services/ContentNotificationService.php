<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Page;
use App\Models\User;
use App\Notifications\ContentPublishedNotification;
use Illuminate\Support\Facades\Notification;

class ContentNotificationService
{
    /**
     * Send a push notification to the audience selected on the content's
     * one-shot notify checkboxes, then clear those flags so the next save
     * doesn't resend it.
     */
    public function dispatchIfRequested(Article|Page $content, string $title, string $url, ?string $excerpt): void
    {
        if (! $content->notify_all && ! $content->notify_free && ! $content->notify_subscribers) {
            return;
        }

        $recipients = $this->resolveRecipients($content);

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new ContentPublishedNotification($title, $excerpt, $url));
        }

        $content->newQuery()->whereKey($content->getKey())->update([
            'notify_all' => false,
            'notify_free' => false,
            'notify_subscribers' => false,
        ]);
    }

    private function resolveRecipients(Article|Page $content)
    {
        $query = User::query()->whereHas('pushSubscriptions');

        if (! $content->notify_all) {
            $roles = array_filter([
                $content->notify_free ? 'gratuit' : null,
                $content->notify_subscribers ? 'abonne' : null,
            ]);

            $query->role($roles);
        }

        return $query->get();
    }
}
