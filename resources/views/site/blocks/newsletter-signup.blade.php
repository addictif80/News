<form action="{{ route('newsletter.subscribe') }}" method="POST" class="newsletter-signup">
    @csrf
    <input type="email" name="email" placeholder="Votre email" required>
    <button type="submit">S'inscrire à la newsletter</button>
</form>
