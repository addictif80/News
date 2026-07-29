<?php

namespace App\Services;

use App\Models\AlertBanner;
use App\Models\Article;
use App\Models\Page;
use App\Models\Template;
use DOMDocument;
use DOMXPath;

class TemplateRenderer
{
    public function renderArticle(Article $article): string
    {
        $template = $article->template ?? $this->defaultTemplate('article');

        if (! $template || blank($template->html)) {
            return view('site.fallback.article', ['article' => $article])->render();
        }

        $replacements = [
            '{{title}}' => e($article->title),
            '{{featured_image}}' => $article->featured_image_url ?? '',
            '{{content}}' => $article->content ?? '',
            '{{date}}' => optional($article->published_at)->translatedFormat('d F Y') ?? '',
            '{{author}}' => $article->author?->name ?? '',
        ];

        $html = strtr($template->html ?? '', $replacements);

        if ($article->is_imported && $article->sourceSite) {
            $badge = view('site.partials.source-badge', ['article' => $article])->render();
            $html = $badge.$html;
        }

        return $this->wrapWithStyle($html, $template->css);
    }

    public function renderPage(Page $page): string
    {
        $template = $page->template ?? $this->defaultTemplate('page');

        if (! $template || blank($template->html)) {
            return view('site.fallback.page', ['page' => $page])->render();
        }

        $replacements = [
            '{{title}}' => e($page->title),
            '{{featured_image}}' => $page->featured_image ? asset('storage/'.$page->featured_image) : '',
            '{{content}}' => $page->content ?? '',
            '{{date}}' => optional($page->published_at)->translatedFormat('d F Y') ?? '',
        ];

        $html = strtr($template->html ?? '', $replacements);

        return $this->wrapWithStyle($html, $template->css);
    }

    public function renderHomepage(): string
    {
        $template = $this->defaultTemplate('homepage');

        if (! $template || blank($template->html)) {
            return view('site.fallback.homepage')->render();
        }

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8">'.$template->html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        foreach (iterator_to_array($dom->childNodes) as $node) {
            if ($node->nodeType === XML_PI_NODE) {
                $dom->removeChild($node);
            }
        }

        $xpath = new DOMXPath($dom);

        foreach ($xpath->query('//*[@data-block]') as $node) {
            $blockType = $node->getAttribute('data-block');

            $fragmentHtml = match ($blockType) {
                'article-card-grid' => $this->renderArticleCardGrid(
                    $node->getAttribute('data-category'),
                    (int) ($node->getAttribute('data-columns') ?: 3),
                    (int) ($node->getAttribute('data-rows') ?: 2),
                ),
                'alert-banner' => $this->renderAlertBanner(),
                'newsletter-signup' => view('site.blocks.newsletter-signup')->render(),
                'subscription-cta' => view('site.blocks.subscription-cta')->render(),
                'auth-widget' => view('site.blocks.auth-widget')->render(),
                default => null,
            };

            if ($fragmentHtml === null) {
                continue;
            }

            $fragment = $dom->createDocumentFragment();
            @$fragment->appendXML($this->toXmlSafe($fragmentHtml));
            $node->parentNode->replaceChild($fragment, $node);
        }

        $html = html_entity_decode($dom->saveHTML(), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return $this->wrapWithStyle($html, $template->css);
    }

    private function renderArticleCardGrid(?string $categorySlug, int $columns, int $rows): string
    {
        $query = Article::query()->published()->latest('published_at');

        if (filled($categorySlug)) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        $articles = $query->limit(max(1, $columns * $rows))->get();

        return view('site.blocks.article-card-grid', [
            'articles' => $articles,
            'columns' => $columns,
        ])->render();
    }

    private function renderAlertBanner(): string
    {
        $banner = AlertBanner::currentlyActive()->latest()->first();

        if (! $banner) {
            return '';
        }

        return view('site.blocks.alert-banner', ['banner' => $banner])->render();
    }

    private function defaultTemplate(string $type): ?Template
    {
        return Template::query()->where('type', $type)->where('is_default', true)->first()
            ?? Template::query()->where('type', $type)->first();
    }

    private function wrapWithStyle(string $html, ?string $css): string
    {
        if (blank($css)) {
            return $html;
        }

        return '<style>'.$css.'</style>'.$html;
    }

    private function toXmlSafe(string $html): string
    {
        return '<div>'.$html.'</div>';
    }
}
