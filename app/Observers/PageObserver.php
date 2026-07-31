<?php

namespace App\Observers;

use App\Models\Page;
use App\Services\ContentNotificationService;
use Illuminate\Support\Str;

class PageObserver
{
    public function saved(Page $page): void
    {
        if ($page->status !== 'published') {
            return;
        }

        if (! $page->notify_all && ! $page->notify_free && ! $page->notify_subscribers) {
            return;
        }

        app(ContentNotificationService::class)->dispatchIfRequested(
            $page,
            $page->title,
            route('pages.show', $page->slug),
            Str::limit(strip_tags((string) $page->content), 150),
        );
    }
}
