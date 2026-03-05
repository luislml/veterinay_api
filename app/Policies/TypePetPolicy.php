<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TypePet;

class TypePetPolicy
{
     public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any type-pets');
    }

    public function view(User $user, TypePet $typePet)
    {
        return $user->hasPermissionTo('view type-pets');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create type-pets');
    }

    public function update(User $user, TypePet $typePet)
    {
        return $user->hasPermissionTo('update type-pets');
    }

    public function delete(User $user, TypePet $typePet)
    {
        return $user->hasPermissionTo('delete type-pets');
    }

    public function restore(User $user, TypePet $typePet)
    {
        return false;
    }

    public function forceDelete(User $user, TypePet $typePet)
    {
        return false;
    }
}
