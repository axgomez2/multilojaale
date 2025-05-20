<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'product_type_id',
        'productable_id',
        'productable_type'
    ];

    protected $appends = ['price'];

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function productable()
    {
        return $this->morphTo();
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getVinylSecAttribute()
    {
        if ($this->productable_type === 'App\\Models\\VinylMaster') {
            return $this->productable->vinylSec;
        }
        return null;
    }

    public function getPriceAttribute()
    {
        \Log::info('Getting price for product: ' . $this->id);
        \Log::info('Productable type: ' . $this->productable_type);
        if ($this->productable_type === 'App\\Models\\VinylMaster') {
            $price = $this->vinylSec ? $this->vinylSec->price : 0;
            \Log::info('VinylSec price: ' . $price);
            return $price;
        }
        // Handle other product types here if necessary
        \Log::info('Default price: 0');
        return 0;
    }
}
