<?php

namespace App\Policies;

use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AdvertisementPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any advertisements');
    }

    /**
     * Determina si el usuario puede ver un anuncio específico.
     */
    public function view(User $user, Advertisement $advertisement)
    {
        return $user->hasPermissionTo('view advertisements');
    }

    /**
     * Determina si el usuario puede crear anuncios.
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create advertisements');
    }

    /**
     * Determina si el usuario puede actualizar un anuncio.
     */
    public function update(User $user, Advertisement $advertisement)
    {
        return $user->hasPermissionTo('update advertisements');
    }

    /**
     * Determina si el usuario puede eliminar un anuncio.
     */
    public function delete(User $user, Advertisement $advertisement)
    {
        return $user->hasPermissionTo('delete advertisements');
    }

    /**
     * Determina si el usuario puede descargar PDFs.
     */
    public function download(User $user, Advertisement $advertisement)
    {
        return $user->hasPermissionTo('download advertisements');
    }
}
