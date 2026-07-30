<?php

namespace App\Http\Controllers;

use App\Services\TemplateRenderer;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(TemplateRenderer $renderer): View
    {
        return view('site.layouts.app', [
            'content' => $renderer->renderHomepage(),
            'seoTitle' => config('app.name'),
            'showGlobalBanner' => ! $renderer->homepageHandlesOwnAlertBanner(),
        ]);
    }
}
