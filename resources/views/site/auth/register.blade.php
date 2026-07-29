<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Inscription</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf
        <h1>Inscription</h1>
        @if ($errors->any())
            <div class="auth-form__errors">{{ $errors->first() }}</div>
        @endif
        <label>Nom <input type="text" name="name" value="{{ old('name') }}" required></label>
        <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
        <label>Mot de passe <input type="password" name="password" required></label>
        <label>Confirmation <input type="password" name="password_confirmation" required></label>
        <button type="submit">Créer mon compte</button>
        <a href="{{ route('login') }}">J'ai déjà un compte</a>
    </form>
</body>
</html>
