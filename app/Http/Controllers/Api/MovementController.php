<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movement;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class MovementController extends Controller
{
    // Listar todos los movimientos
    public function index(Request $request)
{
    Gate::authorize('viewAny', Movement::class);

    $start = $request->query('start');
    $end = $request->query('end');
    $veterinaryId = $request->query('veterinary_id');

    // paginate=true por defecto
    $paginate = $request->query('paginate', 'true') === 'true';

    $query = Movement::query();

    if ($start) {
        $query->whereDate('created_at', '>=', $start);
    }

    if ($end) {
        $query->whereDate('created_at', '<=', $end);
    }

    if ($veterinaryId) {
        $query->where('veterinary_id', $veterinaryId);
    }

    $query->orderBy('created_at', 'desc');

    // Si paginate=true → usar paginación (default 15)
    if ($paginate) {
        $perPage = $request->query('per_page', 15);
        $movements = $query->paginate($perPage);
    } 
    // Si paginate=false → devolver todo
    else {
        $movements = $query->get();
    }

    return response()->json([
        'data' => $movements
    ]);
}


    // Crear un nuevo movimiento
    public function store(Request $request)
    {
        Gate::authorize('create', Movement::class);

        $data = $request->validate([
            'veterinary_id' => 'required|exists:veterinaries,id',
            'type' => 'required|in:entrada,salida',
            'amount' => 'required|numeric|min:0',
            'detail' => 'nullable|string',
        ]);

        $data['created_by'] = $request->user()->id;

        $movement = Movement::create($data);

        return response()->json($movement, 201);
    }

    // Mostrar un movimiento específico
    public function show(Movement $movement)
    {
        Gate::authorize('view', $movement);

        return response()->json($movement->load('veterinary'));
    }

    // Actualizar un movimiento
    public function update(Request $request, Movement $movement)
    {
        Gate::authorize('update', $movement);

        $data = $request->validate([
            'type' => 'sometimes|in:entrada,salida',
            'amount' => 'sometimes|numeric|min:0',
            'detail' => 'nullable|string',
        ]);

        $data['updated_by'] = $request->user()->id;

        $movement->update($data);

        return response()->json($movement);
    }

    // Eliminar un movimiento (soft delete)
    public function destroy(Movement $movement)
    {
        Gate::authorize('delete', $movement);

        $movement->deleted_by = auth()->id();
        $movement->save();

        $movement->delete();

        return response()->json(['message' => 'Movimiento eliminado correctamente']);
    }
}
