<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function checkout(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->subscribed('default')) {
            return redirect()->route('subscription.portal');
        }

        $checkout = $user->newSubscription('default', config('services.stripe.price_id'))
            ->checkout([
                'success_url' => route('subscription.success'),
                'cancel_url' => route('home'),
            ]);

        return redirect($checkout->url);
    }

    public function portal(Request $request): RedirectResponse
    {
        return $request->user()->redirectToBillingPortal(route('home'));
    }

    public function success(): RedirectResponse
    {
        return redirect()->route('home')->with('status', 'Abonnement activé, merci !');
    }
}
