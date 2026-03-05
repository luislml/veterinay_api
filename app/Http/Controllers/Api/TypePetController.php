<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TypePet;
use Illuminate\Http\Request;
use App\Http\Requests\TypePetRequest;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\typePets\TypePetResource;
use App\Http\Resources\typepets\TypePetCollection;
use DB;

class TypePetController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', TypePet::class);

        // Paginación opcional (true por defecto)
        $usePagination = filter_var($request->get('paginate', true), FILTER_VALIDATE_BOOLEAN);

        $typePets = $usePagination
            ? TypePet::latest()->paginate(10)
            : TypePet::latest()->get();

        return new TypePetCollection($typePets);
    }

    public function store(TypePetRequest $request)
    {
        Gate::authorize('create', TypePet::class);
        DB::beginTransaction();
        try {
            $typePet = TypePet::create($request->validated());

            DB::commit();
            return new TypePetResource($typePet);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function show(TypePet $typePet)
    {
        Gate::authorize('view', $typePet);
        //return response()->json($typePet->load('races'));
        return new TypePetResource($typePet);
    }

    public function update(TypePetRequest $request, TypePet $typePet)
    {
        Gate::authorize('update', $typePet);
        DB::beginTransaction();
        try {
            $typePet->update($request->validated());

            DB::commit();
            return new TypePetResource($typePet);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function destroy(TypePet $typePet)
    {
        Gate::authorize('delete', $typePet);
        $typePet->delete();
        return response()->json(null, 204);
    }
}
