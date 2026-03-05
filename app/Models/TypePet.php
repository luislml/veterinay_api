<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypePet extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function races()
    {
        return $this->hasMany(Race::class);
    }

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }
}
