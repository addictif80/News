<div class="news-widget news-widget--{{ $widget->theme }}">
    @foreach($articles as $article)
        <a href="{{ route('articles.show', $article->slug) }}" target="_blank" rel="noopener" class="news-widget__item">
            @if($article->featured_image)
                <img src="{{ asset('storage/'.$article->featured_image) }}" alt="{{ $article->title }}">
            @endif
            <span>{{ $article->title }}</span>
        </a>
    @endforeach
</div>
