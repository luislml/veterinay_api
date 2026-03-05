<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory; use SoftDeletes;
    protected $fillable = ['name', 'last_name', 'phone', 'address','ci'];
    protected static function booted()
    {
        static::creating(function ($model) {
            if(auth()->check()) {
                $model->created_by = auth()->id();
                $model->updated_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if(auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });

        static::deleting(function ($model) {
            if(auth()->check()) {
                $model->deleted_by = auth()->id();
                $model->save();
            }
        });
    }

    public function veterinaries()
    {
        return $this->belongsToMany(Veterinary::class, 'vet_clients');
    }

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

}
