<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportPolicy
{
    /**
     * Determina si el usuario puede ver reportes de ventas
     */
    public function viewSalesAnalytics(User $user)
    {
        return $user->hasPermissionTo('reports sales');
    }

    /**
     * Determina si el usuario puede ver reportes de compras
     */
    public function viewPurchasesAnalytics(User $user)
    {
        return $user->hasPermissionTo('reports shoppings');
    }

    /**
     * Determina si el usuario puede ver reportes de consultas
     */
    public function viewConsultationsAnalytics(User $user)
    {
        return $user->hasPermissionTo('reports consultations');
    }

    /**
     * Determina si el usuario puede ver el dashboard
     */
    public function viewDashboard(User $user)
    {
        return $user->hasPermissionTo('view dashboard');
    }

    /**
     * Determina si el usuario puede ver productos con stock bajo
     */
    public function viewLowStock(User $user)
    {
        return $user->hasPermissionTo('view products');
    }
}
