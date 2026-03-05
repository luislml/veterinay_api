<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Veterinary;
use Illuminate\Http\Request;
use App\Http\Resources\veterinaries\VeterinaryResource;

class VeterinaryPublicController extends Controller
{
    /**
     * Display the specified veterinary by slug with all public configuration data.
     */
    public function show($slug)
    {
        $veterinary = Veterinary::where('slug', $slug)
            ->with([
                'configuration.favicon', // Cargar configuración y su favicon
                'contentVeterinaries.files', // Cargar contenidos y sus imágenes
                'imagesVeterinaries.files', // Cargar imágenes de equipo/logo/testimonio
                'addresses',
                'schedules',
                // 'plan' // Si es necesario mostrar info del plan
            ])
            ->firstOrFail();

        return new VeterinaryResource($veterinary);
    }
}
