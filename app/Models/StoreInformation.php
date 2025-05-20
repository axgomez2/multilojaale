<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class StoreInformation extends Model
{
    use HasFactory;
    
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'document_type',
        'document',
        'address',
        'zipcode',
        'neighborhood',
        'state',
        'phone',
        'email',
        'logo_path',
        'favicon_path',
    ];
    
    /**
     * Obter a instância única da loja (singleton pattern)
     *
     * @return StoreInformation
     */
    public static function getInstance()
    {
        $store = self::first();
        
        if (!$store) {
            // Cria uma instância com valores padrão se não existir
            $store = self::create([
                'name' => config('app.name'),
                'document_type' => 'cnpj',
            ]);
        }
        
        return $store;
    }
    
    /**
     * Obter a URL completa do logo
     *
     * @return string|null
     */
    public function getLogoUrlAttribute()
    {
        if (!$this->logo_path) {
            return null;
        }
        
        return Storage::url($this->logo_path);
    }
    
    /**
     * Obter a URL completa do favicon
     *
     * @return string|null
     */
    public function getFaviconUrlAttribute()
    {
        if (!$this->favicon_path) {
            return null;
        }
        
        return Storage::url($this->favicon_path);
    }
}
