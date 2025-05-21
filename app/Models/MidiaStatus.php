<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MidiaStatus extends Model
{
    use HasFactory;
    
    /**
     * A tabela associada ao modelo
     *
     * @var string
     */
    protected $table = 'midia_status';
    
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description'
    ];
        
    /**
     * Retorna os discos (vinyl_secs) com este status de mídia
     */
    public function vinylSecs()
    {
        return $this->hasMany(VinylSec::class, 'midia_status_id');
    }
}
