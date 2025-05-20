<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VinylSec extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vinyl_master_id',
        'cover_status',
        'midia_status',
        'catalog_number',
        'barcode',
        'weight_id',
        'dimension_id',
        'quantity',
        'price',
        'format',
        'num_discs',
        'speed',
        'edition',
        'notes',
        'is_new',
        'buy_price',
        'promotional_price',
        'is_promotional',
        'in_stock',
        'cat_style_shop_id'
    ];

    protected $casts = [
        'cover_status' => 'string',
        'midia_status' => 'string',
        'is_new' => 'boolean',
        'is_promotional' => 'boolean',
        'in_stock' => 'boolean',
    ];

    public function vinylMaster(): BelongsTo
    {
        return $this->belongsTo(VinylMaster::class);
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

    public function categories()
    {
        return $this->belongsToMany(CatStyleShop::class, 'cat_style_shop_vinyl_sec');
    }

    public function playlistTracks()
    {
        return $this->morphMany(PlaylistTrack::class, 'trackable');
    }
}
