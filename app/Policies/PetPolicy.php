<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Pet;

class PetPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any pets');
    }

    public function view(User $user, Pet $pet)
    {
        return $user->hasPermissionTo('view pets');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create pets');
    }

    public function update(User $user, Pet $pet)
    {
        return $user->hasPermissionTo('update pets');
    }

    public function delete(User $user, Pet $pet)
    {
        return $user->hasPermissionTo('delete pets');
    }
    public function restore(User $user, Pet $pet)
    {
        return $user->hasPermissionTo('restore pets');
    }
}
