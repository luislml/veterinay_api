<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;
use App\Http\Requests\ConfigurationRequest;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\configurations\ConfigurationResource;
use App\Http\Resources\configurations\ConfigurationCollection;
use DB;
use Illuminate\Support\Facades\Storage;

class ConfigurationController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Configuration::class);
        $query = Configuration::with('veterinary');

        // Filtro opcional por veterinaria
        if ($request->has('veterinary_id')) {
            $query->where('veterinary_id', $request->veterinary_id);
        }

        $configurations = $query->latest()->get();

        return new ConfigurationCollection($configurations);
    }

    public function store(ConfigurationRequest $request)
    {
        Gate::authorize('create', Configuration::class);
        DB::beginTransaction();
        try {
            // 1️⃣ Crear configuración
            $configuration = Configuration::create($request->validated());

            // 2️⃣ Refrescar modelo para asegurar id y relaciones
            $configuration = Configuration::find($configuration->id);

            // 3️⃣ Subir favicon si viene
            if ($request->hasFile('favicon')) {
                // Utiliza tu trait HasFiles
                $configuration->addFile($request->file('favicon'));
            }

            DB::commit();

            return new ConfigurationResource($configuration->load('veterinary', 'favicon'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function show(Configuration $configuration)
    {
        //Gate::authorize('view', $configuration);
        return new ConfigurationResource($configuration->load('veterinary'));
        //return response()->json($configuration->load('veterinary'));
    }

    public function update(ConfigurationRequest $request, Configuration $configuration)
    {
        Gate::authorize('update', $configuration);
        DB::beginTransaction();
        try {
            // 1️⃣ Actualizar datos
            $configuration->update($request->validated());

            // 2️⃣ Reemplazar favicon si se envía
            if ($request->hasFile('favicon')) {
                // Eliminar favicons existentes (todos, por si hay inconsistencia)
                $existingImages = $configuration->favicon()->get();
                foreach ($existingImages as $existingImage) {
                    $path = 'files/' . $existingImage->name . '.' . $existingImage->extension;
                    if ($existingImage->name !== 'default' && Storage::exists($path)) {
                        Storage::delete($path);
                    }
                    $existingImage->delete();
                }

                // Agregar nuevo favicon
                $configuration->addFile($request->file('favicon'));
            }

            DB::commit();

            return new ConfigurationResource(
                $configuration->load('veterinary', 'favicon')
            );

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function destroy(Configuration $configuration)
    {
        Gate::authorize('delete', $configuration);
        $configuration->delete();
        return response()->json(null, 204);
    }
}
