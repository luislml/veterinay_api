<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait FiltersByVeterinary
{
    /**
     * Filtra un query según las veterinarias del usuario logueado.
     * - Admin ve todo
     * - Usuario con veterinarias ve solo sus clientes/mascotas
     * - Usuario sin veterinarias ve lista vacía
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Support\Collection
     */
    public function filterByUserVeterinary($query, Request $request)
    {
        $user = $request->user();

        // Admin ve todo
        if ($user->hasRole('admin')) {
            return $query;
        }

        // Usuario normal: obtener sus veterinarias
        $userVetIds = $user->veterinaries()->pluck('veterinary_id');

        // Si no tiene veterinarias, devolver colección vacía
        if ($userVetIds->isEmpty()) {
            // Retornamos un query que al ejecutar get() devuelve colección vacía
            return $query->whereRaw('0 = 1');
        }

        // Filtrar por veterinarias del usuario
        return $query->whereHas('veterinaries', function ($q) use ($userVetIds) {
            $q->whereIn('veterinary_id', $userVetIds);
        });
    }
}
