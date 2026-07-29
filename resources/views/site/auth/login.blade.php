<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Connexion</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf
        <h1>Connexion</h1>
        @if ($errors->any())
            <div class="auth-form__errors">{{ $errors->first() }}</div>
        @endif
        <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
        <label>Mot de passe <input type="password" name="password" required></label>
        <label><input type="checkbox" name="remember"> Se souvenir de moi</label>
        <button type="submit">Se connecter</button>
        <a href="{{ route('register') }}">Créer un compte</a>
    </form>
</body>
</html>
