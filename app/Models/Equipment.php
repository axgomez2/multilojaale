<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;
  protected $table = "equipments";
    protected $fillable = [
        'name',
        'slug',
        'brand_id',
        'equipment_category_id',
        'description',
        'specifications',
        'weight_id',
        'dimension_id',
        'quantity',
        'price',
        'sku',
        'is_new',
        'buy_price',
        'promotional_price',
        'is_promotional',
        'in_stock'
    ];

    protected $casts = [
        'specifications' => 'array',
        'is_new' => 'boolean',
        'is_promotional' => 'boolean',
        'in_stock' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($equipment) {
            $equipment->slug = Str::slug($equipment->name);
        });
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(EquipmentCategory::class, 'equipment_category_id');
    }

    public function weight()
    {
        return $this->belongsTo(Weight::class);
    }

    public function dimension()
    {
        return $this->belongsTo(Dimension::class);
    }

    public function product()
    {
        return $this->morphOne(Product::class, 'productable');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
