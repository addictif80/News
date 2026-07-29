<div class="homepage-fallback">
    @include('site.blocks.article-card-grid', [
        'articles' => \App\Models\Article::published()->latest('published_at')->limit(9)->get(),
        'columns' => 3,
    ])
</div>
