<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * The moderateur role gets full access to editorial/content resources,
     * but not to users, roles, or the settings pages (SMTP/Stripe stay admin-only).
     */
    private array $moderatorResources = [
        'Article', 'Page', 'Category', 'Tag', 'Author', 'Comment',
        'AlertBanner', 'PopupModal', 'NewsletterSubscriber', 'NewsletterCampaign',
        'Widget', 'SourceSite', 'Keyword', 'SupportCategory', 'SupportTicket',
        'ImportLogEntry',
    ];

    public function run(): void
    {
        $moderateur = Role::firstOrCreate(['name' => 'moderateur', 'guard_name' => 'web']);

        $permissions = Permission::query()
            ->where(function ($query) {
                foreach ($this->moderatorResources as $resource) {
                    $query->orWhere('name', 'like', '%:'.$resource);
                }
            })
            ->pluck('name');

        $moderateur->syncPermissions($permissions);
    }
}
