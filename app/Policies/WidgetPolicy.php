<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Widget;
use Illuminate\Auth\Access\HandlesAuthorization;

class WidgetPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Widget');
    }

    public function view(AuthUser $authUser, Widget $widget): bool
    {
        return $authUser->can('View:Widget');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Widget');
    }

    public function update(AuthUser $authUser, Widget $widget): bool
    {
        return $authUser->can('Update:Widget');
    }

    public function delete(AuthUser $authUser, Widget $widget): bool
    {
        return $authUser->can('Delete:Widget');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Widget');
    }

    public function restore(AuthUser $authUser, Widget $widget): bool
    {
        return $authUser->can('Restore:Widget');
    }

    public function forceDelete(AuthUser $authUser, Widget $widget): bool
    {
        return $authUser->can('ForceDelete:Widget');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Widget');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Widget');
    }

    public function replicate(AuthUser $authUser, Widget $widget): bool
    {
        return $authUser->can('Replicate:Widget');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Widget');
    }

}