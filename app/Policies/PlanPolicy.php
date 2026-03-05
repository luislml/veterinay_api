<?php

namespace App\Policies;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlanPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any plans');
    }

    public function view(User $user, Plan $plan)
    {
        return $user->hasPermissionTo('view plans');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create plans');
    }

    public function update(User $user, Plan $plan)
    {
        return $user->hasPermissionTo('update plans');
    }

    public function delete(User $user, Plan $plan)
    {
        return $user->hasPermissionTo('delete plans');
    }

    public function restore(User $user, Plan $plan): bool
    {
        return false;
    }

    public function forceDelete(User $user, Plan $plan): bool
    {
        return false;
    }
}
