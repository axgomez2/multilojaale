<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ShippingQuote extends Model
{
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quote_token',
        'user_id',
        'session_id',
        'cart_items_hash',
        'cart_items',
        'zip_from',
        'zip_to',
        'products',
        'api_response',
        'options',
        'selected_service_id',
        'selected_price',
        'selected_delivery_time',
        'expires_at',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cart_items' => 'array',
        'products' => 'array',
        'api_response' => 'array',
        'options' => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($model) {
            // Gera automaticamente um UUID para o quote_token se não estiver definido
            if (!$model->quote_token) {
                $model->quote_token = (string) Str::uuid();
            }

            // Define a data de expiração padrão (24 horas a partir de agora)
            if (!$model->expires_at) {
                $model->expires_at = now()->addHours(24);
            }
        });
    }

    /**
     * Relacionamento com o usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verifica se a cotação está expirada
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Método de escopo para cotações não expiradas
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Formata o preço selecionado
     *
     * @return string
     */
    public function getFormattedPriceAttribute(): string
    {
        if (!$this->selected_price) {
            return 'R$ 0,00';
        }

        return 'R$ ' . number_format($this->selected_price, 2, ',', '.');
    }
}
