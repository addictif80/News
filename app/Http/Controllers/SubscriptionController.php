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
                'cancel_url' => route('account.edit'),
            ]);

        return redirect($checkout->url);
    }

    public function portal(Request $request): RedirectResponse
    {
        return $request->user()->redirectToBillingPortal(route('account.edit'));
    }

    public function cancel(Request $request): RedirectResponse
    {
        $subscription = $request->user()->subscription('default');

        if ($subscription && ! $subscription->canceled()) {
            $subscription->cancel();
        }

        return redirect()->route('account.edit')
            ->with('status', "Abonnement résilié — il reste actif jusqu'à la fin de la période déjà payée.");
    }

    public function success(): RedirectResponse
    {
        return redirect()->route('account.edit')->with('status', 'Abonnement activé, merci !');
    }
}
