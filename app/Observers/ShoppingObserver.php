<?php

namespace App\Observers;

use App\Models\Shopping;
use Illuminate\Support\Facades\Log;

class ShoppingObserver
{
    public function created(Shopping $shopping)
    {
        Log::info('Shopping created', [
            'shopping_id' => $shopping->id,
            'user_id' => $shopping->user_id,
            'amount' => $shopping->amount,
            'state' => $shopping->state,
            'veterinary_id' => $shopping->veterinary_id,
            'products' => $shopping->products->map(function ($p) {
                return [
                    'product_id' => $p->id,
                    'name' => $p->name,
                    'quantity' => $p->pivot->quantity,
                    'price_unit' => $p->pivot->price_unit,
                ];
            }),
        ]);
    }

    public function updated(Shopping $shopping)
    {
        Log::info('Shopping updated', [
            'shopping_id' => $shopping->id,
            'changes' => $shopping->getChanges(),
            'user_id' => auth()->id(),
        ]);
    }

    public function deleted(Shopping $shopping)
    {
        Log::warning('Shopping deleted', [
            'shopping_id' => $shopping->id,
            'user_id' => auth()->id(),
        ]);
    }
}
