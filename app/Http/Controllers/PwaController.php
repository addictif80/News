<?php

namespace App\Http\Controllers;

use App\Settings\GeneralSettings;
use Illuminate\Http\JsonResponse;

class PwaController extends Controller
{
    public function manifest(GeneralSettings $settings): JsonResponse
    {
        return response()->json([
            'name' => $settings->site_name,
            'short_name' => str($settings->site_name)->limit(12, ''),
            'start_url' => '/',
            'scope' => '/',
            'display' => 'standalone',
            'background_color' => '#f7f7f8',
            'theme_color' => '#1c1c2b',
            'lang' => 'fr',
            'icons' => [
                ['src' => asset('icons/icon-192.png'), 'sizes' => '192x192', 'type' => 'image/png'],
                ['src' => asset('icons/icon-512.png'), 'sizes' => '512x512', 'type' => 'image/png'],
                ['src' => asset('icons/icon-maskable-192.png'), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'maskable'],
                ['src' => asset('icons/icon-maskable-512.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
            ],
        ])->header('Content-Type', 'application/manifest+json');
    }
}
