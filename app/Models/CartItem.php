<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory, HasUuids;
    
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'cart_id',
        'vinyl_master_id',
        'quantity',
        'saved_for_later',
    ];
    
    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'cart_id' => 'string',
        'vinyl_master_id' => 'integer',
        'quantity' => 'integer',
        'saved_for_later' => 'boolean',
    ];
    
    /**
     * Obter o usuário ao qual este item do carrinho pertence.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Obter o disco de vinil que está no carrinho.
     */
    public function vinylMaster(): BelongsTo
    {
        return $this->belongsTo(VinylMaster::class);
    }
    
    /**
     * Obter o carrinho ao qual este item pertence.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }
    
    /**
     * Obter o subtotal do item (preço * quantidade).
     */
    public function getSubtotalAttribute()
    {
        if ($this->vinylMaster && $this->vinylMaster->vinylSec) {
            return $this->vinylMaster->vinylSec->price * $this->quantity;
        }
        
        return 0;
    }
    
    /**
     * Verifica se há estoque suficiente para este item do carrinho.
     */
    public function hasEnoughStock(): bool
    {
        if (!$this->vinylMaster || !$this->vinylMaster->vinylSec) {
            return false;
        }
        
        return $this->vinylMaster->vinylSec->stock >= $this->quantity;
    }
    
    /**
     * Verifica se o produto está disponível para compra.
     */
    public function isAvailable(): bool
    {
        return $this->vinylMaster && $this->vinylMaster->isAvailable();
    }
}
