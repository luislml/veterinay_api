<?php

namespace App\Policies;

use App\Models\Movement;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MovementPolicy
{

    /**
     * Determina si el usuario puede ver cualquier movimiento.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any movements');
    }

    /**
     * Determina si el usuario puede ver un movimiento específico.
     */
    public function view(User $user, Movement $movement)
    {
        return $user->hasPermissionTo('view movements');
    }

    /**
     * Determina si el usuario puede crear movimientos.
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create movements');
    }

    /**
     * Determina si el usuario puede actualizar movimientos.
     */
    public function update(User $user, Movement $movement)
    {
        return $user->hasPermissionTo('update movements');
    }

    /**
     * Determina si el usuario puede eliminar movimientos.
     */
    public function delete(User $user, Movement $movement)
    {
        return $user->hasPermissionTo('delete movements');
    }
}
