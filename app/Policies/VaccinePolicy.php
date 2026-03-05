<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vaccine;

class VaccinePolicy
{
    /**
     * Ver listado de vacunas
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any vaccines');
    }

    /**
     * Ver una vacuna
     */
    public function view(User $user, Vaccine $vaccine)
    {
        return $user->hasPermissionTo('view vaccines');
    }

    /**
     * Crear vacuna
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create vaccines');
    }

    /**
     * Actualizar vacuna
     */
    public function update(User $user, Vaccine $vaccine)
    {
        return $user->hasPermissionTo('update vaccines');
    }

    /**
     * Eliminar vacuna
     */
    public function delete(User $user, Vaccine $vaccine)
    {
        return $user->hasPermissionTo('delete vaccines');
    }

    public function restore(User $user, Vaccine $vaccine)
    {
        return false;
    }

    public function forceDelete(User $user, Vaccine $vaccine)
    {
        return false;
    }
}
