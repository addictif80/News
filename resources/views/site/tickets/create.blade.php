<x-account-shell title="Nouveau ticket">
    <section class="account-card">
        <form method="POST" action="{{ route('tickets.store') }}" class="account-form">
            @csrf
            <label>Catégorie
                <select name="support_category_id">
                    <option value="">— Aucune —</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Sujet <input type="text" name="subject" value="{{ old('subject') }}" required></label>
            <label>Message <textarea name="body" rows="6" required>{{ old('body') }}</textarea></label>
            <button type="submit">Envoyer</button>
        </form>
    </section>
</x-account-shell>
