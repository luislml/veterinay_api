<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Requests\ClientRequest;
use DB;
use Gate;
use App\Http\Resources\clients\ClientCollection;
use App\Http\Resources\clients\ClientResource;
use App\Traits\FiltersByVeterinary;

class ClientController extends Controller
{
    use FiltersByVeterinary;
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Client::class);

        $query = Client::with(['pets.race.typePet', 'veterinaries']);

        // Aplicar filtro por veterinaria según el usuario logueado (trait)
        $query = $this->filterByUserVeterinary($query, $request);

        // Filtro de veterinaria directo si lo envían como parámetro
        if ($request->has('veterinary_id')) {
            $query->whereHas('veterinaries', function ($q) use ($request) {
                $q->where('veterinary_id', $request->veterinary_id);
            });
        }

        // 🔎 BÚSQUEDA GENERAL (nombre, apellido, teléfono, ci, mascotas)
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('ci', 'LIKE', "%{$search}%")

                    // Buscar también por mascotas
                    ->orWhereHas('pets', function ($pet) use ($search) {
                        $pet->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('code', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Paginación opcional
        $usePagination = filter_var($request->get('paginate', true), FILTER_VALIDATE_BOOLEAN);
        $clients = $usePagination ? $query->latest()->paginate(10) : $query->latest()->get();

        return new ClientCollection($clients);
    }


    public function store(ClientRequest $request)
    {
        Gate::authorize('create', Client::class);
        DB::beginTransaction();
        try {
            // Crear cliente
            $client = Client::create($request->validated());

            // Validar veterinarias extra (opcional)
            $veterinaryIds = $request->input('veterinary_id', []);
            if (!empty($veterinaryIds)) {
                $existingVeterinaries = \App\Models\Veterinary::whereIn('id', $veterinaryIds)->pluck('id')->toArray();
                $invalidIds = array_diff($veterinaryIds, $existingVeterinaries);
                if (!empty($invalidIds)) {
                    return response()->json([
                        'error' => 'Las siguientes veterinarias no existen: ' . implode(', ', $invalidIds)
                    ], 422);
                }

                $client->veterinaries()->syncWithoutDetaching($existingVeterinaries);
            }

            DB::commit();

            return response()->json([
                'message' => 'Cliente creado y asignado correctamente',
                'data' => $client->load('veterinaries')
            ], 201);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function show(Client $client)
    {
        Gate::authorize('view', $client);

        $client->load([
            'pets.race.typePet',
            'veterinaries',
        ]);

        return new ClientResource($client);
    }

    public function update(ClientRequest $request, Client $client)
    {
        Gate::authorize('update', $client);
        DB::beginTransaction();
        try {
            // 1. Actualizar los datos del cliente
            $client->update($request->except('veterinary_id'));

            // 2. Asignar veterinaria si llega en el request
            $veterinaryId = $request->input('veterinary_id');

            if ($veterinaryId) {
                $client->veterinaries()->sync($veterinaryId);
            }

            DB::commit();

            return response()->json([
                'message' => 'Cliente actualizado correctamente',
                'data' => $client->load('veterinaries')
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function destroy(Client $client)
    {
        Gate::authorize('delete', $client);
        $client->delete();
        return response()->json(null, 204);
    }
    public function restore($id)
    {
        $client = Client::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $client);

        $client->restore();

        return response()->json(['message' => 'Cliente restaurado correctamente.']);
    }

}
