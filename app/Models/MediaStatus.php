<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaStatus extends Model
{
    use HasFactory;
    
    /**
     * A tabela associada ao modelo
     *
     * @var string
     */
    protected $table = 'media_statuses';
    
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color_code'
    ];
    
    /**
     * Retorna os discos (vinyl_secs) com este status de mídia
     */
    public function vinylSecs()
    {
        return $this->hasMany(VinylSec::class, 'media_status_id');
    }
}
