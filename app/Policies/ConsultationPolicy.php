<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Consultation;

class ConsultationPolicy
{
    /**
     * Ver listado de consultas
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any consultations');
    }

    /**
     * Ver una consulta
     */
    public function view(User $user, Consultation $consultation)
    {
        return $user->hasPermissionTo('view consultations');
    }

    /**
     * Crear consulta
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create consultations');
    }

    /**
     * Actualizar consulta
     */
    public function update(User $user, Consultation $consultation)
    {
        return $user->hasPermissionTo('update consultations');
    }

    /**
     * Eliminar consulta
     */
    public function delete(User $user, Consultation $consultation)
    {
        return $user->hasPermissionTo('delete consultations');
    }

    public function restore(User $user, Consultation $consultation)
    {
        return false;
    }

    public function forceDelete(User $user, Consultation $consultation)
    {
        return false;
    }
}
