<div class="article-card-grid" style="display:grid;grid-template-columns:repeat({{ $columns }}, 1fr);gap:1.5rem;">
    @foreach($articles as $article)
        <a href="{{ route('articles.show', $article->slug) }}" class="article-card">
            @if($article->featured_image)
                <img src="{{ asset('storage/'.$article->featured_image) }}" alt="{{ $article->title }}">
            @endif
            <h3>{{ $article->title }}</h3>
            <p>{{ $article->excerpt }}</p>
            @if($article->is_imported && $article->sourceSite)
                <span class="source-badge">depuis {{ $article->sourceSite->name }}</span>
            @endif
        </a>
    @endforeach
</div>
