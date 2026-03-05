<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageVeterinary extends Model
{
    use HasFactory;
    use \App\Traits\HasFiles;

    protected $table = 'images_veterinaries';
    protected $fillable = ['veterinary_id', 'type'];

    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);
    }
}
