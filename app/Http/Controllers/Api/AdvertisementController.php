<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Advertisement;
use Barryvdh\DomPDF\Facade\Pdf;
use Gate;
use DB;

class AdvertisementController extends Controller
{
    /**
     * Mostrar todos los anuncios.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Advertisement::class);

        // Veterinaria pasada por parámetro (opcional)
        $veterinaryId = $request->query('veterinary_id');

        // Query base
        $query = Advertisement::with([
            'pet',
            'pet.client',
            'pet.client.veterinaries'
        ]);

        // Si el usuario fuerza un filtro por ?veterinary_id=XX
        if ($veterinaryId) {
            $query->whereHas('pet.client.veterinaries', function ($q) use ($veterinaryId) {
                $q->where('veterinary_id', $veterinaryId);
            });
        }

        // Si NO manda veterinary_id → filtrar por veterinarias del usuario autenticado
        else {
            $user = auth()->user();

            // Veterinarias a las que pertenece el usuario
            $userVetIds = $user->veterinaries->pluck('id');

            // Filtrar anuncios solo de esas veterinarias
            $query->whereHas('pet.client.veterinaries', function ($q) use ($userVetIds) {
                $q->whereIn('veterinary_id', $userVetIds);
            });
        }

        return $query->latest()->get();
    }

    /**
     * Crear un nuevo anuncio.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Advertisement::class);
        DB::beginTransaction();
        try {
            // Validación
            $data = $request->validate([
                'id_pets' => 'required|exists:pets,id',
                'description' => 'nullable|string|max:255',
                'date' => 'required|date',
            ]);
            // Crear anuncio
            $advertisement = Advertisement::create($data);
            DB::commit();
            // Respuesta con la mascota relacionada
            return response()->json(
                $advertisement->load('pet'),
                201
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

    /**
     * Mostrar un anuncio específico.
     */
    public function show($id)
    {
        $advertisement = Advertisement::with([
            'pet',
            'pet.race',
            'pet.client',
            'pet.images',
            'pet.vaccines',
        ])->findOrFail($id);
        Gate::authorize('view', $advertisement);

        return response()->json($advertisement);
    }

    /**
     * Actualizar un anuncio.
     */
    public function update(Request $request, $id)
    {
        $advertisement = Advertisement::findOrFail($id);
        Gate::authorize('update', $advertisement);
        DB::beginTransaction();
        try {
            // Validación
            $data = $request->validate([
                'id_pets' => 'sometimes|exists:pets,id',
                'description' => 'nullable|string|max:255',
                'date' => 'sometimes|date',
            ]);
            // Actualizar
            $advertisement->update($data);
            DB::commit();
            // Retornar con relaciones cargadas
            return response()->json(
                $advertisement->load('pet')
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

    /**
     * Eliminar un anuncio.
     */
    public function destroy($id)
    {
        $advertisement = Advertisement::findOrFail($id);
        Gate::authorize('delete', $advertisement);
        $advertisement->delete();

        return response()->json(['message' => 'Advertisement deleted successfully']);
    }
    public function downloadPdf(Advertisement $advertisement)
    {
        Gate::authorize('download', $advertisement);
        $advertisement->load([
            'pet',
            'pet.race',
            'pet.client',
            'pet.images',
            'pet.vaccines',
        ]);

        $pdf = Pdf::loadView('pdf.advertisement', [
            'ad' => $advertisement,
            'pet' => $advertisement->pet
        ]);

        $pdfContent = $pdf->output();
        $base64 = base64_encode($pdfContent);

        return response()->json([
            'file_name' => 'advertisement_' . $advertisement->id . '.pdf',
            'pdf_base64' => $base64
        ]);
    }
}
