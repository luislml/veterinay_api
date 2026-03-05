<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Veterinary;
use Illuminate\Auth\Access\Response;

class VeterinaryPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any veterinaries');
    }

    public function view(User $user, Veterinary $veterinary)
    {
        return $user->hasPermissionTo('view veterinaries');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create veterinaries');
    }

    public function update(User $user, Veterinary $veterinary)
    {
        return $user->hasPermissionTo('update veterinaries');
    }

    public function delete(User $user, Veterinary $veterinary)
    {
        return $user->hasPermissionTo('delete veterinaries');
    }

    public function restore(User $user, Veterinary $veterinary)
    {
        return $user->hasPermissionTo('restore veterinaries');
    }

    public function forceDelete(User $user, Veterinary $veterinary): bool
    {
        return false;
    }
}
