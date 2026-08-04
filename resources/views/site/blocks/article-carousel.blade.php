@if($articles->isNotEmpty())
    <div class="article-carousel">
        <button type="button" class="article-carousel__nav article-carousel__nav--prev" aria-label="Précédent">&larr;</button>

        <div class="article-carousel__track">
            @foreach($articles as $article)
                <a href="{{ route('articles.show', $article->slug) }}" class="article-carousel__card">
                    @if($article->featured_image_url)
                        <img src="{{ $article->featured_image_url }}" alt="{{ $article->title }}" loading="lazy">
                    @endif
                    <div class="article-carousel__body">
                        @if($article->category)
                            <span class="article-card__eyebrow">{{ $article->category->name }}</span>
                        @endif
                        <h3>{{ $article->title }}</h3>
                        <span class="article-card__meta">{{ optional($article->published_at)->translatedFormat('d F Y') }}</span>
                    </div>
                </a>
            @endforeach
        </div>

        <button type="button" class="article-carousel__nav article-carousel__nav--next" aria-label="Suivant">&rarr;</button>
    </div>

    @once
        @vite(['resources/js/carousel.js'])
    @endonce
@endif
