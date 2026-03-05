<?php

namespace App\Http\Resources\sales;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\typepets\TypePetResource;

class SaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date_sale' => $this->date_sale,
            'state' => $this->state,
            'amount' => $this->amount,
            'discount' => $this->discount,

            'client' => $this->client ? [
                'id' => $this->client->id,
                'name' => $this->client->name,
                'last_name' => $this->client->last_name,
            ] : null,
            'products' => $this->products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $product->pivot->quantity,
                    'price_unit' => $product->pivot->price_unit,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
