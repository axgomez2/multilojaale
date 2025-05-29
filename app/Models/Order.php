<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory, HasUuids;
    
    protected $fillable = [
        'user_id',
        'session_id',
        'status',
        'payment_status',
        'shipping_status',
        'subtotal',
        'shipping',
        'discount',
        'tax',
        'total',
        'shipping_quote_id',
        'shipping_address_id',
        'billing_address_id',
        'shipping_method',
        'shipping_label_url',
        'tracking_number',
        'tracking_url',
        'payment_method',
        'order_number',
        'notes',
    ];
    
    protected $casts = [
        'status' => OrderStatus::class,
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];
    
    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the payment associated with the order.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
    
    /**
     * Get the shipping quote associated with the order.
     */
    public function shippingQuote(): BelongsTo
    {
        return $this->belongsTo(ShippingQuote::class);
    }
    
    /**
     * Get the shipping address associated with the order.
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }
    
    /**
     * Get the billing address associated with the order.
     */
    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }
    
    /**
     * Format total as currency.
     */
    public function formattedTotal(): string
    {
        return 'R$ ' . number_format($this->total, 2, ',', '.');
    }
    
    /**
     * Scope a query to only include orders with a specific status.
     */
    public function scopeWithStatus($query, OrderStatus $status)
    {
        return $query->where('status', $status->value);
    }
    
    /**
     * Scope a query to only include orders for the currently authenticated user.
     */
    public function scopeForCurrentUser($query)
    {
        return $query->where('user_id', auth()->id());
    }
    
    /**
     * Scope a query to only include orders for the current session.
     */
    public function scopeForCurrentSession($query)
    {
        return $query->where('session_id', session()->getId());
    }
}
