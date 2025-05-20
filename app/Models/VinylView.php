<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VinylView extends Model
{
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vinyl_master_id',
        'user_id',
        'ip_address',
        'viewed_at',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    /**
     * Obtém o disco relacionado a esta visualização.
     */
    public function vinyl()
    {
        return $this->belongsTo(VinylMaster::class, 'vinyl_master_id');
    }

    /**
     * Obtém o usuário relacionado a esta visualização, se houver.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Registra uma nova visualização para um disco.
     *
     * @param VinylMaster $vinyl O disco visualizado
     * @param User|null $user O usuário atual, se autenticado
     * @param string|null $ipAddress O endereço IP do usuário
     * @return VinylView
     */
    public static function recordView(VinylMaster $vinyl, $user = null, $ipAddress = null)
    {
        return static::create([
            'vinyl_master_id' => $vinyl->id,
            'user_id' => $user ? $user->id : null,
            'ip_address' => $ipAddress,
            'viewed_at' => now(),
        ]);
    }
}
