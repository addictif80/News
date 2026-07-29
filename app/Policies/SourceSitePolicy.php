<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SourceSite;
use Illuminate\Auth\Access\HandlesAuthorization;

class SourceSitePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SourceSite');
    }

    public function view(AuthUser $authUser, SourceSite $sourceSite): bool
    {
        return $authUser->can('View:SourceSite');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SourceSite');
    }

    public function update(AuthUser $authUser, SourceSite $sourceSite): bool
    {
        return $authUser->can('Update:SourceSite');
    }

    public function delete(AuthUser $authUser, SourceSite $sourceSite): bool
    {
        return $authUser->can('Delete:SourceSite');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SourceSite');
    }

    public function restore(AuthUser $authUser, SourceSite $sourceSite): bool
    {
        return $authUser->can('Restore:SourceSite');
    }

    public function forceDelete(AuthUser $authUser, SourceSite $sourceSite): bool
    {
        return $authUser->can('ForceDelete:SourceSite');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SourceSite');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SourceSite');
    }

    public function replicate(AuthUser $authUser, SourceSite $sourceSite): bool
    {
        return $authUser->can('Replicate:SourceSite');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SourceSite');
    }

}