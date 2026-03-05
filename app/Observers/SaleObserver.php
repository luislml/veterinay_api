<?php

namespace App\Observers;

use App\Models\Sale;
use Illuminate\Support\Facades\Log;

class SaleObserver
{
    public function created(Sale $sale)
    {
        Log::info('Sale created', [
            'sale_id' => $sale->id,
            'user_id' => $sale->user_id,
            'client_id' => $sale->client_id,
            'amount' => $sale->amount,
            'state' => $sale->state,
            'veterinary_id' => $sale->veterinary_id,
            'products' => $sale->products->map(function ($p) {
                return [
                    'product_id' => $p->id,
                    'name' => $p->name,
                    'quantity' => $p->pivot->quantity,
                    'price_unit' => $p->pivot->price_unit,
                ];
            }),
        ]);
    }

    public function updated(Sale $sale)
    {
        Log::info('Sale updated', [
            'sale_id' => $sale->id,
            'changes' => $sale->getChanges(),
            'user_id' => auth()->id(),
        ]);
    }

    public function deleted(Sale $sale)
    {
        Log::warning('Sale deleted', [
            'sale_id' => $sale->id,
            'user_id' => auth()->id(),
        ]);
    }
}
