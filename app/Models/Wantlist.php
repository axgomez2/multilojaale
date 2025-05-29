<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wantlist extends Model
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
        'notification_sent',
        'last_notification_at',
    ];
    
    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'vinyl_master_id' => 'integer',
        'notification_sent' => 'boolean',
        'last_notification_at' => 'datetime',
    ];
    
    /**
     * Obter o usuário ao qual este item da lista de interesse pertence.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Obter o disco de vinil que está na lista de interesse.
     */
    public function vinylMaster(): BelongsTo
    {
        return $this->belongsTo(VinylMaster::class);
    }
    
    /**
     * Verifica se um disco específico está na wantlist de um usuário
     */
    public static function hasItem($userId, $vinylMasterId): bool
    {
        return static::where('user_id', $userId)
            ->where('vinyl_master_id', $vinylMasterId)
            ->exists();
    }
    
    /**
     * Obter itens da wantlist que precisam ser verificados quanto à disponibilidade
     */
    public static function getItemsToCheck()
    {
        return static::with(['user', 'vinylMaster.vinylSecs'])
            ->where(function($query) {
                $query->where('notification_sent', false)
                    ->orWhereNull('last_notification_at')
                    ->orWhere('last_notification_at', '<=', now()->subDays(30));
            })
            ->get();
    }
}
