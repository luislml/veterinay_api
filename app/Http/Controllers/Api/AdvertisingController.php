<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertising;
use Illuminate\Http\Request;
use App\Http\Requests\AdvertisingRequest;
use App\Http\Resources\advertising\AdvertisingResource;
use Illuminate\Support\Facades\Gate;
use DB;

class AdvertisingController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Advertising::class);
        $advertisings = Advertising::with(['veterinary', 'images'])->latest()->get();
        return AdvertisingResource::collection($advertisings);
    }

    public function store(AdvertisingRequest $request)
    {
        Gate::authorize('create', Advertising::class);
        DB::beginTransaction();

        try {
            $advertising = Advertising::create($request->validated());

            // Subir imagen opcional
            if ($request->hasFile('image')) {
                $advertising->addFile($request->file('image'));
            }

            DB::commit();

            return response()->json(
                $advertising->load('veterinary', 'images'),
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

    public function show(Advertising $advertising)
    {
        Gate::authorize('view', $advertising);
        $advertising->load(['veterinary', 'images']);
        return new AdvertisingResource($advertising);
    }

    public function update(AdvertisingRequest $request, Advertising $advertising)
    {
        Gate::authorize('update', $advertising);
        DB::beginTransaction();

        try {
            // Actualizar datos excepto imagen
            $data = $request->except('image');

            if (!empty($data)) {
                $advertising->update($data);
            }

            // Manejar imagen opcional
            if ($request->hasFile('image')) {
                $advertising->addFile($request->file('image'));
            }

            DB::commit();

            return response()->json(
                $advertising->load('veterinary', 'images')
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

    public function destroy(Advertising $advertising)
    {
        Gate::authorize('delete', $advertising);
        $advertising->delete();
        return response()->json(null, 204);
    }
}
