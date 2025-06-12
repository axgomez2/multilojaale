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
        'product_id',
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
        'product_id' => 'integer',
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
     * @deprecated Use o relacionamento product em vez disso
     */
    public function vinylMaster(): BelongsTo
    {
        return $this->belongsTo(VinylMaster::class);
    }
    
    /**
     * Obter o produto que está no carrinho.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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
        // Se temos um produto, usamos seu preço
        if ($this->product) {
            return $this->product->price * $this->quantity;
        }
        
        // Retrocompatibilidade: se não temos produto mas temos vinyl_master, usamos ele
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
        // Verificar produto primeiro
        if ($this->product) {
            // Para produtos do tipo VinylMaster
            if ($this->product->productable_type === 'App\\Models\\VinylMaster' && $this->product->vinylSec) {
                // Verificar se o estoque é maior que zero E suficiente para a quantidade solicitada
                return $this->product->vinylSec->stock > 0 && $this->product->vinylSec->stock >= $this->quantity;
            }
            
            // Para outros tipos de produtos, implementar lógica específica aqui
            // ...
        }
        
        // Retrocompatibilidade
        if ($this->vinylMaster && $this->vinylMaster->vinylSec) {
            // Verificar se o estoque é maior que zero E suficiente para a quantidade solicitada
            return $this->vinylMaster->vinylSec->stock > 0 && $this->vinylMaster->vinylSec->stock >= $this->quantity;
        }
        
        return false;
    }
    
    /**
     * Verifica se o produto está disponível para compra.
     */
    public function isAvailable(): bool
    {
        // Verificar produto primeiro
        if ($this->product) {
            // Para produtos do tipo VinylMaster
            if ($this->product->productable_type === 'App\\Models\\VinylMaster') {
                return $this->product->productable && $this->product->productable->isAvailable();
            }
            
            // Para outros tipos de produtos, verificar disponibilidade conforme necessário
            // Por padrão, assumimos que está disponível se o produto existe
            return true;
        }
        
        // Retrocompatibilidade
        return $this->vinylMaster && $this->vinylMaster->isAvailable();
    }
}
