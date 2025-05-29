<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory, HasUuids;
    
    protected $fillable = [
        'order_id',
        'gateway',
        'method',
        'status',
        'transaction_id',
        'amount',
        'gateway_data',
    ];
    
    protected $casts = [
        'status' => PaymentStatus::class,
        'amount' => 'decimal:2',
        'gateway_data' => 'json',
    ];
    
    /**
     * Get the order associated with the payment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Format amount as currency.
     */
    public function formattedAmount(): string
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }
    
    /**
     * Get formatted payment method name.
     */
    public function formattedMethod(): string
    {
        return match($this->method) {
            'credit_card' => 'Cartão de Crédito',
            'pix' => 'PIX',
            'boleto' => 'Boleto Bancário',
            default => ucfirst($this->method),
        };
    }
    
    /**
     * Get QR code for PIX payments.
     */
    public function getPixQrCode(): ?string
    {
        if ($this->method !== 'pix' || !isset($this->gateway_data['qr_code'])) {
            return null;
        }
        
        return $this->gateway_data['qr_code'];
    }
    
    /**
     * Get PIX code for copy and paste.
     */
    public function getPixCopyPaste(): ?string
    {
        if ($this->method !== 'pix' || !isset($this->gateway_data['qr_code_text'])) {
            return null;
        }
        
        return $this->gateway_data['qr_code_text'];
    }
    
    /**
     * Get boleto URL for payments with boleto.
     */
    public function getBoletoUrl(): ?string
    {
        if ($this->method !== 'boleto' || !isset($this->gateway_data['boleto_url'])) {
            return null;
        }
        
        return $this->gateway_data['boleto_url'];
    }
}
