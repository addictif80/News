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
        $isLocked = ! $article->isAccessibleBy(auth()->user());

        if (! $template || blank($template->html)) {
            return view('site.fallback.article', ['article' => $article, 'isLocked' => $isLocked])->render();
        }

        $replacements = [
            '{{title}}' => e($article->title),
            '{{featured_image}}' => $article->featured_image_url ?? '',
            '{{content}}' => $this->resolveArticleContent($article, $isLocked),
            '{{date}}' => optional($article->published_at)->translatedFormat('d F Y') ?? '',
            '{{author}}' => $article->author?->name ?? '',
        ];

        $html = $this->stripUnresolvedPlaceholders(strtr($template->html ?? '', $replacements));

        if ($article->is_imported && $article->sourceSite) {
            $badge = view('site.partials.source-badge', ['article' => $article])->render();
            $html = $badge.$html;
        }

        return $this->wrapWithStyle($html, $template->css);
    }

    private function resolveArticleContent(Article $article, bool $isLocked): string
    {
        if (! $isLocked) {
            return $article->content ?? '';
        }

        return '<p>'.e($article->previewContent()).'</p>'
            .view('site.partials.paywall', ['article' => $article])->render();
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

        $html = $this->stripUnresolvedPlaceholders(strtr($template->html ?? '', $replacements));

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

            $this->replaceNodeWithHtml($dom, $node, $fragmentHtml);
        }

        $html = html_entity_decode($dom->saveHTML(), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return $this->wrapWithStyle($html, $template->css);
    }

    /**
     * Whether the active homepage template already places the alert-banner
     * block itself, so callers can avoid rendering the sitewide banner twice.
     */
    public function homepageHandlesOwnAlertBanner(): bool
    {
        $template = $this->defaultTemplate('homepage');

        return $template
            && filled($template->html)
            && str_contains($template->html, 'data-block="alert-banner"');
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

    /**
     * A template built for the wrong content type (e.g. an article template
     * assigned to a page) can reference a placeholder we never substitute
     * (e.g. {{author}} on a Page). Rather than leaking the raw token to
     * visitors, drop any of our known placeholders left unresolved.
     */
    private function stripUnresolvedPlaceholders(string $html): string
    {
        return str_replace(
            ['{{title}}', '{{featured_image}}', '{{content}}', '{{date}}', '{{author}}'],
            '',
            $html,
        );
    }

    /**
     * Insert an arbitrary (Blade-rendered) HTML fragment in place of $node.
     *
     * DOMDocument::appendXML() requires strictly well-formed XML, which Blade
     * output (unescaped ampersands, boolean attributes, etc.) does not
     * guarantee. Parsing the fragment through loadHTML() is far more lenient
     * and mirrors how a browser would interpret the same markup.
     */
    private function replaceNodeWithHtml(DOMDocument $dom, \DOMNode $node, string $html): void
    {
        $fragmentDom = new DOMDocument;
        libxml_use_internal_errors(true);
        $fragmentDom->loadHTML('<?xml encoding="utf-8"><div id="__fragment_root__">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $root = (new DOMXPath($fragmentDom))->query('//*[@id="__fragment_root__"]')->item(0);

        if (! $root) {
            return;
        }

        $parent = $node->parentNode;

        foreach (iterator_to_array($root->childNodes) as $child) {
            $parent->insertBefore($dom->importNode($child, true), $node);
        }

        $parent->removeChild($node);
    }
}
