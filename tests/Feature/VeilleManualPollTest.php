<?php

namespace Tests\Feature;

use App\Filament\Resources\SourceSites\Pages\EditSourceSite;
use App\Filament\Resources\SourceSites\Pages\ListSourceSites;
use App\Models\Article;
use App\Models\Keyword;
use App\Models\SourceSite;
use App\Models\User;
use App\Services\VeilleService;
use App\Settings\VeilleSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VeilleManualPollTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }

    private function fakeFeedAndArticle(): void
    {
        $rss = <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <rss version="2.0">
              <channel>
                <item>
                  <title>Percée en intelligence artificielle</title>
                  <link>https://feed.example.com/articles/ia</link>
                  <description>Une avancée majeure.</description>
                </item>
              </channel>
            </rss>
        XML;

        Http::fake([
            'feed.example.com/rss.xml' => Http::response($rss, 200),
            'feed.example.com/articles/ia' => Http::response(
                '<html><head><title>IA</title></head><body><article><p>Contenu suffisamment long pour être retenu comme corps principal.</p></article></body></html>',
                200,
            ),
        ]);
    }

    public function test_manual_poll_ignores_the_polling_interval(): void
    {
        // Veille disabled + settings irrelevant to a manual trigger — it should still run.
        $settings = app(VeilleSettings::class);
        $settings->is_enabled = false;
        $settings->polling_interval_minutes = 999999;
        $settings->requires_admin_validation = true;
        $settings->save();

        Keyword::create(['term' => 'intelligence artificielle', 'is_active' => true]);

        $site = SourceSite::create([
            'name' => 'Exemple Actu',
            'base_url' => 'https://feed.example.com',
            'rss_feed_url' => 'https://feed.example.com/rss.xml',
            'is_active' => true,
            'used_for_watch' => true,
            'last_polled_at' => now(), // just polled a second ago — a scheduled run() would skip it
        ]);

        $this->fakeFeedAndArticle();

        $result = app(VeilleService::class)->pollNow(collect([$site]));

        $this->assertSame(1, $result['polled']);
        $this->assertSame(1, $result['imported']);
        $this->assertSame(1, Article::count());
    }

    public function test_admin_can_trigger_a_poll_from_the_source_site_row_action(): void
    {
        $admin = $this->admin();
        Keyword::create(['term' => 'intelligence artificielle', 'is_active' => true]);

        $site = SourceSite::create([
            'name' => 'Exemple Actu',
            'base_url' => 'https://feed.example.com',
            'rss_feed_url' => 'https://feed.example.com/rss.xml',
            'is_active' => true,
            'used_for_watch' => true,
        ]);

        $this->fakeFeedAndArticle();

        Livewire::actingAs($admin)
            ->test(ListSourceSites::class)
            ->callTableAction('pollNow', $site);

        $this->assertSame(1, Article::count());
        $this->assertNotNull($site->fresh()->last_polled_at);
    }

    public function test_admin_can_trigger_a_global_poll_from_the_header_action(): void
    {
        $admin = $this->admin();
        Keyword::create(['term' => 'intelligence artificielle', 'is_active' => true]);

        SourceSite::create([
            'name' => 'Exemple Actu',
            'base_url' => 'https://feed.example.com',
            'rss_feed_url' => 'https://feed.example.com/rss.xml',
            'is_active' => true,
            'used_for_watch' => true,
        ]);

        $this->fakeFeedAndArticle();

        Livewire::actingAs($admin)
            ->test(ListSourceSites::class)
            ->callAction('pollAll');

        $this->assertSame(1, Article::count());
    }

    public function test_polling_without_an_active_keyword_warns_instead_of_silently_importing_nothing(): void
    {
        $admin = $this->admin();

        $site = SourceSite::create([
            'name' => 'Exemple Actu',
            'base_url' => 'https://feed.example.com',
            'rss_feed_url' => 'https://feed.example.com/rss.xml',
            'is_active' => true,
            'used_for_watch' => true,
        ]);

        $this->fakeFeedAndArticle();

        Livewire::actingAs($admin)
            ->test(ListSourceSites::class)
            ->callTableAction('pollNow', $site)
            ->assertNotified('Aucun mot-clé actif');

        $this->assertSame(0, Article::count());
    }

    public function test_feed_discovery_action_fills_the_rss_field(): void
    {
        $admin = $this->admin();

        $site = SourceSite::create([
            'name' => 'Exemple Actu',
            'base_url' => 'https://feed.example.com',
            'is_active' => true,
            'used_for_watch' => true,
        ]);

        Http::fake([
            'feed.example.com*' => Http::response(
                '<html><head><link rel="alternate" type="application/rss+xml" href="https://feed.example.com/rss.xml"></head></html>',
                200,
            ),
        ]);

        Livewire::actingAs($admin)
            ->test(EditSourceSite::class, ['record' => $site->getRouteKey()])
            ->callFormComponentAction('rss_feed_url', 'discoverFeed')
            ->assertFormSet(['rss_feed_url' => 'https://feed.example.com/rss.xml']);
    }
}
