<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Traits\HasFiles;
use Illuminate\Http\Request;
use App\Http\Requests\PetRequest;
use App\Http\Resources\pets\PetResource;
use App\Http\Resources\pets\PetCollection;
use App\Models\Race;
use App\Models\Client;
use DB;
use Gate;
use App\Traits\FiltersByVeterinary;

class PetController extends Controller
{
    use FiltersByVeterinary;
    use HasFiles;
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Pet::class);
        $user = $request->user();

        // === 1) Si el usuario es admin → ve TODO ===
        if ($user->hasRole('admin')) {
            $query = Pet::with(['race', 'client', 'vaccines', 'consultations', 'images']);
        } else {
            // === 2) Obtener veterinarias asignadas al usuario ===
            $userVetIds = $user->veterinaries()->pluck('veterinary_id');

            // Si no tiene veterinarias → lista vacía
            if ($userVetIds->isEmpty()) {
                return new PetCollection(collect([]));
            }

            // === 3) Filtrar mascotas solo de esas veterinarias ===
            $query = Pet::with(['race.typePet', 'client', 'vaccines', 'consultations', 'images'])
                ->whereHas('client.veterinaries', function ($q) use ($userVetIds) {
                    $q->whereIn('veterinary_id', $userVetIds);
                });
        }

        // === 4) Filtro directo por veterinaria_id recibido en el request ===
        if ($request->filled('veterinary_id')) {
            $query->whereHas('client.veterinaries', function ($q) use ($request) {
                $q->where('veterinary_id', $request->veterinary_id);
            });
        }

        // === 5) Filtro por client_id recibido en el request ===
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // === 6) Paginación ===
        $pets = $query->latest()->paginate(10);

        return new PetCollection($pets);
    }
    public function store(PetRequest $request)
    {
        Gate::authorize('create', Pet::class);
        DB::beginTransaction();

        try {
            // 1. Validación extra
            $race = Race::find($request->race_id);
            if (!$race) {
                return response()->json(['error' => 'La raza seleccionada no existe.'], 422);
            }

            $client = Client::find($request->client_id);
            if (!$client) {
                return response()->json(['error' => 'El propietario seleccionado no existe.'], 422);
            }

            // 2. Crear la mascota
            $pet = Pet::create($request->validated());

            // 3. Subir imagen opcional
            if ($request->hasFile('image')) {
                // Utiliza tu trait HasFiles
                $pet->addFile($request->file('image'));
            }

            DB::commit();

            return new PetResource(
                $pet->load('race', 'client', 'vaccines', 'consultations', 'images')
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

    public function show(Pet $pet)
    {
        Gate::authorize('view', $pet);
        return new PetResource($pet->load('race.typePet', 'client', 'vaccines', 'consultations', 'images'));
    }

    public function update(PetRequest $request, Pet $pet)
    {
        Gate::authorize('update', $pet);
        DB::beginTransaction();

        try {
            // 1️⃣ Tomar todos los datos excepto archivos
            $data = $request->except('image');

            // 2️⃣ Actualizar solo si hay datos
            if (!empty($data)) {
                $pet->update($data);
            }

            // 3️⃣ Manejar imagen opcional
            if ($request->hasFile('image')) {
                // Si quieres reemplazar la imagen anterior, primero puedes borrar la anterior
                // $pet->deleteFiles(); // opcional, depende de tu trait HasFiles
                $pet->addFile($request->file('image'));
            }

            DB::commit();

            // 4️⃣ Retornar recurso con relaciones cargadas
            return new PetResource(
                $pet->load('race', 'client', 'vaccines', 'consultations', 'images')
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
    public function destroy(Pet $pet)
    {
        Gate::authorize('delete', $pet);
        $pet->delete();
        return response()->json(null, 204);
    }
    public function restore($id)
    {
        $pet = Pet::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $pet);

        $pet->restore();

        return response()->json(['message' => 'Mascota restaurada correctamente.']);
    }
}
