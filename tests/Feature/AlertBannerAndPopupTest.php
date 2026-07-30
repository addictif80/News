<?php

namespace Tests\Feature;

use App\Models\AlertBanner;
use App\Models\PopupModal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertBannerAndPopupTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_alert_banner_shows_on_the_homepage_by_default(): void
    {
        AlertBanner::create([
            'message' => 'Alerte test',
            'is_active' => true,
            'style' => 'danger',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Alerte test');
    }

    public function test_active_popup_renders_and_its_script_builds_correctly(): void
    {
        PopupModal::create([
            'name' => 'Popup test',
            'content' => '<p>Contenu popup</p>',
            'trigger_type' => 'load',
            'display_frequency_days' => 1,
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Contenu popup', false);
        $response->assertSee('site-popup', false);
    }
}
