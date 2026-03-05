<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Schedule;

class SchedulePolicy
{
    /**
     * Listar horarios
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any schedules');
    }

    /**
     * Ver un horario
     */
    public function view(User $user, Schedule $schedule)
    {
        return $user->hasPermissionTo('view schedules');
    }

    /**
     * Crear horarios
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create schedules');
    }

    /**
     * Actualizar horarios
     */
    public function update(User $user, Schedule $schedule)
    {
        return $user->hasPermissionTo('update schedules');
    }

    /**
     * Eliminar horarios
     */
    public function delete(User $user, Schedule $schedule)
    {
        return $user->hasPermissionTo('delete schedules');
    }

    public function restore(User $user, Schedule $schedule)
    {
        return false;
    }

    public function forceDelete(User $user, Schedule $schedule)
    {
        return false;
    }
}
