<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\addresses\AddressResource;
use App\Http\Resources\addresses\AddressCollection;
use DB;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Address::class);
        $query = Address::with('veterinary');

        // Filtro opcional por veterinaria
        if ($request->has('veterinary_id')) {
            $query->where('veterinary_id', $request->veterinary_id);
        }
        $addresses = $query->latest()->get();
        return new AddressCollection($addresses);
    }

    public function store(AddressRequest $request)
    {
        Gate::authorize('create', Address::class);
        DB::beginTransaction();
        try {
            $address = Address::create($request->validated());

            // Guardar imagen si existe
            if ($request->hasFile('image')) {
                $address->addFile($request->file('image'));
            }

            DB::commit();
            return new AddressResource($address->load('veterinary'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function show(Address $address)
    {
        Gate::authorize('view', $address);
        $address->load('images'); // carga las fotos
        return new AddressResource($address);
        // return response()->json($address->load('veterinary'));
    }

    public function update(AddressRequest $request, Address $address)
    {
        Gate::authorize('update', $address);
        DB::beginTransaction();
        try {
            $address->update($request->validated());

            // Guardar imagen si existe (esto agregará una nueva imagen, no reemplaza la anterior por defecto en HasFiles a menos que se maneje así)
            // Asumiendo comportamiento similar a ProductController que usa addFile
            if ($request->hasFile('image')) {
                $address->addFile($request->file('image'));
            }

            DB::commit();
            // Devolver recurso con la relación cargada
            return new AddressResource($address->load('veterinary'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function destroy(Address $address)
    {
        Gate::authorize('delete', $address);
        $address->delete();
        return response()->json(null, 204);
    }
}
