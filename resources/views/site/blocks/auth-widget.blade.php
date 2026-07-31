<div class="auth-widget">
    @auth
        <a href="{{ route('account.edit') }}">Bonjour, {{ auth()->user()->name }}</a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Déconnexion</button>
        </form>
    @else
        <a href="{{ route('login') }}">Connexion</a>
        <a href="{{ route('register') }}">Inscription</a>
    @endauth
</div>
