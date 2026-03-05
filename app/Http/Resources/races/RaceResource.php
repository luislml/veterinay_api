<?php

namespace App\Http\Resources\races;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\typepets\TypePetResource;

class RaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'type_pet_id' => $this->type_pet_id,

            // Relación opcional
            'type_pet' => new TypePetResource($this->whenLoaded('typePet')),
        ];
    }
}
