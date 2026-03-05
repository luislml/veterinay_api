<?php

namespace App\Policies;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PromotionPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any promotions');
    }

    public function view(User $user, Promotion $promotion)
    {
        return $user->hasPermissionTo('view promotions');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create promotions');
    }

    public function update(User $user, Promotion $promotion)
    {
        return $user->hasPermissionTo('update promotions');
    }

    public function delete(User $user, Promotion $promotion)
    {
        return $user->hasPermissionTo('delete promotions');
    }

    public function restore(User $user, Promotion $promotion)
    {
        return $user->hasPermissionTo('restore promotions');
    }
}
