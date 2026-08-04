<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * A cheap invalidation token for cached content fragments (homepage blocks,
 * RSS feeds). Bumping it makes every previously cached key effectively
 * unreachable without having to enumerate or tag them individually — which
 * matters because not every cache driver (e.g. database, file) supports tags.
 */
class ContentCache
{
    private const KEY = 'content-cache-version';

    public static function version(): int
    {
        return (int) Cache::get(self::KEY, 1);
    }

    public static function bump(): void
    {
        Cache::forever(self::KEY, self::version() + 1);
    }
}
