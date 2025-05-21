<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosSaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pos_sale_id',
        'vinyl_sec_id',
        'price',
        'quantity',
        'item_discount',
        'item_total',
    ];

    /**
     * Relação com a venda
     */
    public function sale()
    {
        return $this->belongsTo(PosSale::class, 'pos_sale_id');
    }

    /**
     * Relação com o disco
     */
    public function vinyl()
    {
        return $this->belongsTo(VinylSec::class, 'vinyl_sec_id');
    }
}
