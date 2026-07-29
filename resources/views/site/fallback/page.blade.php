<article class="page-fallback">
    <h1 class="page-title">{{ $page->title }}</h1>
    @if($page->featured_image)
        <img class="page-featured-image" src="{{ asset('storage/'.$page->featured_image) }}" alt="{{ $page->title }}">
    @endif
    <div class="page-content">{!! $page->content !!}</div>
</article>
