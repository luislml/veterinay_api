<?php

namespace App\Http\Resources\Addresses;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\veterinaries\VeterinaryResource;
use App\Http\Resources\files\FileResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'address'       => $this->address,
            'address_type'  => $this->address_type,
            'veterinary_id' => $this->veterinary_id,
            //'created_at'    => $this->created_at,
            //'updated_at'    => $this->updated_at,

            'veterinary'    => new VeterinaryResource($this->whenLoaded('veterinary')),
            // Fotos
            'images' => FileResource::collection($this->images),
        ];
    }
}
