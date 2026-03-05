<?php

namespace App\Http\Resources\veterinaries;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\plans\PlanResource;
use App\Http\Resources\addresses\AddressResource;
use App\Http\Resources\configurations\ConfigurationResource;
use App\Http\Resources\schedules\ScheduleResource;
use App\Http\Resources\users\UserResource;
use App\Http\Resources\ContentVeterinaryResource;
use App\Http\Resources\ImageVeterinaryResource;

class VeterinaryResource extends JsonResource
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
            'slug' => $this->slug,

            // Relaciones principales
            'plan' => new PlanResource($this->whenLoaded('plan')),
            //'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
            'addresses' => $this->whenLoaded('addresses', function ($addresses) {
                return $addresses
                    ->groupBy('address_type') // agrupa por address_type
                    ->map(function ($items, $address_type) {
                        return AddressResource::collection($items);
                    });
            }),

            'configuration' => new ConfigurationResource($this->whenLoaded('configuration')),
            'schedules' => ScheduleResource::collection($this->whenLoaded('schedules')),
            'content_veterinaries' => $this->whenLoaded('contentVeterinaries', function ($content) {
                return $content->groupBy('type')->map(function ($items, $type) {
                    return ContentVeterinaryResource::collection($items);
                });
            }),
            'images_veterinaries' => ImageVeterinaryResource::collection($this->whenLoaded('imagesVeterinaries')),

            // Relaciones adicionales
            'users' => UserResource::collection($this->whenLoaded('users')),
            //'products'     => ProductResource::collection($this->whenLoaded('products')),
            //'clients'      => ClientResource::collection($this->whenLoaded('clients')),
        ];
    }
}
