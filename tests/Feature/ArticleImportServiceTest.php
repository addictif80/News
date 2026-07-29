<?php

namespace Tests\Feature;

use App\Services\ArticleImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ArticleImportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_an_article_from_a_url(): void
    {
        $html = <<<'HTML'
            <html>
            <head>
                <title>Fallback title</title>
                <meta property="og:title" content="Un grand titre" />
                <meta property="og:description" content="Un résumé accrocheur" />
                <meta property="og:image" content="https://example.com/image.jpg" />
                <meta property="og:site_name" content="Example News" />
            </head>
            <body>
                <article>
                    <p>Premier paragraphe suffisamment long pour être retenu comme contenu principal.</p>
                    <p>Deuxième paragraphe également assez long pour passer le filtre de longueur.</p>
                </article>
            </body>
            </html>
        HTML;

        Http::fake([
            'example.com/*' => Http::response($html, 200),
        ]);

        $article = (new ArticleImportService)->importFromUrl('https://example.com/articles/1');

        $this->assertSame('Un grand titre', $article->title);
        $this->assertSame('Un résumé accrocheur', $article->excerpt);
        $this->assertSame('https://example.com/image.jpg', $article->featured_image);
        $this->assertTrue($article->is_imported);
        $this->assertSame('pending_review', $article->status);
        $this->assertTrue($article->requires_admin_validation);
        $this->assertSame('Example News', $article->sourceSite->name);
        $this->assertStringContainsString('Premier paragraphe', $article->content);
    }
}
