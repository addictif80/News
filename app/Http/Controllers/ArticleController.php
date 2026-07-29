<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\TemplateRenderer;
use Illuminate\Contracts\View\View;

class ArticleController extends Controller
{
    public function show(Article $article, TemplateRenderer $renderer): View
    {
        abort_unless($article->status === 'published' || auth()->user()?->hasAnyRole(['admin', 'moderateur']), 404);

        $article->increment('views_count');

        return view('site.layouts.app', [
            'content' => $renderer->renderArticle($article),
            'seoTitle' => $article->seo_title ?: $article->title,
            'seoDescription' => $article->seo_description ?: $article->excerpt,
            'canonicalUrl' => $article->canonical_url,
            'ogImage' => $article->seo_og_image ? asset('storage/'.$article->seo_og_image) : null,
        ]);
    }
}
