<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory, HasUuids;
    
    protected $fillable = [
        'order_id',
        'vinyl_master_id',
        'name',
        'description',
        'sku',
        'quantity',
        'unit_price',
        'original_price',
        'discount',
        'tax',
        'total_price',
        'metadata',
    ];
    
    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];
    
    /**
     * Get the order that owns the item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Get the vinyl that is associated with the item.
     */
    public function vinylMaster(): BelongsTo
    {
        return $this->belongsTo(VinylMaster::class, 'vinyl_master_id');
    }
    
    /**
     * Format unit price as currency.
     */
    public function formattedUnitPrice(): string
    {
        return 'R$ ' . number_format($this->unit_price, 2, ',', '.');
    }
    
    /**
     * Format total price as currency.
     */
    public function formattedTotalPrice(): string
    {
        return 'R$ ' . number_format($this->total_price, 2, ',', '.');
    }
}
