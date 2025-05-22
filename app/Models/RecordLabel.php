<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class RecordLabel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'website', 'logo'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($recordLabel) {
            $recordLabel->slug = Str::slug($recordLabel->name);
        });
    }

    public function vinylMasters()
    {
        return $this->hasMany(VinylMaster::class);
    }
    
    /**
     * Obter a URL do logo da gravadora
     * Compatível com imagens antigas e novas
     *
     * @return string|null URL da imagem ou null
     */
    public function getLogoUrlAttribute()
    {
        if (empty($this->logo)) {
            return null;
        }
        
        // Verificar se a imagem existe no storage
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->logo)) {
            return asset('storage/' . $this->logo);
        }
        
        // Se não existir, talvez seja um caminho relativo ou URL completa
        if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
            return $this->logo;
        }
        
        return asset('storage/' . $this->logo);
    }
}
