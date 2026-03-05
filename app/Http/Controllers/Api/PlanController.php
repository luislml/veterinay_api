<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use App\Http\Requests\PlanRequest;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\plans\PlanResource;
use App\Http\Resources\plans\PlanCollection;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Plan::class);

        $paginate = $request->query('paginate', true); // Por defecto paginado
        $perPage = $request->query('per_page', 10);    // Items por página

        $query = Plan::query();

        if ($paginate === 'false') {
            $plans = $query->latest()->get();
            return new PlanCollection($plans);
        }

        $plans = $query->latest()->paginate($perPage);
        return new PlanCollection($plans);
    }

    public function store(PlanRequest $request)
    {
        Gate::authorize('create', Plan::class);

        DB::beginTransaction();
        try {
            $plan = Plan::create($request->validated());
            DB::commit();
            return new PlanResource($plan);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }


        // $plan = Plan::create($request->validated());
        // return response()->json($plan, 201);
    }

    public function show(Plan $plan)
    {
        Gate::authorize('view', $plan);

        // return response()->json($plan->load('veterinaries'));
        return new PlanResource($plan);
    }

    public function update(PlanRequest $request, Plan $plan)
    {
        Gate::authorize('update', $plan);
        DB::beginTransaction();
        try {
            $plan->update($request->validated());

            DB::commit();
            return new PlanResource($plan);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function destroy(Plan $plan)
    {
        Gate::authorize('delete', $plan);
        $plan->delete();
        return response()->json(null, 204);
    }
}
