<div class="paywall">
    <p class="paywall__title">
        @if($article->access_level === 'subscribers')
            Cet article est réservé à nos abonnés.
        @else
            Cet article est réservé aux membres inscrits.
        @endif
    </p>
    <p class="paywall__text">Créez un compte gratuit ou abonnez-vous pour lire la suite de cet article.</p>
    <div class="paywall__actions">
        @guest
            <a href="{{ route('register') }}" class="btn btn--primary">S'inscrire gratuitement</a>
        @endguest
        @if($article->access_level === 'subscribers')
            <a href="{{ route('subscription.checkout') }}" class="btn btn--primary">Voir les abonnements</a>
        @endif
        @guest
            <a href="{{ route('login') }}" class="btn btn--muted">Se connecter</a>
        @endguest
    </div>
</div>
