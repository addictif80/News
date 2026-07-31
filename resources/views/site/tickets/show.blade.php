<x-account-shell :title="$ticket->subject">
    <p class="ticket-status ticket-status--{{ $ticket->status }}">
        {{ match($ticket->status) {
            'open' => 'Ouvert',
            'pending' => 'En attente',
            'closed' => 'Fermé',
        } }}
    </p>

    <div class="ticket-thread">
        @foreach($ticket->messages as $message)
            <div class="ticket-message {{ $message->is_staff_reply ? 'ticket-message--staff' : '' }}">
                <div class="ticket-message__meta">
                    <strong>{{ $message->is_staff_reply ? 'Support' : $message->user->name }}</strong>
                    <span>{{ $message->created_at->translatedFormat('d F Y H:i') }}</span>
                </div>
                <p>{{ $message->body }}</p>
            </div>
        @endforeach
    </div>

    @if($ticket->status !== 'closed')
        <section class="account-card">
            <form method="POST" action="{{ route('tickets.reply', $ticket) }}" class="account-form">
                @csrf
                <label>Répondre <textarea name="body" rows="4" required></textarea></label>
                <button type="submit">Envoyer</button>
            </form>
        </section>
    @else
        <p>Ce ticket est fermé.</p>
    @endif
</x-account-shell>
