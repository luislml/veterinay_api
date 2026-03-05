<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Client;

class ClientPolicy
{
    /**
     * Ver lista de clientes.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any clients');
    }

    /**
     * Ver un cliente específico.
     */
    public function view(User $user, Client $client)
    {
        return $user->hasPermissionTo('view clients');
    }

    /**
     * Crear clientes.
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create clients');
    }

    /**
     * Actualizar un cliente.
     */
    public function update(User $user, Client $client)
    {
        return $user->hasPermissionTo('update clients');
    }

    /**
     * Eliminar un cliente.
     */
    public function delete(User $user, Client $client)
    {
        return $user->hasPermissionTo('delete clients');
    }
    public function restore(User $user, Client $client)
    {
        return $user->hasPermissionTo('restore clients');
    }
}
