<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Race;

class RacePolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any races');
    }

    public function view(User $user, Race $race)
    {
        return $user->hasPermissionTo('view races');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create races');
    }

    public function update(User $user, Race $race)
    {
        return $user->hasPermissionTo('update races');
    }

    public function delete(User $user, Race $race)
    {
        return $user->hasPermissionTo('delete races');
    }

    public function restore(User $user, Race $race)
    {
        return false;
    }

    public function forceDelete(User $user, Race $race)
    {
        return false;
    }
}
