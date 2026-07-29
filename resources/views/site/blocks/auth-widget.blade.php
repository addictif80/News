<div class="auth-widget">
    @auth
        <span>Bonjour, {{ auth()->user()->name }}</span>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Déconnexion</button>
        </form>
    @else
        <a href="{{ route('login') }}">Connexion</a>
        <a href="{{ route('register') }}">Inscription</a>
    @endauth
</div>
