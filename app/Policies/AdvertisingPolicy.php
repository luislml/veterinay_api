<?php

namespace App\Policies;

use App\Models\Advertising;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AdvertisingPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any advertisings');
    }

    public function view(User $user, Advertising $advertising)
    {
        return $user->hasPermissionTo('view advertisings');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create advertisings');
    }

    public function update(User $user, Advertising $advertising)
    {
        return $user->hasPermissionTo('update advertisings');
    }

    public function delete(User $user, Advertising $advertising)
    {
        return $user->hasPermissionTo('delete advertisings');
    }
    
    public function restore(User $user, Advertising $advertising)
    {
        return $user->hasPermissionTo('restore advertisings');
    }
}
