<div class="news-widget news-widget--{{ $widget->theme }}">
    @foreach($articles as $article)
        <a href="{{ route('articles.show', $article->slug) }}" target="_blank" rel="noopener" class="news-widget__item">
            @if($article->featured_image_url)
                <img src="{{ $article->featured_image_url }}" alt="{{ $article->title }}">
            @endif
            <span>{{ $article->title }}</span>
        </a>
    @endforeach
</div>
