<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = ['description', 'pet_id', 'date','reason'];
    protected static function booted()
    {
        static::creating(function ($consultation) {
            if (!$consultation->date) {
                $consultation->date = now();
            }
        });
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
