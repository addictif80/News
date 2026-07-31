@props(['title'])
<!DOCTYPE html>
<html lang="fr">
<head>
    @include('site.partials.head-meta')
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/push-notifications.js'])
</head>
<body class="site-body">
    @include('site.partials.header')

    <main class="site-main account-page">
        <h1 class="section-heading">{{ $title }}</h1>

        @if(session('status'))
            <div class="account-status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="account-form__errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}
    </main>

    @include('site.partials.footer')
    @include('site.partials.active-popup')
</body>
</html>
