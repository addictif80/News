<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PopupModal;
use Illuminate\Auth\Access\HandlesAuthorization;

class PopupModalPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PopupModal');
    }

    public function view(AuthUser $authUser, PopupModal $popupModal): bool
    {
        return $authUser->can('View:PopupModal');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PopupModal');
    }

    public function update(AuthUser $authUser, PopupModal $popupModal): bool
    {
        return $authUser->can('Update:PopupModal');
    }

    public function delete(AuthUser $authUser, PopupModal $popupModal): bool
    {
        return $authUser->can('Delete:PopupModal');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PopupModal');
    }

    public function restore(AuthUser $authUser, PopupModal $popupModal): bool
    {
        return $authUser->can('Restore:PopupModal');
    }

    public function forceDelete(AuthUser $authUser, PopupModal $popupModal): bool
    {
        return $authUser->can('ForceDelete:PopupModal');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PopupModal');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PopupModal');
    }

    public function replicate(AuthUser $authUser, PopupModal $popupModal): bool
    {
        return $authUser->can('Replicate:PopupModal');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PopupModal');
    }

}