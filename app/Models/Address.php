<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFiles;

class Address extends Model
{
    use HasFactory; use HasFiles;

    protected $fillable = ['address', 'veterinary_id','address_type'];

    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);
    }
    public function images()
    {
        // Trae solo imágenes
        return $this->morphMany(File::class, 'fileable')
                    ->where('type', 'like', 'image/%');
    }
}
