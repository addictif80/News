<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\NewsletterSubscriber;
use App\Models\SupportTicket;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $articlesThisWeek = Article::query()
            ->where('status', 'published')
            ->where('published_at', '>=', now()->startOfWeek())
            ->count();

        $openTickets = SupportTicket::query()
            ->whereIn('status', ['open', 'pending'])
            ->count();

        $activeSubscribers = User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', 'abonne'))
            ->count();

        $newsletterSubscribers = NewsletterSubscriber::query()
            ->where('is_confirmed', true)
            ->whereNull('unsubscribed_at')
            ->count();

        return [
            Stat::make('Articles publiés cette semaine', $articlesThisWeek),
            Stat::make('Tickets support ouverts', $openTickets)
                ->color($openTickets > 0 ? 'warning' : 'success'),
            Stat::make('Abonnés payants actifs', $activeSubscribers),
            Stat::make('Inscrits newsletter', $newsletterSubscribers),
        ];
    }
}
