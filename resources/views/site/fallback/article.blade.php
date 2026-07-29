<article class="article-fallback">
    @if($article->is_imported && $article->sourceSite)
        @include('site.partials.source-badge', ['article' => $article])
    @endif
    <h1 class="article-title">{{ $article->title }}</h1>
    @if($article->featured_image_url)
        <img class="article-featured-image" src="{{ $article->featured_image_url }}" alt="{{ $article->title }}">
    @endif
    <p>
        @if($article->author)
            <span class="article-author">{{ $article->author->name }}</span>
        @endif
        <span class="article-date">{{ optional($article->published_at)->translatedFormat('d F Y') }}</span>
    </p>
    <div class="article-content">{!! $article->content !!}</div>
</article>
