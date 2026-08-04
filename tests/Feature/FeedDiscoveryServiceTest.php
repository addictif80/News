<?php

namespace Tests\Feature;

use App\Services\FeedDiscoveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FeedDiscoveryServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_discovers_an_absolute_rss_feed_url(): void
    {
        Http::fake([
            'example.com/*' => Http::response(
                '<html><head><link rel="alternate" type="application/rss+xml" href="https://example.com/rss.xml"></head></html>',
                200,
            ),
        ]);

        $feed = (new FeedDiscoveryService)->discover('https://example.com/');

        $this->assertSame('https://example.com/rss.xml', $feed);
    }

    public function test_it_resolves_a_relative_feed_url_against_the_page_origin(): void
    {
        Http::fake([
            'example.com/*' => Http::response(
                '<html><head><link rel="alternate" type="application/rss+xml" href="/feed/"></head></html>',
                200,
            ),
        ]);

        $feed = (new FeedDiscoveryService)->discover('https://example.com/blog');

        $this->assertSame('https://example.com/feed/', $feed);
    }

    public function test_it_falls_back_to_atom_when_no_rss_link_exists(): void
    {
        Http::fake([
            'example.com/*' => Http::response(
                '<html><head><link rel="alternate" type="application/atom+xml" href="https://example.com/atom.xml"></head></html>',
                200,
            ),
        ]);

        $feed = (new FeedDiscoveryService)->discover('https://example.com/');

        $this->assertSame('https://example.com/atom.xml', $feed);
    }

    public function test_it_returns_null_when_no_feed_is_advertised(): void
    {
        Http::fake([
            'example.com/*' => Http::response('<html><head></head></html>', 200),
        ]);

        $this->assertNull((new FeedDiscoveryService)->discover('https://example.com/'));
    }

    public function test_it_returns_null_on_a_failed_request(): void
    {
        Http::fake([
            'example.com/*' => Http::response('', 500),
        ]);

        $this->assertNull((new FeedDiscoveryService)->discover('https://example.com/'));
    }
}
