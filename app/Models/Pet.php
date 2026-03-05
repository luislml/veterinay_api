<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pet extends Model
{
    use HasFactory; use HasFiles; use SoftDeletes;

    protected $fillable = ['name', 'race_id', 'client_id', 'color', 'gender', 'birthday', 'code'];

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
    protected static function boot()
    {
        parent::boot();

        // Generar código automáticamente antes de crear la mascota
        static::creating(function ($pet) {
            if (empty($pet->code)) {
                $pet->code = 'PET' . strtoupper(Str::random(6));
            }
        });
    }
    public function race()
    {
        return $this->belongsTo(Race::class);
    }

    public function vaccines()
    {
        return $this->hasMany(Vaccine::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function images()
    {
        return $this->morphMany(File::class, 'fileable')
                    ->where('type', 'like', 'image/%');
    }







}
