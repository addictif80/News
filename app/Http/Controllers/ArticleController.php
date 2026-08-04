<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\TemplateRenderer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function show(Article $article, TemplateRenderer $renderer, Request $request): View
    {
        $isStaff = auth()->user()?->hasAnyRole(['admin', 'moderateur']) ?? false;

        abort_unless($article->status === 'published' || $isStaff, 404);

        $previewAs = null;

        if ($isStaff) {
            $requested = $request->query('preview_as');
            $previewAs = in_array($requested, ['guest', 'free', 'subscriber'], true) ? $requested : null;
        }

        if ($previewAs === null) {
            $article->increment('views_count');
        }

        return view('site.layouts.app', [
            'content' => $renderer->renderArticle($article, $previewAs),
            'seoTitle' => $article->seo_title ?: $article->title,
            'seoDescription' => $article->seo_description ?: $article->excerpt,
            'canonicalUrl' => $article->canonical_url,
            'ogImage' => $article->seo_og_image ? asset('storage/'.$article->seo_og_image) : null,
        ]);
    }
}
