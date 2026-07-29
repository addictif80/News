<div class="alert-banner alert-banner--{{ $banner->style }}">
    @if($banner->link_url)
        <a href="{{ $banner->link_url }}">{{ $banner->message }}</a>
    @else
        {{ $banner->message }}
    @endif
</div>
