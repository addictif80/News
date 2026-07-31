<!DOCTYPE html>
<html lang="fr">
<head>
    @include('site.partials.head-meta')
    <title>{{ $seoTitle ?? config('app.name') }}</title>
    @if(!empty($seoDescription))
        <meta name="description" content="{{ $seoDescription }}">
    @endif
    @if(!empty($canonicalUrl))
        <link rel="canonical" href="{{ $canonicalUrl }}">
    @endif
    @if(!empty($ogImage))
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site-body">
    @include('site.partials.header')

    @if($showGlobalBanner ?? true)
        @include('site.partials.active-banner')
    @endif

    <main class="site-main">
        {!! $content !!}
    </main>

    @include('site.partials.footer')
    @include('site.partials.active-popup')
</body>
</html>
