<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Veterinary extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($veterinary) {
            $veterinary->slug = Str::slug($veterinary->name, '-');
        });
    }

    protected $fillable = ['name', 'plan_id'];
    protected static function booted()
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
                $model->updated_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });

        static::deleting(function ($model) {
            if (auth()->check()) {
                $model->deleted_by = auth()->id();
                $model->save();
            }
        });
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_veterinary');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'vet_product');
    }

    public function advertisings()
    {
        return $this->hasMany(Advertising::class);
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'vet_clients');
    }

    public function configuration()
    {
        return $this->hasOne(Configuration::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function addresses()
    {
    return $this->hasMany(Address::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function contentVeterinaries()
    {
        return $this->hasMany(ContentVeterinary::class);
    }

    public function imagesVeterinaries()
    {
        return $this->hasMany(ImageVeterinary::class);
    }
}
