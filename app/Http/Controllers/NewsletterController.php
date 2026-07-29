<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        NewsletterSubscriber::firstOrCreate(
            ['email' => $data['email']],
            ['user_id' => auth()->id(), 'subscribed_at' => now(), 'is_confirmed' => true]
        );

        return back()->with('status', 'Merci pour votre inscription à la newsletter !');
    }

    public function unsubscribe(string $token): RedirectResponse
    {
        NewsletterSubscriber::where('unsubscribe_token', $token)
            ->update(['unsubscribed_at' => now(), 'is_confirmed' => false]);

        return redirect('/')->with('status', 'Vous avez été désinscrit de la newsletter.');
    }
}
