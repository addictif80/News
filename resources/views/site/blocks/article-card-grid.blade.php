<div class="article-card-grid" style="grid-template-columns:repeat({{ $columns }}, 1fr);">
    @foreach($articles as $article)
        <a href="{{ route('articles.show', $article->slug) }}" class="article-card">
            @if($article->featured_image_url)
                <img src="{{ $article->featured_image_url }}" alt="{{ $article->title }}">
            @endif
            <div class="article-card__body">
                @if($article->category)
                    <span class="article-card__eyebrow">{{ $article->category->name }}</span>
                @endif
                <h3>{{ $article->title }}</h3>
                <p>{{ $article->excerpt }}</p>
                <span class="article-card__meta">{{ optional($article->published_at)->translatedFormat('d F Y') }}</span>
                @if($article->is_imported && $article->sourceSite)
                    <span class="source-badge">depuis {{ $article->sourceSite->name }}</span>
                @endif
            </div>
        </a>
    @endforeach
</div>
