<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Configuration;

class ConfigurationPolicy
{
    /**
     * Listar configuraciones
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any configurations');
    }

    /**
     * Ver una configuración
     */
    public function view(User $user, Configuration $configuration)
    {
        return $user->hasPermissionTo('view configurations');
    }

    /**
     * Crear configuraciones
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create configurations');
    }

    /**
     * Actualizar configuraciones
     */
    public function update(User $user, Configuration $configuration)
    {
        return $user->hasPermissionTo('update configurations');
    }

    /**
     * Eliminar configuraciones
     */
    public function delete(User $user, Configuration $configuration)
    {
        return $user->hasPermissionTo('delete configurations');
    }

    public function restore(User $user, Configuration $configuration)
    {
        return false;
    }

    public function forceDelete(User $user, Configuration $configuration)
    {
        return false;
    }
}
