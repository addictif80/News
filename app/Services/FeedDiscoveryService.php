<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use Throwable;

class FeedDiscoveryService
{
    /**
     * Fetches $url and looks for a <link rel="alternate" type="application/rss+xml">
     * (or atom+xml) tag in its <head>, the standard way sites advertise their feed —
     * the same tag this site itself emits for its own RSS feed.
     */
    public function discover(string $url): ?string
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; NewsFeedDiscoveryBot/1.0)',
            ])->get($url);
        } catch (Throwable) {
            return null;
        }

        if ($response->failed()) {
            return null;
        }

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($response->body(), 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        $nodes = $xpath->query('//link[@rel="alternate"][@type="application/rss+xml"]/@href');

        if ($nodes->length === 0) {
            $nodes = $xpath->query('//link[@rel="alternate"][@type="application/atom+xml"]/@href');
        }

        if ($nodes->length === 0) {
            return null;
        }

        return $this->toAbsoluteUrl($nodes->item(0)->nodeValue, $url);
    }

    private function toAbsoluteUrl(string $href, string $baseUrl): string
    {
        if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
            return $href;
        }

        $parts = parse_url($baseUrl);
        $origin = ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? '');

        return str_starts_with($href, '/') ? $origin.$href : $origin.'/'.$href;
    }
}
