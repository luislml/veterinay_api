<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Address;

class AddressPolicy
{
    /**
     * Listar direcciones
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any addresses');
    }

    /**
     * Ver una dirección
     */
    public function view(User $user, Address $address)
    {
        return $user->hasPermissionTo('view addresses');
    }

    /**
     * Crear direcciones
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create addresses');
    }

    /**
     * Actualizar direcciones
     */
    public function update(User $user, Address $address)
    {
        return $user->hasPermissionTo('update addresses');
    }

    /**
     * Eliminar direcciones
     */
    public function delete(User $user, Address $address)
    {
        return $user->hasPermissionTo('delete addresses');
    }

    public function restore(User $user, Address $address)
    {
        return false;
    }

    public function forceDelete(User $user, Address $address)
    {
        return false;
    }
}
