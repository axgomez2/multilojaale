<?php

namespace App\Services;

use App\Models\StoreInformation;
use Illuminate\Support\Facades\Cache;

class StoreService
{
    /**
     * Instância única do StoreInformation
     */
    protected $store;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        // Carregar a instância da loja do cache ou do banco de dados
        $this->store = Cache::remember('store_information', 60 * 24, function () {
            return StoreInformation::getInstance();
        });
    }
    
    /**
     * Obter instância da loja
     *
     * @return StoreInformation
     */
    public function getStore()
    {
        return $this->store;
    }
    
    /**
     * Obter um atributo específico da loja
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->store->{$key} ?? $default;
    }
    
    /**
     * Obter o nome da loja
     *
     * @return string
     */
    public function getName()
    {
        return $this->store->name;
    }
    
    /**
     * Obter a descrição da loja
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->store->description;
    }
    
    /**
     * Obter a URL do logo
     *
     * @return string|null
     */
    public function getLogoUrl()
    {
        return $this->store->logo_url;
    }
    
    /**
     * Obter a URL do favicon
     *
     * @return string|null
     */
    public function getFaviconUrl()
    {
        return $this->store->favicon_url;
    }
    
    /**
     * Limpar o cache da loja
     */
    public function clearCache()
    {
        Cache::forget('store_information');
        $this->store = StoreInformation::getInstance();
        
        return $this;
    }
}
