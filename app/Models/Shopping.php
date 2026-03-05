<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shopping extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['date_shop', 'state', 'amount', 'veterinary_id', 'created_by', 'updated_by', 'deleted_by'];

    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);

    }


    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_shopping')
            ->withPivot('quantity', 'price_unit') // campos del pivote
            ->withTimestamps(); // si tu pivote tiene created_at y updated_at
    }
    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

}
