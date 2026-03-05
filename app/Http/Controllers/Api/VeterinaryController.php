<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Veterinary;
use App\Models\Configuration;
use App\Models\ContentVeterinary;
use App\Models\ImageVeterinary;
use Illuminate\Http\Request;
use App\Http\Requests\VeterinaryRequest;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\veterinaries\VeterinaryResource;
use App\Http\Resources\veterinaries\VeterinaryCollection;
use DB;

class VeterinaryController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Veterinary::class);
        $query = Veterinary::with(['users', 'plan', 'addresses', 'configuration', 'schedules']);

        // 🔥 Paginación opcional
        $usePagination = filter_var($request->get('paginate', true), FILTER_VALIDATE_BOOLEAN);

        if ($usePagination) {
            $veterinaries = $query->latest()->paginate(10);
        } else {
            $veterinaries = $query->latest()->get();
        }

        return new VeterinaryCollection($veterinaries);
    }

    public function store(VeterinaryRequest $request)
    {
        Gate::authorize('create', Veterinary::class);
        DB::beginTransaction();

        try {
            // Validación ya se hizo en VeterinaryRequest
            $data = $request->validated();

            // Crear la veterinaria
            $veterinary = Veterinary::create($data);

            // Asociar al usuario si existe
            if ($request->has('user_id')) {
                $user = \App\Models\User::find($request->user_id);
                if (!$user) {
                    return response()->json([
                        'error' => 'El usuario seleccionado no existe.'
                    ], 422);
                }
                $veterinary->users()->attach($user->id);
            }

            // Crear configuración por defecto
            $configuration = Configuration::create([
                'veterinary_id' => $veterinary->id,
                'color_primary' => '#007bff', // Azul por defecto
                'color_secondary' => '#6c757d', // Gris por defecto
                'about_us' => 'Bienvenido a nuestra veterinaria.',
                'description_team' => 'Nuestro equipo de profesionales.',
                'phone' => '000000000',
                'phone_emergency' => '000000000',
            ]);
            // Insertar imagen por defecto para configuración (favicon o similar)
            $configuration->files()->create([
                'name' => 'favicon',
                'type' => 'image/jpeg',
                'extension' => 'jpg',
            ]);

            // Tipos de contenido (extraídos de ContentVeterinaryRequest)
            $contentTypes = ['banner', 'service', 'specialty', 'testimonial'];
            foreach ($contentTypes as $type) {
                $content = ContentVeterinary::create([
                    'veterinary_id' => $veterinary->id,
                    'title' => 'Título por defecto para ' . $type,
                    'description' => 'Descripción por defecto para ' . $type,
                    'type' => $type,
                ]);
                $content->files()->create([
                    'name' => $type, // banner, service, specialty, testimonial
                    'type' => 'image/jpeg',
                    'extension' => 'jpg',
                ]);
            }

            // Tipos de imagen (extraídos de ImageVeterinaryRequest)
            // 'team', 'logo', 'testimonial' - testimonial se repite en ambos arrays en el codigo original?
            // El usuario pidió: team.jpg, logo.jpg. testimonial ya está arriba pero si aquí es otro recurso se pone.
            // Asumo que testimonial aquí es ImageVeterinary, diferente de ContentVeterinary.
            $imageTypes = ['team', 'logo', 'testimonial'];
            foreach ($imageTypes as $type) {
                $image = ImageVeterinary::create([
                    'veterinary_id' => $veterinary->id,
                    'type' => $type,
                ]);

                // Si el tipo es testimonial, usaremos testimonial.jpg
                // Si es team, team.jpg
                // Si es logo, logo.jpg
                $image->files()->create([
                    'name' => $type,
                    'type' => 'image/jpeg',
                    'extension' => 'jpg',
                ]);
            }

            DB::commit();

            return new VeterinaryResource($veterinary->load('users', 'plan', 'addresses', 'configuration', 'schedules'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function show(Veterinary $veterinary)
    {
        Gate::authorize('view', $veterinary);
        $veterinary->load(['users', 'plan', 'addresses', 'configuration', 'schedules']);

        return new VeterinaryResource($veterinary);
        //return response()->json($veterinary->load(['plan', 'address', 'configuration', 'schedules']));
    }

    public function update(VeterinaryRequest $request, Veterinary $veterinary)
    {
        Gate::authorize('update', $veterinary);
        DB::beginTransaction();
        try {
            // Actualizar la veterinaria
            $veterinary->update($request->validated());

            // Actualizar la relación con usuarios si viene user_id
            if ($request->has('user_id')) {
                $veterinary->users()->sync($request->user_id);
            }

            DB::commit();

            // Devolver recurso con relaciones cargadas
            return new VeterinaryResource($veterinary->load('users', 'plan', 'address', 'configuration', 'schedules'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function destroy(Veterinary $veterinary)
    {
        Gate::authorize('delete', $veterinary);
        $veterinary->delete();
        return response()->json(null, 204);
    }

    public function addClients(Request $request, Veterinary $veterinary)
    {
        $data = $request->validate([
            'client_ids' => 'required|array',
            'client_ids.*' => 'exists:clients,id',
        ]);

        $veterinary->clients()->syncWithoutDetaching($data['client_ids']); // agrega sin borrar existentes

        return response()->json([
            'message' => 'Clientes asignados a la veterinaria',
            'data' => $veterinary->clients
        ]);
    }

    public function attachProduct(Request $request)
    {
        $data = $request->validate([
            'veterinary_id' => 'required|exists:veterinaries,id',
            'product_id' => 'required|exists:products,id',
        ]);

        // Evitar duplicados
        $exists = \DB::table('vet_product')
            ->where('veterinary_id', $data['veterinary_id'])
            ->where('product_id', $data['product_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Este producto ya está asociado a la veterinaria'
            ], 409);
        }

        // Insertar en la tabla pivote
        \DB::table('vet_product')->insert($data);

        return response()->json([
            'message' => 'Producto asociado a la veterinaria correctamente'
        ], 201);
    }
    public function restore($id)
    {
        $veterinary = Veterinary::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $veterinary);

        $veterinary->restore();

        return response()->json(['message' => 'Veterinaria restaurada correctamente.']);
    }


}
