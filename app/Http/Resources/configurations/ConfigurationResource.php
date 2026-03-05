<?php

namespace App\Http\Resources\Configurations;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\veterinaries\VeterinaryResource;
use App\Http\Resources\files\FileResource;

class ConfigurationResource extends JsonResource
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
            'veterinary_id' => $this->veterinary_id,
            'color_primary' => $this->color_primary,
            'color_secondary' => $this->color_secondary,
            'about_us' => $this->about_us,
            'description_team' => $this->description_team,
            'phone' => $this->phone,
            'phone_emergency' => $this->phone_emergency,
            //'created_at'    => $this->created_at,
            //'updated_at'    => $this->updated_at,

            'veterinary' => new VeterinaryResource($this->whenLoaded('veterinary')),
            'favicon' => $this->favicon->first() ? new FileResource($this->favicon->first()) : null,
        ];
    }
}
