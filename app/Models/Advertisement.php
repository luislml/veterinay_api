<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = ['id_pets', 'description', 'date'];

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'id_pets');
    }
}
