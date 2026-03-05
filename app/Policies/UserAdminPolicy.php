<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserAdminPolicy
{
    /**
     * Solo el admin puede ver el listado de usuarios
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any users');
    }

    /**
     * Solo el admin puede ver un usuario
     */
    public function view(User $user, User $model)
    {
        return $user->hasPermissionTo('view users');
    }

    /**
     * Solo el admin puede crear usuarios
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create users');
    }

    /**
     * Solo el admin puede actualizar usuarios
     */
    public function update(User $user, User $model)
    {
        return $user->hasPermissionTo('update users');
    }

    /**
     * Solo el admin puede eliminar usuarios
     */
    public function delete(User $user, User $model)
    {
        return $user->hasPermissionTo('delete users');
    }

    public function restore(User $user, User $model)
    {
        return $user->hasPermissionTo('restore users');
    }

    public function forceDelete(User $user, User $model)
    {
        return false;
    }
}
