<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFiles;

class Promotion extends Model
{
     use HasFactory; use HasFiles;

    protected $fillable = ['name', 'description', 'date_init', 'date_end', 'veterinary_id'];

    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);
    }
    public function images()
    {
        return $this->morphMany(File::class, 'fileable')
                    ->where('type', 'like', 'image/%');
    }
}
