<?php

namespace App\Services;

use App\Exceptions\DuplicateImportException;
use App\Models\Article;
use App\Models\ImportLogEntry;
use App\Models\SourceSite;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ArticleImportService
{
    /**
     * @throws DuplicateImportException when an article for this URL (or a
     *                                  near-identical title) already exists — the duplicate is logged
     *                                  before the exception is thrown.
     */
    public function importFromUrl(string $url, ?string $keyword = null): Article
    {
        try {
            if ($existing = Article::where('source_url', $url)->first()) {
                $this->log($url, $keyword, 'duplicate', "URL déjà importée (article #{$existing->id})", $existing);

                throw new DuplicateImportException($existing, "Cette URL a déjà été importée : « {$existing->title} ».");
            }

            $response = Http::timeout(15)->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; NewsImportBot/1.0)',
            ])->get($url);

            if ($response->failed()) {
                throw new RuntimeException("Impossible de récupérer l'URL : {$response->status()}");
            }

            $extracted = $this->extract($response->body(), $url);

            $titleSlug = Str::slug($extracted['title']);
            if ($titleSlug !== '' && ($existing = Article::where('slug', $titleSlug)->orWhere('slug', 'like', "{$titleSlug}-%")->first())) {
                $this->log($url, $keyword, 'duplicate', "Titre très proche d'un article existant : « {$existing->title} » (#{$existing->id})", $existing);

                throw new DuplicateImportException($existing, "Un article au titre très proche existe déjà : « {$existing->title} ».");
            }

            $sourceSite = $this->resolveSourceSite($url, $extracted['site_name']);

            $article = Article::create([
                'title' => $extracted['title'],
                'excerpt' => $extracted['excerpt'],
                'content' => $extracted['content'],
                'featured_image' => $extracted['image'],
                'status' => 'pending_review',
                'is_imported' => true,
                'source_site_id' => $sourceSite->id,
                'source_url' => $url,
                'requires_admin_validation' => true,
            ]);

            $this->log($url, $keyword, 'imported', null, $article, $sourceSite);

            return $article;
        } catch (DuplicateImportException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->log($url, $keyword, 'error', $e->getMessage());

            throw $e;
        }
    }

    private function log(
        string $url,
        ?string $keyword,
        string $outcome,
        ?string $message,
        ?Article $article = null,
        ?SourceSite $sourceSite = null,
    ): void {
        ImportLogEntry::create([
            'source_site_id' => $sourceSite?->id ?? $article?->source_site_id,
            'article_id' => $article?->id,
            'keyword' => $keyword,
            'url' => $url,
            'outcome' => $outcome,
            'message' => $message,
        ]);
    }

    /**
     * @return array{title: string, excerpt: ?string, content: ?string, image: ?string, site_name: ?string}
     */
    private function extract(string $html, string $url): array
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        $meta = fn (string $property) => $this->metaContent($xpath, $property);

        $title = $meta('og:title') ?: $this->tagText($xpath, '//title') ?: 'Article importé';
        $excerpt = $meta('og:description') ?: $meta('description');
        $image = $meta('og:image');
        $siteName = $meta('og:site_name') ?: parse_url($url, PHP_URL_HOST);

        $content = $this->extractMainContent($xpath);

        return [
            'title' => trim($title),
            'excerpt' => $excerpt ? trim($excerpt) : null,
            'content' => $content,
            'image' => $image ?: null,
            'site_name' => $siteName,
        ];
    }

    private function metaContent(DOMXPath $xpath, string $property): ?string
    {
        $nodes = $xpath->query("//meta[@property='{$property}']/@content");

        if ($nodes->length === 0) {
            $nodes = $xpath->query("//meta[@name='{$property}']/@content");
        }

        return $nodes->length > 0 ? $nodes->item(0)->nodeValue : null;
    }

    private function tagText(DOMXPath $xpath, string $query): ?string
    {
        $nodes = $xpath->query($query);

        return $nodes->length > 0 ? $nodes->item(0)->textContent : null;
    }

    private function extractMainContent(DOMXPath $xpath): ?string
    {
        $articleNodes = $xpath->query('//article');

        $container = $articleNodes->length > 0 ? $articleNodes->item(0) : null;

        if (! $container) {
            $paragraphs = $xpath->query('//p');
            $text = collect(iterator_to_array($paragraphs))
                ->map(fn ($node) => trim($node->textContent))
                ->filter(fn ($text) => Str::length($text) > 40)
                ->map(fn ($text) => '<p>'.e($text).'</p>')
                ->implode("\n");

            return $text ?: null;
        }

        $html = '';
        foreach ($container->childNodes as $child) {
            $html .= $container->ownerDocument->saveHTML($child);
        }

        return $html ?: null;
    }

    private function resolveSourceSite(string $url, ?string $siteName): SourceSite
    {
        $host = parse_url($url, PHP_URL_HOST) ?? $siteName ?? 'source-inconnue';
        $baseUrl = parse_url($url, PHP_URL_SCHEME).'://'.$host;

        return SourceSite::firstOrCreate(
            ['base_url' => $baseUrl],
            ['name' => $siteName ?: $host, 'is_active' => true]
        );
    }
}
