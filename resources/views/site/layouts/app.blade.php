<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    @vite(['resources/css/app.css'])
</head>
<body>
    @if($showGlobalBanner ?? true)
        @include('site.partials.active-banner')
    @endif

    <main>
        {!! $content !!}
    </main>

    @include('site.partials.active-popup')
</body>
</html>
