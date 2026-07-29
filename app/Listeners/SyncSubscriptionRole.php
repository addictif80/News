<?php

namespace App\Listeners;

use App\Models\User;
use Laravel\Cashier\Events\WebhookHandled;

class SyncSubscriptionRole
{
    private const RELEVANT_EVENTS = [
        'customer.subscription.created',
        'customer.subscription.updated',
        'customer.subscription.deleted',
    ];

    public function handle(WebhookHandled $event): void
    {
        $type = $event->payload['type'] ?? null;

        if (! in_array($type, self::RELEVANT_EVENTS, true)) {
            return;
        }

        $stripeCustomerId = $event->payload['data']['object']['customer'] ?? null;

        if (! $stripeCustomerId) {
            return;
        }

        $user = User::where('stripe_id', $stripeCustomerId)->first();

        if (! $user) {
            return;
        }

        if ($user->subscribed('default')) {
            $user->syncRoles(array_diff($user->getRoleNames()->all(), ['gratuit', 'abonne']));
            $user->assignRole('abonne');
        } else {
            $user->syncRoles(array_diff($user->getRoleNames()->all(), ['gratuit', 'abonne']));
            $user->assignRole('gratuit');
        }
    }
}
