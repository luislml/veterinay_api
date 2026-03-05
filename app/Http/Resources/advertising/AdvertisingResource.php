<?php

namespace App\Http\Resources\advertising;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\veterinaries\VeterinaryResource;
use App\Http\Resources\files\FileResource;

class   AdvertisingResource extends JsonResource
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
            'veterinary_id' => $this->veterinary_id,
            'name'          => $this->name,
            'description'   => $this->description,
            'date_init'     => $this->date_init,
            'date_end'      => $this->date_end,
            'veterinary'    => new VeterinaryResource($this->whenLoaded('veterinary')),
            'images' => FileResource::collection($this->images),
        ];
    }
}
