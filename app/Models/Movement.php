<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'veterinary_id',
        'type',
        'amount',
        'detail',
        'created_by',
        'updated_by',
        'deleted_by',
        'sale_id',
        'shopping_id',
    ];

    // Relación con veterinary
    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);
    }
    // Relación opcional con Sale
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // Relación opcional con Shopping
    public function shopping()
    {
        return $this->belongsTo(Shopping::class);
    }
}
