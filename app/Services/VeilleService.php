<?php

namespace App\Services;

use App\Exceptions\DuplicateImportException;
use App\Models\Keyword;
use App\Models\SourceSite;
use App\Settings\VeilleSettings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class VeilleService
{
    public function __construct(
        private readonly VeilleSettings $settings,
        private readonly ArticleImportService $importer,
    ) {}

    /**
     * @return array{polled: int, imported: int}
     */
    public function run(): array
    {
        if (! $this->settings->is_enabled) {
            return ['polled' => 0, 'imported' => 0];
        }

        $keywords = Keyword::query()->where('is_active', true)->pluck('term');

        if ($keywords->isEmpty()) {
            return ['polled' => 0, 'imported' => 0];
        }

        $sites = SourceSite::query()
            ->where('is_active', true)
            ->where('used_for_watch', true)
            ->whereNotNull('rss_feed_url')
            ->get()
            ->filter(fn (SourceSite $site) => $this->isDue($site));

        $polled = 0;
        $imported = 0;

        foreach ($sites as $site) {
            $polled++;
            $imported += $this->pollSite($site, $keywords);
            $site->update(['last_polled_at' => now()]);
        }

        return ['polled' => $polled, 'imported' => $imported];
    }

    private function isDue(SourceSite $site): bool
    {
        if (! $site->last_polled_at) {
            return true;
        }

        return $site->last_polled_at->diffInMinutes(now()) >= $this->settings->polling_interval_minutes;
    }

    /**
     * @param  Collection<int, string>  $keywords
     */
    private function pollSite(SourceSite $site, $keywords): int
    {
        $response = Http::timeout(15)->get($site->rss_feed_url);

        if ($response->failed()) {
            return 0;
        }

        $items = $this->parseFeedItems($response->body());
        $imported = 0;

        foreach ($items as $item) {
            $haystack = Str::lower($item['title'].' '.$item['description']);

            $matchedKeyword = $keywords->first(fn (string $keyword) => str_contains($haystack, Str::lower($keyword)));

            if ($matchedKeyword === null) {
                continue;
            }

            try {
                $this->importer->importFromUrl($item['link'], $matchedKeyword);
                $imported++;
            } catch (DuplicateImportException) {
                // Already recorded in the import journal by the importer.
            } catch (Throwable) {
                // Same — logged by the importer; keep polling the rest of the feed.
            }
        }

        return $imported;
    }

    /**
     * @return array<int, array{title: string, link: string, description: string}>
     */
    private function parseFeedItems(string $xml): array
    {
        $previous = libxml_use_internal_errors(true);
        $feed = simplexml_load_string(ltrim($xml));
        libxml_use_internal_errors($previous);

        if ($feed === false) {
            return [];
        }

        $items = [];

        foreach ($feed->channel->item ?? [] as $item) {
            $items[] = [
                'title' => (string) $item->title,
                'link' => (string) $item->link,
                'description' => (string) $item->description,
            ];
        }

        // Atom feeds use <entry> instead of RSS <item>.
        foreach ($feed->entry ?? [] as $entry) {
            $items[] = [
                'title' => (string) $entry->title,
                'link' => (string) ($entry->link['href'] ?? $entry->link),
                'description' => (string) ($entry->summary ?? $entry->content ?? ''),
            ];
        }

        return $items;
    }
}
