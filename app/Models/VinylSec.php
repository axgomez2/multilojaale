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
        'catalog_number',
        'barcode',
        'internal_code',
        'weight_id',
        'dimension_id',
        'midia_status_id',
        'cover_status_id',
        'supplier_id',
        'stock',
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
        'promo_starts_at',
        'promo_ends_at',
        'in_stock'
    ];

    protected $casts = [
        'is_new' => 'boolean',
        'is_promotional' => 'boolean',
        'in_stock' => 'boolean',
        'promo_starts_at' => 'datetime',
        'promo_ends_at' => 'datetime',
        'stock' => 'integer',
        'num_discs' => 'integer',
        'price' => 'decimal:2',
        'buy_price' => 'decimal:2',
        'promotional_price' => 'decimal:2'
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
    
    public function midiaStatus()
    {
        return $this->belongsTo(MidiaStatus::class);
    }
    
    public function coverStatus()
    {
        return $this->belongsTo(CoverStatus::class);
    }
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product()
    {
        return $this->morphOne(Product::class, 'productable');
    }

    public function categories()
    {
        return $this->belongsToMany(CatStyleShop::class, 'cat_style_shop_vinyl_master', 'vinyl_master_id', 'cat_style_shop_id')
                    ->withTimestamps();
    }

    public function playlistTracks()
    {
        return $this->morphMany(PlaylistTrack::class, 'trackable');
    }
}
