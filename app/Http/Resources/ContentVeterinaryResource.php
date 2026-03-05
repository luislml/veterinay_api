<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\files\FileResource;

class ContentVeterinaryResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'file' => $this->files->first() ? new FileResource($this->files->first()) : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
