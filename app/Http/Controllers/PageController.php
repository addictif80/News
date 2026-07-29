<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\TemplateRenderer;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function show(Page $page, TemplateRenderer $renderer): View
    {
        abort_unless($page->status === 'published', 404);

        return view('site.layouts.app', [
            'content' => $renderer->renderPage($page),
            'seoTitle' => $page->seo_title ?: $page->title,
            'seoDescription' => $page->seo_description,
            'canonicalUrl' => $page->canonical_url,
            'ogImage' => $page->seo_og_image ? asset('storage/'.$page->seo_og_image) : null,
        ]);
    }
}
