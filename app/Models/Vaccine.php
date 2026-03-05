<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vaccine extends Model
{
     use HasFactory;

    protected $fillable = ['name', 'pet_id', 'date'];
    protected static function booted()
    {
        static::creating(function ($vaccine) {
            if (!$vaccine->date) {
                $vaccine->date = now();
            }
        });
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
