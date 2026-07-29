<div class="subscription-cta">
    @auth
        @if(auth()->user()->subscribed('default'))
            <p>Vous êtes abonné. Merci de votre soutien !</p>
        @else
            <a href="{{ route('subscription.checkout') }}" class="btn btn--primary">S'abonner</a>
        @endif
    @else
        <a href="{{ route('login') }}" class="btn btn--primary">Se connecter pour s'abonner</a>
    @endauth
</div>
