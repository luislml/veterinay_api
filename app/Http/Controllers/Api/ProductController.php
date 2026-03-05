<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\products\ProductResource;
use App\Http\Resources\products\ProductCollection;
use DB;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Product::class);

        $user = $request->user();
        $query = Product::query();

        if ($user->hasRole('admin')) {
            // Admin ve todos los productos con sus veterinarias
            $query->with('veterinaries');
        } else {
            // Usuario con rol veterinary: obtener IDs de sus veterinarias
            $userVetIds = $user->veterinaries()->pluck('veterinaries.id');

            if ($userVetIds->isEmpty()) {
                return response()->json([], 200);
            }

            // Filtrar productos que tengan al menos una veterinaria del usuario
            $query->whereHas('veterinaries', function ($q) use ($userVetIds) {
                $q->whereIn('veterinaries.id', $userVetIds);
            });

            // Cargar solo las veterinarias del usuario en la respuesta JSON
            $query->with([
                'veterinaries' => function ($q) use ($userVetIds) {
                    $q->whereIn('veterinaries.id', $userVetIds);
                }
            ]);
        }

        // Filtro opcional por veterinary_id
        if ($request->filled('veterinary_id')) {
            $veterinaryId = $request->veterinary_id;
            $query->whereHas('veterinaries', function ($q) use ($veterinaryId) {
                $q->where('veterinaries.id', $veterinaryId);
            });
        }

        // Filtro de búsqueda (nombre, código)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        // Paginación opcional
        $usePagination = filter_var($request->get('paginate', true), FILTER_VALIDATE_BOOLEAN);
        $products = $usePagination ? $query->latest()->paginate(10) : $query->latest()->get();

        return new ProductCollection($products);
    }

    public function store(ProductRequest $request)
{
    Gate::authorize('create', Product::class);

    DB::beginTransaction();

    try {
        $user = $request->user();

        // Veterinarias enviadas por el usuario
        $requestedVetIds = collect($request->veterinary_ids);

        // Veterinarias que realmente pertenecen al usuario autenticado
        $userVetIds = $user->veterinaries()->pluck('veterinary_id');

        // Validar que SOLO use veterinarias que son suyas
        $invalidVetIds = $requestedVetIds->diff($userVetIds);

        if ($invalidVetIds->isNotEmpty()) {
            throw new \Exception("No puedes asignar un producto a veterinarias que no te pertenecen.");
        }

        // Crear el producto
        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'code' => $request->code ?? null,
        ]);

        // Asociar veterinarias
        $product->veterinaries()->attach($requestedVetIds);

        // Guardar imagen si existe
        if ($request->hasFile('image')) {
            $product->addFile($request->file('image'));
        }

        DB::commit();

        return response()->json([
            'message' => 'Producto creado exitosamente',
            'data' => $product->load('veterinaries')
        ], 201);

    } catch (\Throwable $th) {

        DB::rollBack();

        return response()->json([
            'error' => $th->getMessage(),
            'line' => $th->getLine(),
            'file' => $th->getFile(),
        ], 500);
    }
}


    public function show(Product $product)
    {
        Gate::authorize('view', $product);
        return new ProductResource($product);
    }

    public function update(ProductRequest $request, Product $product)
    {
        Gate::authorize('update', $product);
        $user = $request->user();

        DB::beginTransaction();

        try {

            // Veterinarias enviadas por el usuario
            $requestedVetIds = collect($request->veterinary_ids ?? []);

            // Veterinarias que realmente pertenecen al usuario autenticado
            $userVetIds = $user->veterinaries()->pluck('veterinary_id');

            // Validar que SOLO use veterinarias que son suyas
            $invalidVetIds = $requestedVetIds->diff($userVetIds);

            if ($invalidVetIds->isNotEmpty()) {
                throw new \Exception(
                    "No puedes asignar un producto a veterinarias que no te pertenecen: " 
                    . implode(',', $invalidVetIds->toArray())
                );
            }

            // Actualizar producto
            $product->update([
                'name' => $request->name ?? $product->name,
                'price' => $request->price ?? $product->price,
                'stock' => $request->stock ?? $product->stock,
                'code' => $request->code ?? $product->code,
            ]);

            // Actualizar veterinarias (si se enviaron)
            if ($requestedVetIds->isNotEmpty()) {
                $product->veterinaries()->sync($requestedVetIds);
            }

            // Guardar imagen si se envía una nueva
            if ($request->hasFile('image')) {
                $product->addFile($request->file('image')); // tu helper existente
            }

            DB::commit();

            return response()->json([
                'message' => 'Producto actualizado correctamente.',
                'data' => $product->load('veterinaries')
            ], 200);

        } catch (\Throwable $th) {

            DB::rollBack();

            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }


    public function destroy(Product $product)
    {
        Gate::authorize('delete', $product);
        $product->delete();
        return response()->json(null, 204);
    }
}
