<?php

namespace App\Observers;

use App\Models\Article;
use App\Services\ContentNotificationService;

class ArticleObserver
{
    public function saved(Article $article): void
    {
        if ($article->status !== 'published') {
            return;
        }

        if (! $article->notify_all && ! $article->notify_free && ! $article->notify_subscribers) {
            return;
        }

        app(ContentNotificationService::class)->dispatchIfRequested(
            $article,
            $article->title,
            route('articles.show', $article->slug),
            $article->excerpt,
        );
    }
}
