<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Keyword;
use App\Models\SourceSite;
use App\Services\ArticleImportService;
use App\Services\VeilleService;
use App\Settings\VeilleSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VeilleServiceTest extends TestCase
{
    use RefreshDatabase;

    private function enableVeille(): void
    {
        $settings = app(VeilleSettings::class);
        $settings->is_enabled = true;
        $settings->polling_interval_minutes = 60;
        $settings->requires_admin_validation = true;
        $settings->save();
    }

    public function test_it_imports_matching_articles_from_an_rss_feed(): void
    {
        $this->enableVeille();

        Keyword::create(['term' => 'intelligence artificielle', 'is_active' => true]);

        $site = SourceSite::create([
            'name' => 'Exemple Actu',
            'base_url' => 'https://feed.example.com',
            'rss_feed_url' => 'https://feed.example.com/rss.xml',
            'is_active' => true,
            'used_for_watch' => true,
        ]);

        $rss = <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <rss version="2.0">
              <channel>
                <item>
                  <title>Percée en intelligence artificielle</title>
                  <link>https://feed.example.com/articles/ia</link>
                  <description>Une avancée majeure.</description>
                </item>
                <item>
                  <title>Résultats sportifs du week-end</title>
                  <link>https://feed.example.com/articles/sport</link>
                  <description>Rien à voir.</description>
                </item>
              </channel>
            </rss>
        XML;

        $articlePageHtml = '<html><head><title>Percée en IA</title></head><body><article><p>Contenu suffisamment long pour être retenu comme corps principal.</p></article></body></html>';

        Http::fake([
            'https://feed.example.com/rss.xml' => Http::response($rss, 200),
            'https://feed.example.com/articles/ia' => Http::response($articlePageHtml, 200),
        ]);

        $result = (new VeilleService(app(VeilleSettings::class), app(ArticleImportService::class)))->run();

        $this->assertSame(1, $result['polled']);
        $this->assertSame(1, $result['imported']);
        $this->assertSame(1, Article::count());
        $this->assertSame('https://feed.example.com/articles/ia', Article::first()->source_url);
        $this->assertTrue(Article::first()->requires_admin_validation);
        $this->assertSame('pending_review', Article::first()->status);
        $this->assertNotNull($site->fresh()->last_polled_at);
    }
}
