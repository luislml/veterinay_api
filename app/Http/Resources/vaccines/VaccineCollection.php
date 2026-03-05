<?php

namespace App\Http\Resources\vaccines;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class VaccineCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => VaccineResource::collection($this->collection),
        ];
    }
}
