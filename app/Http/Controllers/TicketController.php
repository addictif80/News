<?php

namespace App\Http\Controllers;

use App\Models\SupportCategory;
use App\Models\SupportTicket;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = SupportTicket::where('user_id', $request->user()->id)
            ->latest('last_activity_at')
            ->get();

        return view('site.tickets.index', ['tickets' => $tickets]);
    }

    public function create(): View
    {
        return view('site.tickets.create', [
            'categories' => SupportCategory::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'support_category_id' => ['nullable', 'exists:support_categories,id'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $request->user()->id,
            'support_category_id' => $data['support_category_id'] ?? null,
            'subject' => $data['subject'],
            'status' => 'open',
            'last_activity_at' => now(),
        ]);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $data['body'],
            'is_staff_reply' => false,
        ]);

        return redirect()->route('tickets.show', $ticket)->with('status', 'Ticket créé.');
    }

    public function show(Request $request, SupportTicket $ticket): View
    {
        $this->authorize('view', $ticket);

        return view('site.tickets.show', [
            'ticket' => $ticket->load('messages.user', 'category'),
        ]);
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $this->authorize('reply', $ticket);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $isStaff = $request->user()->hasRole('moderateur') && $request->user()->id !== $ticket->user_id;

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $data['body'],
            'is_staff_reply' => $isStaff,
        ]);

        $ticket->update([
            'last_activity_at' => now(),
            'status' => $isStaff ? 'pending' : 'open',
        ]);

        return back()->with('status', 'Message envoyé.');
    }
}
