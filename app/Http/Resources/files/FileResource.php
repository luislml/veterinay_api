<?php

namespace App\Http\Resources\files;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            //'id'        => $this->id,
            //'type'      => $this->type,
            //'name'      => $this->name,
            //'extension' => $this->extension,
            //'fileable_type' => $this->fileable_type,
            //'fileable_id'   => $this->fileable_id,
            'url' => $this->publicUrl(), 
            //'created_at'=> $this->created_at,
            //'updated_at'=> $this->updated_at,
        ];
    }
    private function publicUrl()
    {
        return "storage/files/{$this->name}.{$this->extension}";
    }
}
