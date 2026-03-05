<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use App\Http\Requests\PromotionRequest;
use App\Http\Resources\promotions\PromotionResource;
use Illuminate\Support\Facades\Gate;
use DB;

class PromotionController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Promotion::class);
        $promotions = Promotion::with(['veterinary', 'images'])->latest()->get();
        return PromotionResource::collection($promotions);
    }

    public function store(PromotionRequest $request)
    {
        Gate::authorize('create', Promotion::class);
        DB::beginTransaction();

        try {
            $promotion = Promotion::create($request->validated());

            // Subir imagen opcional
            if ($request->hasFile('image')) {
                $promotion->addFile($request->file('image'));
            }

            DB::commit();

            return response()->json(
                $promotion->load('veterinary', 'images'),
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

    public function show(Promotion $promotion)
    {
        Gate::authorize('view', $promotion);
        $promotion->load(['veterinary', 'images']); // Carga relaciones
        return new PromotionResource($promotion);
    }

    public function update(PromotionRequest $request, Promotion $promotion)
    {
        Gate::authorize('update', $promotion);
        DB::beginTransaction();

        try {
            // Actualizar datos excepto imagen
            $data = $request->except('image');

            if (!empty($data)) {
                $promotion->update($data);
            }

            // Manejar imagen opcional
            if ($request->hasFile('image')) {
                $promotion->addFile($request->file('image'));
            }

            DB::commit();

            return response()->json(
                $promotion->load('veterinary', 'images')
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

    public function destroy(Promotion $promotion)
    {
        Gate::authorize('delete', $promotion);
        $promotion->delete();
        return response()->json(null, 204);
    }
}
