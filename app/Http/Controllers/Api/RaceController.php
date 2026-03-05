<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Race;
use Illuminate\Http\Request;
use App\Http\Requests\RaceRequest;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\races\RaceResource;
use App\Http\Resources\races\RaceCollection;
use DB;

class RaceController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Race::class);
        $query = Race::with('typePet');
        // FILTRO POR TIPO DE MASCOTA
        if ($request->has('type_pet_id')) {
            $query->where('type_pet_id', $request->type_pet_id);
        }
        // Paginación opcional
        $usePagination = filter_var($request->get('paginate', true), FILTER_VALIDATE_BOOLEAN);
        if ($usePagination) {
            $races = $query->latest()->paginate(10);
        } else {
            $races = $query->latest()->get();
        }

        return new RaceCollection($races);
    }

    public function store(RaceRequest $request)
    {
        Gate::authorize('create', Race::class);
        DB::beginTransaction();
        try {
            $race = Race::create($request->validated());

            DB::commit();
            return new RaceResource($race);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function show(Race $race)
    {
        Gate::authorize('view', $race);
        //return response()->json($race->load('typePet'));
        return new RaceResource($race);
    }

    public function update(RaceRequest $request, Race $race)
    {
        Gate::authorize('update', $race);
        DB::beginTransaction();
        try {
            $race->update($request->validated());

            DB::commit();
            return new RaceResource($race);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function destroy(Race $race)
    {
        Gate::authorize('delete', $race);
        $race->delete();
        return response()->json(null, 204);
    }
}
