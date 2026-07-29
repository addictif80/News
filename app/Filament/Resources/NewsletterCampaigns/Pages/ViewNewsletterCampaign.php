<?php

namespace App\Filament\Resources\NewsletterCampaigns\Pages;

use App\Filament\Resources\NewsletterCampaigns\NewsletterCampaignResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewNewsletterCampaign extends ViewRecord
{
    protected static string $resource = NewsletterCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
