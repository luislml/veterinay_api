<?php

namespace App\Http\Resources\products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\files\FileResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            'code' => $this->code,
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
            // Solo devolver las veterinarias que vienen cargadas
            'veterinaries' => $this->whenLoaded('veterinaries', function () {
                return $this->veterinaries->map(function ($vet) {
                    return [
                        'id' => $vet->id,
                        'name' => $vet->name,
                        'slug' => $vet->slug,
                        //'pivot' => $vet->pivot ? [
                            //'product_id' => $vet->pivot->product_id,
                            //'veterinary_id' => $vet->pivot->veterinary_id,
                        //] : null,
                    ];
                });
            }),
            'images' => FileResource::collection($this->images),
        ];
    }
}
