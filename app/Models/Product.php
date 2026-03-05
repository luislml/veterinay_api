<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use HasFiles;

    protected $fillable = ['name', 'price', 'stock', 'code'];

    public function veterinaries()
    {
        return $this->belongsToMany(Veterinary::class, 'vet_product');
    }

    public function shoppings()
    {
        return $this->belongsToMany(Shopping::class, 'product_shopping');
    }

    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'product_sale')
            ->withPivot(['quantity', 'price_unit'])
            ->withTimestamps();
    }
    public function images()
    {
        // Trae solo imágenes
        return $this->morphMany(File::class, 'fileable')
            ->where('type', 'like', 'image/%');
    }
}
