<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Artist extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'discogs_id',
        'profile',
        'images',
        'discogs_url'
    ];

    protected $casts = [
        'images' => 'array',
        'discogs_id' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($artist) {
            if (empty($artist->slug)) {
                $artist->slug = Str::slug($artist->name);
            }
        });
    }

    public function vinylMasters()
    {
        return $this->belongsToMany(VinylMaster::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
    
    /**
     * Obter a URL da imagem do artista
     * Compatível com imagens antigas e novas
     *
     * @return string|null URL da imagem ou null
     */
    public function getImageUrlAttribute()
    {
        // Verificar o campo 'image'
        if (!empty($this->image)) {
            // Verificar se a imagem existe no storage
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->image)) {
                return asset('storage/' . $this->image);
            }
            
            // Se não existir, talvez seja um caminho relativo ou URL completa
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }
            
            return asset('storage/' . $this->image);
        }
        
        // Verificar o array 'images' (formato antigo)
        if (!empty($this->images) && is_array($this->images) && isset($this->images[0])) {
            $firstImage = $this->images[0];
            
            // Se for um caminho completo ou URL
            if (filter_var($firstImage, FILTER_VALIDATE_URL)) {
                return $firstImage;
            }
            
            // Se for um caminho de storage
            return asset('storage/' . $firstImage);
        }
        
        return null;
    }
}
