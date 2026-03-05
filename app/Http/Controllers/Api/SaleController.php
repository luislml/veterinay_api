<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use App\Http\Requests\SaleRequest;
use App\Http\Resources\sales\SaleResource;
use App\Http\Resources\sales\SaleCollection;
use Gate;
use DB;
use App\Models\Product;
use App\Models\Movement;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Sale::class);
        $request->validate([
            'veterinary_id' => 'required|exists:veterinaries,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $veterinaryId = $request->veterinary_id;
        $perPage = $request->per_page ?? 10;

        // Query filtrando directamente por veterinary_id
        $query = Sale::where('veterinary_id', $veterinaryId)
            ->with(['products', 'client', 'veterinary']);

        // Paginación opcional
        $usePagination = filter_var($request->get('paginate', true), FILTER_VALIDATE_BOOLEAN);

        $sales = $usePagination ? $query->latest()->paginate($perPage) : $query->latest()->get();

        return new SaleCollection($sales);
    }

    public function store(SaleRequest $request)
    {
        Gate::authorize('create', Sale::class);
        $data = $request->validated();

        DB::beginTransaction();

        try {
            // 1️⃣ Crear venta con los datos principales
            $sale = Sale::create([
                'date_sale' => now(),
                'state' => $data['state'] ?? 'Completado',
                'amount' => $data['amount'] ?? 0,       // tu modelo usa amount
                'client_id' => $data['client_id'] ?? null,
                'discount' => $data['discount'] ?? 0,
                'veterinary_id' => $data['veterinary_id'],
                'created_by' => $request->user()->id,
            ]);

            // 2️⃣ Guardar productos en la tabla pivote product_sale y actualizar stock
            foreach ($data['products'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                $quantity = $item['quantity'] ?? 1;

                // Reducir stock
                if ($product->stock < $quantity) {
                    throw new \Exception("Stock insuficiente para el producto: {$product->name}");
                }
                $product->decrement('stock', $quantity);

                // Insertar en pivote
                $sale->products()->attach($product->id, [
                    'quantity' => $quantity,
                    'price_unit' => $item['price_unit'] ?? 0,
                ]);
            }
            // 3️⃣ Registrar movimiento de entrada
            Movement::create([
                'veterinary_id' => $sale->veterinary_id,
                'sale_id' => $sale->id,
                'type' => 'entrada',
                'amount' => $sale->amount,
                'detail' => 'Venta de productos, venta ID: ' . $sale->id,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Venta creada exitosamente',
                'data' => $sale->load('products', 'veterinary')
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


    public function show(Sale $sale)
    {
        Gate::authorize('view', $sale);
        return new SaleResource($sale->load('products', 'client', 'veterinary'));
    }

    public function update(SaleRequest $request, Sale $sale)
    {
        Gate::authorize('update', $sale);
        $data = $request->validated();

        DB::beginTransaction();

        try {
            // 1️⃣ Revertir stock anterior
            foreach ($sale->products as $p) {
                $product = Product::find($p->id);
                if ($product) {
                    $product->increment('stock', $p->pivot->quantity);
                }
            }

            // 2️⃣ Obtener movimiento existente
            $movement = $sale->movements()->where('sale_id', $sale->id)->first();

            // 3️⃣ Si la venta se cancela
            if (isset($data['state']) && strtolower($data['state']) === 'cancelado') {

                $sale->update([
                    'state' => 'Cancelado',
                    'amount' => 0,
                    'updated_by' => auth()->id(),
                ]);

                // eliminar productos del pivote
                $sale->products()->detach();

                // Eliminar el movimiento asociado si existe
                if ($movement) {
                    $movement->delete();
                }

                DB::commit();

                return response()->json([
                    'message' => 'Venta cancelada y stock revertido correctamente.',
                    'data' => $sale->load('products', 'veterinary')
                ]);
            }

            // 4️⃣ Actualizar datos principales
            $sale->update([
                'date_sale' => $data['date_sale'] ?? $sale->date_sale,
                'state' => $data['state'] ?? $sale->state,
                'amount' => $data['amount'] ?? $sale->amount,
                'client_id' => $data['client_id'] ?? $sale->client_id,
                'discount' => $data['discount'] ?? $sale->discount,
                'veterinary_id' => $data['veterinary_id'] ?? $sale->veterinary_id,
                'updated_by' => auth()->id(),
            ]);

            // 5️⃣ Actualizar productos y stock
            if (isset($data['products']) && is_array($data['products'])) {
                $syncData = [];
                foreach ($data['products'] as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $qty = $item['quantity'] ?? 1;

                    if ($product->stock < $qty) {
                        throw new \Exception("Stock insuficiente para el producto: {$product->name}");
                    }

                    $product->decrement('stock', $qty);

                    $syncData[$product->id] = [
                        'quantity' => $qty,
                        'price_unit' => $item['price_unit'] ?? 0,
                    ];
                }
                $sale->products()->sync($syncData);
            }

            // 6️⃣ Actualizar o crear movement
            if ($movement) {
                $movement->update([
                    'amount' => $sale->amount,
                    'detail' => 'Actualización de venta ID: ' . $sale->id,
                ]);
            } else {
                Movement::create([
                    'veterinary_id' => $sale->veterinary_id,
                    'type' => 'entrada',
                    'amount' => $sale->amount,
                    'detail' => 'Venta activada/recreada ID: ' . $sale->id,
                    'sale_id' => $sale->id,
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Venta actualizada exitosamente',
                'data' => $sale->load('products', 'veterinary')
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


    public function destroy(Sale $sale)
    {
        Gate::authorize('delete', $sale);
        $sale->deleted_by = auth()->id();
        $sale->save();
        $sale->delete();
        return response()->json(null, 204);
    }
}
