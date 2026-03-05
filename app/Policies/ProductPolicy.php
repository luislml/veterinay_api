<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver cualquier producto
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any products');
    }

    /**
     * Determina si el usuario puede ver un producto específico
     */
    public function view(User $user, Product $product)
    {
        return $user->hasPermissionTo('view products');
    }

    /**
     * Determina si el usuario puede crear productos
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create products');
    }

    /**
     * Determina si el usuario puede actualizar un producto
     */
    public function update(User $user, Product $product)
    {
        return $user->hasPermissionTo('update products');
    }

    /**
     * Determina si el usuario puede eliminar un producto
     */
    public function delete(User $user, Product $product)
    {
        return $user->hasPermissionTo('delete products');
    }
}
