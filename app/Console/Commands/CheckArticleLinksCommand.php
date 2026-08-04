<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\ArticleLinkCheck;
use DOMDocument;
use DOMXPath;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Throwable;

#[Signature('articles:check-links')]
#[Description('Scan published articles for external links/images and flag broken ones')]
class CheckArticleLinksCommand extends Command
{
    public function handle(): int
    {
        $articles = Article::query()->published()->whereNotNull('content')->get();

        $checked = 0;
        $broken = 0;

        foreach ($articles as $article) {
            foreach ($this->extractUrls($article->content) as [$url, $type]) {
                $result = $this->checkUrl($url);
                $checked++;
                $broken += $result['broken'] ? 1 : 0;

                ArticleLinkCheck::updateOrCreate(
                    ['article_id' => $article->id, 'url' => $url],
                    [
                        'type' => $type,
                        'status_code' => $result['status'],
                        'is_broken' => $result['broken'],
                        'checked_at' => now(),
                    ],
                );
            }
        }

        $this->info("Vérifiés : {$checked} — Cassés : {$broken}");

        return self::SUCCESS;
    }

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    private function extractUrls(string $html): array
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $urls = [];

        foreach ($xpath->query('//a[@href]') as $node) {
            $href = $node->getAttribute('href');
            if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
                $urls[] = [$href, 'link'];
            }
        }

        foreach ($xpath->query('//img[@src]') as $node) {
            $src = $node->getAttribute('src');
            if (str_starts_with($src, 'http://') || str_starts_with($src, 'https://')) {
                $urls[] = [$src, 'image'];
            }
        }

        return $urls;
    }

    /**
     * @return array{status: ?int, broken: bool}
     */
    private function checkUrl(string $url): array
    {
        $headers = ['User-Agent' => 'Mozilla/5.0 (compatible; LinkCheckerBot/1.0)'];

        try {
            $response = Http::timeout(10)->withHeaders($headers)->head($url);

            // Some servers reject HEAD outright (405) rather than answering it;
            // fall back to a GET before concluding the link is actually dead.
            if ($response->status() === 405 || $response->failed()) {
                $response = Http::timeout(10)->withHeaders($headers)->get($url);
            }

            return ['status' => $response->status(), 'broken' => $response->failed()];
        } catch (Throwable) {
            return ['status' => null, 'broken' => true];
        }
    }
}
