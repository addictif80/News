<?php

namespace Tests\Feature;

use App\Exceptions\DuplicateImportException;
use App\Models\Article;
use App\Models\ImportLogEntry;
use App\Services\ArticleImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ArticleImportServiceTest extends TestCase
{
    use RefreshDatabase;

    private function fakeHtml(string $title = 'Un grand titre'): string
    {
        return <<<HTML
            <html>
            <head>
                <title>Fallback title</title>
                <meta property="og:title" content="{$title}" />
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
    }

    public function test_it_imports_an_article_from_a_url(): void
    {
        Http::fake(['example.com/*' => Http::response($this->fakeHtml(), 200)]);

        $article = (new ArticleImportService)->importFromUrl('https://example.com/articles/1');

        $this->assertSame('Un grand titre', $article->title);
        $this->assertSame('Un résumé accrocheur', $article->excerpt);
        $this->assertSame('https://example.com/image.jpg', $article->featured_image);
        $this->assertTrue($article->is_imported);
        $this->assertSame('pending_review', $article->status);
        $this->assertTrue($article->requires_admin_validation);
        $this->assertSame('Example News', $article->sourceSite->name);
        $this->assertStringContainsString('Premier paragraphe', $article->content);

        $this->assertDatabaseHas('import_log_entries', [
            'url' => 'https://example.com/articles/1',
            'outcome' => 'imported',
            'article_id' => $article->id,
        ]);
    }

    public function test_importing_the_same_url_twice_throws_and_logs_a_duplicate(): void
    {
        Http::fake(['example.com/*' => Http::response($this->fakeHtml(), 200)]);

        $importer = new ArticleImportService;
        $first = $importer->importFromUrl('https://example.com/articles/1');

        $this->expectException(DuplicateImportException::class);

        try {
            $importer->importFromUrl('https://example.com/articles/1');
        } finally {
            $this->assertDatabaseHas('import_log_entries', [
                'url' => 'https://example.com/articles/1',
                'outcome' => 'duplicate',
                'article_id' => $first->id,
            ]);
        }
    }

    public function test_importing_a_near_identical_title_from_a_different_url_is_flagged_as_a_duplicate(): void
    {
        Article::create([
            'title' => 'Un grand titre',
            'slug' => 'un-grand-titre',
            'content' => '<p>Contenu existant</p>',
            'status' => 'published',
        ]);

        Http::fake(['example.com/*' => Http::response($this->fakeHtml('Un grand titre'), 200)]);

        $this->expectException(DuplicateImportException::class);

        (new ArticleImportService)->importFromUrl('https://example.com/articles/2');
    }

    public function test_import_failure_is_logged(): void
    {
        Http::fake(['example.com/*' => Http::response('', 500)]);

        try {
            (new ArticleImportService)->importFromUrl('https://example.com/broken');
        } catch (\Throwable) {
            // expected
        }

        $this->assertSame('error', ImportLogEntry::first()->outcome);
    }
}
