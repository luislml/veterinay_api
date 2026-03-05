<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Race extends Model
{
     use HasFactory;

    protected $fillable = ['name', 'type_pet_id'];

    public function typePet()
    {
        return $this->belongsTo(TypePet::class);
    }

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }
}
