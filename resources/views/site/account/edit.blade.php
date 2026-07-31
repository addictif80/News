<!DOCTYPE html>
<html lang="fr">
<head>
    @include('site.partials.head-meta')
    <title>Mon compte</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/push-notifications.js'])
</head>
<body class="site-body">
    @include('site.partials.header')

    <main class="site-main account-page">
        <h1 class="section-heading">Mon compte</h1>

        @if(session('status'))
            <div class="account-status">{{ session('status') }}</div>
        @endif

        <section class="account-card">
            <h2>Profil</h2>
            <form method="POST" action="{{ route('account.update') }}" enctype="multipart/form-data" class="account-form">
                @csrf
                @method('PUT')

                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="account-avatar">
                @endif

                <label>Photo de profil <input type="file" name="avatar" accept="image/*"></label>
                <label>Nom <input type="text" name="name" value="{{ old('name', $user->name) }}" required></label>
                <label>Email <input type="email" name="email" value="{{ old('email', $user->email) }}" required></label>

                @if ($errors->any())
                    <div class="account-form__errors">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button type="submit">Enregistrer</button>
            </form>
        </section>

        <section class="account-card">
            <h2>Abonnement</h2>
            @if($user->subscribed('default'))
                <p>Vous êtes actuellement <strong>abonné</strong>.</p>
                <div class="account-actions">
                    <a href="{{ route('subscription.portal') }}" class="btn">Gérer le paiement</a>
                    <form method="POST" action="{{ route('subscription.cancel') }}">
                        @csrf
                        <button type="submit" class="btn btn--muted" onclick="return confirm('Résilier votre abonnement ?');">Résilier mon abonnement</button>
                    </form>
                </div>
            @else
                <p>Vous êtes actuellement en offre <strong>gratuite</strong>.</p>
                <a href="{{ route('subscription.checkout') }}" class="btn">Passer à l'offre payante</a>
            @endif
        </section>

        <section class="account-card">
            <h2>Notifications push</h2>
            <p>Recevez une notification directement sur cet appareil lors de la publication de certains contenus.</p>
            <button
                type="button"
                class="btn"
                data-push-toggle
                data-vapid-key="{{ $vapidPublicKey }}"
                data-subscribe-url="{{ route('push.subscribe') }}"
                data-unsubscribe-url="{{ route('push.unsubscribe') }}"
                data-csrf="{{ csrf_token() }}"
            >Activer les notifications push</button>
        </section>

        <section class="account-card">
            <h2>Centres d'intérêt</h2>
            <form method="POST" action="{{ route('account.interests') }}" class="account-form">
                @csrf
                <div class="account-checkboxes">
                    @foreach($categories as $category)
                        <label class="account-checkbox">
                            <input
                                type="checkbox"
                                name="category_ids[]"
                                value="{{ $category->id }}"
                                @checked(in_array($category->id, $favoriteCategoryIds))
                            >
                            {{ $category->name }}
                        </label>
                    @endforeach
                </div>
                <button type="submit">Enregistrer mes préférences</button>
            </form>
        </section>

        @if(Route::has('tickets.index'))
            <section class="account-card">
                <h2>Support</h2>
                <p><a href="{{ route('tickets.index') }}">Consulter mes tickets de support</a></p>
            </section>
        @endif

        <section class="account-card">
            <h2>Mes données</h2>
            <a href="{{ route('account.export') }}" class="btn btn--muted">Télécharger mes données</a>
        </section>

        <section class="account-card account-card--danger">
            <h2>Supprimer mon compte</h2>
            <p>Cette action est irréversible. Vos données personnelles seront effacées ; vos commentaires resteront visibles, attribués à « Utilisateur supprimé ».</p>
            <form method="POST" action="{{ route('account.destroy') }}" class="account-form" onsubmit="return confirm('Supprimer définitivement votre compte ?');">
                @csrf
                @method('DELETE')
                <label>Confirmez avec votre mot de passe <input type="password" name="password" required></label>
                <button type="submit" class="btn btn--danger">Supprimer mon compte</button>
            </form>
        </section>
    </main>

    @include('site.partials.footer')
    @include('site.partials.active-popup')
</body>
</html>
