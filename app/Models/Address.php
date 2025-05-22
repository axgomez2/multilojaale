<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;
    
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'recipient',
        'type',
        'zipcode',
        'state',
        'city',
        'district',
        'street',
        'number',
        'complement',
        'reference',
        'phone',
        'is_default',
        'is_active',
    ];
    
    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];
    
    /**
     * Obtém o usuário associado ao endereço.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Retorna o endereço formatado em uma única linha.
     */
    public function getFormattedAttribute(): string
    {
        $address = "$this->street, $this->number";
        
        if ($this->complement) {
            $address .= " - $this->complement";
        }
        
        $address .= " - $this->district, $this->city/$this->state - $this->zipcode";
        
        return $address;
    }
    
    /**
     * Formata o CEP.
     */
    public function getFormattedZipcodeAttribute(): string
    {
        $zipcode = preg_replace('/[^0-9]/', '', $this->zipcode);
        
        if (strlen($zipcode) === 8) {
            return substr($zipcode, 0, 5) . '-' . substr($zipcode, 5, 3);
        }
        
        return $this->zipcode;
    }
    
    /**
     * Define o endereço como padrão e remove o padrão de outros endereços do mesmo usuário.
     */
    public function setAsDefault(): bool
    {
        // Primeiro, remove o padrão de todos os outros endereços do usuário
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->where('is_default', true)
            ->update(['is_default' => false]);
            
        // Define este endereço como padrão
        $this->is_default = true;
        
        return $this->save();
    }
}
