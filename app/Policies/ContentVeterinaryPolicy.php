<?php

namespace App\Policies;

use App\Models\ContentVeterinary;
use App\Models\User;

class ContentVeterinaryPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any content-veterinaries');
    }

    public function view(User $user, ContentVeterinary $contentVeterinary)
    {
        return $user->hasPermissionTo('view content-veterinaries');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create content-veterinaries');
    }

    public function update(User $user, ContentVeterinary $contentVeterinary)
    {
        return $user->hasPermissionTo('update content-veterinaries');
    }

    public function delete(User $user, ContentVeterinary $contentVeterinary)
    {
        return $user->hasPermissionTo('delete content-veterinaries');
    }

    public function restore(User $user, ContentVeterinary $contentVeterinary)
    {
        return false;
    }

    public function forceDelete(User $user, ContentVeterinary $contentVeterinary)
    {
        return false;
    }
}
