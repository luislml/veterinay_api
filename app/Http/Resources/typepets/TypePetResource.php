<?php

namespace App\Http\Resources\typepets;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\races\RaceResource;

class TypePetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,

            // Relación opcional
            'races' => RaceResource::collection($this->whenLoaded('races')),
        ];
    }
}
