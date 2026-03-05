<?php

namespace App\Http\Resources\clients;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\veterinaries\VeterinaryResource;
use App\Http\Resources\pets\PetResource;

class ClientResource extends JsonResource
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
            'last_name'   => $this->last_name,
            'phone'       => $this->phone,
            'address'     => $this->address,
            'ci'          => $this->ci,
            //'created_at'  => $this->created_at,
            //'updated_at'  => $this->updated_at,

            // Relaciones cargadas condicionalmente
            'veterinaries' => VeterinaryResource::collection(
                $this->whenLoaded('veterinaries')
            ),

            'pets' => PetResource::collection(
                $this->whenLoaded('pets')
            ),
        ];
    }
}
