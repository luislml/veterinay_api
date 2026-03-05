<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shopping;
use Illuminate\Http\Request;
use App\Http\Requests\ShoppingRequest;
use App\Http\Resources\shoppings\ShoppingResource;
use App\Http\Resources\shoppings\ShoppingCollection;
use Gate;
use DB;
use App\Models\Product;
use App\Models\Movement;


class ShoppingController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Shopping::class);

        $request->validate([
            'veterinary_id' => 'required|exists:veterinaries,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $veterinaryId = $request->veterinary_id;
        $perPage = $request->per_page ?? 10;

        // Construir query base filtrando por veterinary_id
        $query = Shopping::where('veterinary_id', $veterinaryId)
            ->with(['products', 'veterinary'])
            ->orderBy('date_shop', 'desc');

        // Paginación opcional
        $usePagination = filter_var($request->get('paginate', true), FILTER_VALIDATE_BOOLEAN);

        $shoppings = $usePagination ? $query->paginate($perPage) : $query->get();

        // Retornar usando ShoppingResource, funciona con Collection y Paginator
        return ShoppingResource::collection($shoppings);
    }

    public function store(ShoppingRequest $request)
    {
        Gate::authorize('create', Shopping::class);
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $totalAmount = 0;

            // Calcular total
            foreach ($data['products'] as $item) {
                $totalAmount += ($item['quantity'] ?? 1) * ($item['price_unit'] ?? 0);
            }

            // Crear la compra
            $shopping = Shopping::create([
                'date_shop' => now(),
                'state' => $data['state'] ?? 'Completado',
                'amount' => $totalAmount,
                'veterinary_id' => $data['veterinary_id'],
                'created_by' => auth()->id(),
            ]);

            // Guardar productos y actualizar stock
            foreach ($data['products'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = $item['quantity'] ?? 1;

                // Incrementar stock
                $product->increment('stock', $quantity);

                // Guardar en tabla pivote
                $shopping->products()->attach($product->id, [
                    'quantity' => $quantity,
                    'price_unit' => $item['price_unit'] ?? 0,
                ]);
            }
            // Registrar movimiento de salida
            Movement::create([
                'veterinary_id' => $shopping->veterinary_id,
                'shopping_id' => $shopping->id,
                'type' => 'salida',
                'amount' => $totalAmount,
                'detail' => 'Compra de productos, ID de compra: ' . $shopping->id,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Compra creada exitosamente',
                'data' => $shopping->load('products', 'veterinary'),
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

    public function show(Shopping $shopping)
    {
        Gate::authorize('view', $shopping);
        $shopping->load('products', 'veterinary');
        return new ShoppingResource($shopping);
    }

    public function update(ShoppingRequest $request, Shopping $shopping)
    {
        Gate::authorize('update', $shopping);
        $data = $request->validated();

        DB::beginTransaction();

        try {
            // 1️⃣ Revertir stock anterior (restar la cantidad de la compra)
            foreach ($shopping->products as $p) {
                $product = Product::find($p->id);
                if ($product) {
                    $product->decrement('stock', $p->pivot->quantity);
                }
            }

            // 2️⃣ Obtener movimiento existente
            $movement = $shopping->movements()->where('shopping_id', $shopping->id)->first();

            // 3️⃣ Si se cancela la compra
            if (isset($data['state']) && strtolower($data['state']) === 'cancelado') {

                $shopping->update([
                    'state' => 'Cancelado',
                    'amount' => 0,
                    'updated_by' => auth()->id(),
                ]);

                $shopping->products()->detach();

                if ($movement) {
                    $movement->delete();
                }

                DB::commit();

                return response()->json([
                    'message' => 'Compra cancelada y stock revertido correctamente.',
                    'data' => $shopping->load('products', 'veterinary')
                ]);
            }

            // 4️⃣ Calcular nuevo total
            $totalAmount = 0;
            foreach ($data['products'] as $item) {
                $totalAmount += ($item['quantity'] ?? 1) * ($item['price_unit'] ?? 0);
            }

            // 5️⃣ Actualizar datos principales
            $shopping->update([
                'state' => $data['state'] ?? $shopping->state,
                'amount' => $totalAmount,
                'veterinary_id' => $data['veterinary_id'] ?? $shopping->veterinary_id,
                'updated_by' => auth()->id(),
            ]);

            // 6️⃣ Actualizar productos y stock
            $syncData = [];
            foreach ($data['products'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = $item['quantity'] ?? 1;

                // Incrementar stock (compra)
                $product->increment('stock', $qty);

                $syncData[$product->id] = [
                    'quantity' => $qty,
                    'price_unit' => $item['price_unit'] ?? 0,
                ];
            }
            $shopping->products()->sync($syncData);

            // 7️⃣ Actualizar o crear movement
            if ($movement) {
                $movement->update([
                    'amount' => $shopping->amount,
                    'detail' => 'Actualización de compra ID: ' . $shopping->id,
                ]);
            } else {
                Movement::create([
                    'veterinary_id' => $shopping->veterinary_id,
                    'type' => 'salida',
                    'amount' => $shopping->amount,
                    'detail' => 'Compra activada/recreada ID: ' . $shopping->id,
                    'shopping_id' => $shopping->id,
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Compra actualizada exitosamente',
                'data' => $shopping->load('products', 'veterinary')
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }

    public function destroy(Shopping $shopping)
    {
        Gate::authorize('delete', $shopping);
        $shopping->deleted_by = auth()->id();
        $shopping->save();
        $shopping->delete();
        return response()->json(null, 204);
    }
}
