<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['date_sale', 'state', 'amount', 'client_id', 'discount', 'veterinary_id', 'created_by', 'updated_by', 'deleted_by'];

    public function client()
    {
        return $this->belongsTo(Client::class);

    }

    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_sale')
            ->withPivot(['quantity', 'price_unit'])
            ->withTimestamps();
    }
    public function movements()
    {
        return $this->hasMany(Movement::class, 'sale_id');
    }


}
