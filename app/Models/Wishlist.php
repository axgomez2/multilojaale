<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    use HasFactory, HasUuids;
    
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'vinyl_master_id',
    ];
    
    /**
     * Os atributos que devem ser convertidos em tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'vinyl_master_id' => 'integer',
    ];
    
    /**
     * Obter o usuário ao qual este item da lista de desejos pertence.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Obter o disco de vinil que está na lista de desejos.
     */
    public function vinylMaster(): BelongsTo
    {
        return $this->belongsTo(VinylMaster::class);
    }
    
    /**
     * Verifica se um disco específico está na wishlist de um usuário
     */
    public static function hasItem($userId, $vinylMasterId): bool
    {
        return static::where('user_id', $userId)
            ->where('vinyl_master_id', $vinylMasterId)
            ->exists();
    }
}
