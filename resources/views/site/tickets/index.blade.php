<x-account-shell title="Mes tickets de support">
    <div class="account-actions" style="margin-bottom: 1.5rem;">
        <a href="{{ route('tickets.create') }}" class="btn">Nouveau ticket</a>
    </div>

    @if($tickets->isEmpty())
        <p>Vous n'avez pas encore ouvert de ticket.</p>
    @else
        <div class="ticket-list">
            @foreach($tickets as $ticket)
                <a href="{{ route('tickets.show', $ticket) }}" class="ticket-list__item">
                    <span class="ticket-list__subject">{{ $ticket->subject }}</span>
                    <span class="ticket-status ticket-status--{{ $ticket->status }}">
                        {{ match($ticket->status) {
                            'open' => 'Ouvert',
                            'pending' => 'En attente',
                            'closed' => 'Fermé',
                        } }}
                    </span>
                </a>
            @endforeach
        </div>
    @endif
</x-account-shell>
