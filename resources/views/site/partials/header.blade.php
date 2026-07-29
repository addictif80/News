@php
    $generalSettings = app(\App\Settings\GeneralSettings::class);
    $navCategories = \App\Models\Category::orderBy('position')->limit(8)->get();
@endphp

<div class="site-header__meta">
    <div class="site-header__meta-bar">
        <span>{{ now()->translatedFormat('l d F Y') }}</span>
        <span>Édition en ligne</span>
    </div>
</div>

<header class="site-header">
    <div class="site-header__bar">
        <a href="{{ route('home') }}" class="site-header__brand">
            @if($generalSettings->site_logo)
                <img src="{{ asset('storage/'.$generalSettings->site_logo) }}" alt="{{ $generalSettings->site_name }}">
            @else
                {{ $generalSettings->site_name }}
            @endif
        </a>

        <div class="site-header__auth">
            @include('site.blocks.auth-widget')
        </div>
    </div>

    @if($navCategories->isNotEmpty())
        <nav class="site-nav">
            <ul>
                @foreach($navCategories as $category)
                    <li>{{ $category->name }}</li>
                @endforeach
            </ul>
        </nav>
    @endif
</header>
