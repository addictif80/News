<article class="article-fallback">
    @if($article->is_imported && $article->sourceSite)
        @include('site.partials.source-badge', ['article' => $article])
    @endif
    <h1>{{ $article->title }}</h1>
    @if($article->featured_image_url)
        <img src="{{ $article->featured_image_url }}" alt="{{ $article->title }}">
    @endif
    <p class="article-meta">
        {{ $article->author?->name }} — {{ optional($article->published_at)->translatedFormat('d F Y') }}
    </p>
    <div class="article-content">{!! $article->content !!}</div>
</article>
