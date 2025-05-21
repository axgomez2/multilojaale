<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'subtotal',
        'discount',
        'shipping',
        'total',
        'payment_method',
        'notes',
        'invoice_number',
    ];

    /**
     * Relação com o usuário (se for cliente cadastrado)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relação com os itens da venda
     */
    public function items()
    {
        return $this->hasMany(PosSaleItem::class);
    }

    /**
     * Gera um número de invoice único
     */
    public static function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $lastSale = self::latest()->first();
        
        $sequenceNumber = 1;
        if ($lastSale) {
            // Extrai o número de sequência do último invoice (formato: INV-20250521-00001)
            $parts = explode('-', $lastSale->invoice_number);
            if (count($parts) == 3 && $parts[1] == $date) {
                $sequenceNumber = (int) $parts[2] + 1;
            }
        }
        
        return $prefix . '-' . $date . '-' . str_pad($sequenceNumber, 5, '0', STR_PAD_LEFT);
    }
}
