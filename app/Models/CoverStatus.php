<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoverStatus extends Model
{
    use HasFactory;
    
    /**
     * A tabela associada ao modelo
     *
     * @var string
     */
    protected $table = 'cover_status';
    
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'color_code'
    ];
    
    /**
     * Retorna os discos (vinyl_secs) com este status de capa
     */
    public function vinylSecs()
    {
        return $this->hasMany(VinylSec::class, 'cover_status_id');
    }
}
