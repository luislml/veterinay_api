<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Requests\ScheduleRequest;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\schedules\ScheduleResource;
use App\Http\Resources\schedules\ScheduleCollection;
use DB;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Schedule::class);
        $query = Schedule::with('veterinary');

        // Filtro opcional por veterinaria
        if ($request->has('veterinary_id')) {
            $query->where('veterinary_id', $request->veterinary_id);
        }
        $schedules = $query->latest()->get();

        return new ScheduleCollection($schedules);
    }

    public function store(ScheduleRequest $request)
    {
        Gate::authorize('create', Schedule::class);
        DB::beginTransaction();
        try {
            // Crear el schedule
            $schedule = Schedule::create($request->validated());

            DB::commit();

            // Devolver recurso con la relación veterinary cargada
            return new ScheduleResource($schedule->load('veterinary'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function show(Schedule $schedule)
    {
        Gate::authorize('view', $schedule);
        return new ScheduleResource($schedule->load('veterinary'));
        //return response()->json($schedule->load('veterinary'));
    }

    public function update(ScheduleRequest $request, Schedule $schedule)
    {
        Gate::authorize('update', $schedule);
        DB::beginTransaction();
        try {
            // Actualizar el schedule
            $schedule->update($request->validated());
            DB::commit();
            // Devolver recurso con la relación veterinary cargada
            return new ScheduleResource($schedule->load('veterinary'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function destroy(Schedule $schedule)
    {
        Gate::authorize('delete', $schedule);
        $schedule->delete();
        return response()->json(null, 204);
    }
}
