<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vaccine;
use Illuminate\Http\Request;
use App\Http\Requests\VaccineRequest;
use App\Http\Resources\vaccines\VaccineResource;
use App\Http\Resources\vaccines\VaccineCollection;
use DB;
use Gate;


class VaccineController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Vaccine::class);
        $vaccines = Vaccine::when($request->pet_id, function ($query, $petId) {
            return $query->where('pet_id', $petId);
        })->latest()->paginate(10);
        return new VaccineCollection($vaccines);
    }

    public function store(VaccineRequest $request)
    {
        Gate::authorize('create', Vaccine::class);
        DB::beginTransaction();
        try {
            // Crear vacuna
            $vaccine = Vaccine::create($request->validated());
            DB::commit();
            // Devolver recurso
            return new VaccineResource($vaccine);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function show(Vaccine $vaccine)
    {
        Gate::authorize('view', $vaccine);
        return new VaccineResource($vaccine);
    }

    public function update(VaccineRequest $request, Vaccine $vaccine)
    {
        Gate::authorize('update', $vaccine);
        DB::beginTransaction();
        try {
            // Actualizar vacuna
            $vaccine->update($request->validated());
            DB::commit();
            // Devolver recurso actualizado
            return new VaccineResource($vaccine);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function destroy(Vaccine $vaccine)
    {
        Gate::authorize('delete', $vaccine);
        $vaccine->delete();
        return response()->json(null, 204);
    }
}
