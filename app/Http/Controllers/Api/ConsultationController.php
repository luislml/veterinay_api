<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\Http\Request;
use App\Http\Requests\ConsultationRequest;
use App\Http\Resources\consultations\ConsultationResource;
use App\Http\Resources\consultations\ConsultationCollection;
use Illuminate\Support\Facades\DB;
use Gate;


class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Consultation::class);

        $query = Consultation::query();

        if ($request->has('pet_id')) {
            $query->where('pet_id', $request->pet_id);
        }

        $plans = $query->latest()->paginate(10);
        return new ConsultationCollection($plans);
    }

    public function store(ConsultationRequest $request)
    {
        Gate::authorize('create', Consultation::class);
        DB::beginTransaction();
        try {
            // Crear la consulta
            $consultation = Consultation::create($request->validated());
            DB::commit();
            // Devolver recurso
            return new ConsultationResource($consultation);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function show(Consultation $consultation)
    {
        Gate::authorize('view', $consultation);
        return new ConsultationResource($consultation);
    }

    public function update(ConsultationRequest $request, Consultation $consultation)
    {
        Gate::authorize('update', $consultation);
        DB::beginTransaction();
        try {
            // Actualizar la consulta
            $consultation->update($request->validated());
            DB::commit();
            // Devolver recurso
            return new ConsultationResource($consultation);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function destroy(Consultation $consultation)
    {
        Gate::authorize('delete', $consultation);
        $consultation->delete();
        return response()->json(null, 204);
    }
}
