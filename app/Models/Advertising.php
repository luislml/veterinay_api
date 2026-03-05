<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFiles;

class Advertising extends Model
{
    use HasFactory; use HasFiles;

    protected $fillable = ['name', 'description', 'date_init', 'date_end', 'veterinary_id'];

    protected $table = 'advertising';
    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);
    }
    // Relación polimórfica solo para fotos
    public function images()
    {
        return $this->morphMany(File::class, 'fileable')
                    ->where('type', 'like', 'image/%');
    }
}
