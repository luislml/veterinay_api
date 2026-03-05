<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentVeterinary extends Model
{
    use HasFactory;
    use \App\Traits\HasFiles;

    protected $table = 'content_veterinaries';

    protected $fillable = [
        'veterinary_id',
        'title',
        'description',
        'type',
    ];

    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);
    }
}
