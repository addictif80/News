<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AlertBanner;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlertBannerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AlertBanner');
    }

    public function view(AuthUser $authUser, AlertBanner $alertBanner): bool
    {
        return $authUser->can('View:AlertBanner');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AlertBanner');
    }

    public function update(AuthUser $authUser, AlertBanner $alertBanner): bool
    {
        return $authUser->can('Update:AlertBanner');
    }

    public function delete(AuthUser $authUser, AlertBanner $alertBanner): bool
    {
        return $authUser->can('Delete:AlertBanner');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AlertBanner');
    }

    public function restore(AuthUser $authUser, AlertBanner $alertBanner): bool
    {
        return $authUser->can('Restore:AlertBanner');
    }

    public function forceDelete(AuthUser $authUser, AlertBanner $alertBanner): bool
    {
        return $authUser->can('ForceDelete:AlertBanner');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AlertBanner');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AlertBanner');
    }

    public function replicate(AuthUser $authUser, AlertBanner $alertBanner): bool
    {
        return $authUser->can('Replicate:AlertBanner');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AlertBanner');
    }

}