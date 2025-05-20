<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * A tabela associada ao modelo
     *
     * @var string
     */
    protected $table = 'suppliers';
    
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'document',
        'document_type',
        'address',
        'city',
        'state',
        'zipcode',
        'website',
        'last_purchase_date',
        'notes'
    ];
    
    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'last_purchase_date' => 'date',
    ];
    
    /**
     * Retorna os discos (vinyl_secs) fornecidos por este fornecedor
     */
    public function vinylSecs()
    {
        return $this->hasMany(VinylSec::class);
    }
}
