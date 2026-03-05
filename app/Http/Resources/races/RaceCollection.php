<?php

namespace App\Http\Resources\races;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RaceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => RaceResource::collection($this->collection),
        ];
    }
}
