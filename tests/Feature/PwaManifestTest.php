<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaManifestTest extends TestCase
{
    use RefreshDatabase;

    public function test_manifest_is_served_as_valid_json(): void
    {
        $response = $this->get('/manifest.json');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/manifest+json');
        $response->assertJsonStructure(['name', 'short_name', 'start_url', 'display', 'icons']);
    }

    public function test_service_worker_file_exists(): void
    {
        // Static files aren't routed through Laravel's test kernel (that's the
        // webserver's job), so we just assert the file itself is in place.
        $this->assertFileExists(public_path('sw.js'));
    }
}
