<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SalePolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any sales');
    }

    /**
     * Determina si el usuario puede ver una venta específica
     */
    public function view(User $user, Sale $sale)
    {
        return $user->hasPermissionTo('view sales');
    }

    /**
     * Determina si el usuario puede crear ventas
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create sales');
    }

    /**
     * Determina si el usuario puede actualizar una venta
     */
    public function update(User $user, Sale $sale)
    {
        return $user->hasPermissionTo('update sales');
    }

    /**
     * Determina si el usuario puede eliminar una venta
     */
    public function delete(User $user, Sale $sale)
    {
        return $user->hasPermissionTo('delete sales');
    }
}
