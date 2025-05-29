<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory, HasUuids;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'subtotal',
        'discount',
        'shipping_cost',
        'total',
        'coupon_code',
        'shipping_method',
        'status',
        'name',
        'is_default',
        'last_activity',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'is_default' => 'boolean',
    ];

    /**
     * Relacionamento com o usuário dono do carrinho.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com os itens do carrinho.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Calcular o subtotal do carrinho somando todos os itens.
     *
     * @return float
     */
    public function calculateSubtotal()
    {
        return $this->items()->with('vinylMaster.vinylSec')->get()->sum(function($item) {
            return $item->subtotal;
        });
    }

    /**
     * Calcular o total do carrinho (subtotal - desconto + frete).
     *
     * @return float
     */
    public function calculateTotal()
    {
        return $this->subtotal - $this->discount + $this->shipping_cost;
    }

    /**
     * Atualizar os totais do carrinho.
     *
     * @return void
     */
    public function updateTotals()
    {
        $this->subtotal = $this->calculateSubtotal();
        $this->total = $this->calculateTotal();
        $this->save();
    }

    /**
     * Aplicar um cupom de desconto ao carrinho.
     *
     * @param string $couponCode
     * @return bool
     */
    public function applyCoupon($couponCode)
    {
        // Implementação para aplicar cupom de desconto
        // A ser implementado quando o sistema de cupons for desenvolvido
        $this->coupon_code = $couponCode;
        $this->save();
        
        return true;
    }

    /**
     * Obter ou criar um carrinho para o usuário atual.
     *
     * @param string|null $userId
     * @param string|null $sessionId
     * @return \App\Models\Cart
     */
    public static function getOrCreateCart($userId = null, $sessionId = null)
    {
        $cart = null;
        
        if ($userId) {
            // Tentar encontrar carrinho pelo ID do usuário
            $cart = self::where('user_id', $userId)->where('status', 'active')->first();
        } elseif ($sessionId) {
            // Tentar encontrar carrinho pelo ID da sessão
            $cart = self::where('session_id', $sessionId)->where('status', 'active')->first();
        }
        
        // Se não encontrou um carrinho, criar um novo
        if (!$cart) {
            $cart = self::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'status' => 'active',
            ]);
        }
        
        return $cart;
    }
}
