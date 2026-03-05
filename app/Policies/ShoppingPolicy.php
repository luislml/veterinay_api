<?php

namespace App\Policies;

use App\Models\Shopping;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ShoppingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view any shoppings');
    }

    /**
     * Determine whether the user can view a shopping.
     */
    public function view(User $user, Shopping $shopping): bool
    {
        return $user->hasPermissionTo('view shoppings');
    }

    /**
     * Determine whether the user can create shoppings.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create shoppings');
    }

    /**
     * Determine whether the user can update the shopping.
     */
    public function update(User $user, Shopping $shopping): bool
    {
        return $user->hasPermissionTo('update shoppings');
    }

    /**
     * Determine whether the user can delete the shopping.
     */
    public function delete(User $user, Shopping $shopping): bool
    {
        return $user->hasPermissionTo('delete shoppings');
    }
}
