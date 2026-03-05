<?php

namespace App\Http\Resources\pets;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\races\RaceResource;
use App\Http\Resources\clients\ClientResource;
use App\Http\Resources\vaccines\VaccineResource;
use App\Http\Resources\consultations\ConsultationResource;
use App\Http\Resources\files\FileResource;

use Storage;

class PetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'color'      => $this->color,
            'gender'     => $this->gender,
            'birthday'   => $this->birthday,
            'race_id'    => $this->race_id,
            'code'       => $this->code,
            'client_id'  => $this->client_id,

            // Relaciones
            'race' => new RaceResource(
                $this->whenLoaded('race')
            ),

            'client' => new ClientResource(
                $this->whenLoaded('client')
            ),

            'vaccines' => VaccineResource::collection(
                $this->whenLoaded('vaccines')
            ),

            'consultations' => ConsultationResource::collection(
                $this->whenLoaded('consultations')
            ),
           'images' => FileResource::collection($this->images),
        ];
    }
}
