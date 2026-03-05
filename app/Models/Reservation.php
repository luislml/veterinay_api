<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = ['veterinary_id', 'name_reservation',
                            'last_name_reservation','ci_reservation',
                            'phone_reservation','details','date'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);
    }
}
