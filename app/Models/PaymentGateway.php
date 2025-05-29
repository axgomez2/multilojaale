<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'name',
        'code',
        'active',
        'sandbox_mode',
        'credentials',
        'settings',
    ];
    
    protected $casts = [
        'active' => 'boolean',
        'sandbox_mode' => 'boolean',
        'credentials' => 'array', // Removido 'encrypted:' para evitar problemas com a criptografia
        'settings' => 'array',
    ];
    
    /**
     * The attributes that should be encrypted/decrypted.
     *
     * @var array
     */
    protected $encryptable = [
        'credentials',
    ];
    
    /**
     * Set an attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable) && !is_null($value)) {
            $value = encrypt(json_encode($value));
        }
        
        return parent::setAttribute($key, $value);
    }
    
    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        
        if (in_array($key, $this->encryptable) && !is_null($value)) {
            try {
                return json_decode(decrypt($value), true);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                return [];
            }
        }
        
        return $value;
    }
    
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        });
    }
    
    /**
     * Get the API token for the payment gateway.
     *
     * @return string|null
     */
    public function getApiToken()
    {
        $credentials = $this->credentials;
        
        if ($this->code === 'mercadopago') {
            return $credentials['access_token'] ?? null;
        } elseif ($this->code === 'pagseguro') {
            return $credentials['token'] ?? null;
        } elseif ($this->code === 'rede') {
            return $credentials['token'] ?? null;
        }
        
        return null;
    }
    
    /**
     * Get the available payment methods for this gateway.
     */
    public function getAvailableMethods(): array
    {
        if (!isset($this->settings['available_methods'])) {
            return [];
        }
        
        return $this->settings['available_methods'];
    }
    
    /**
     * Check if a specific payment method is available.
     */
    public function isMethodAvailable(string $method): bool
    {
        return in_array($method, $this->getAvailableMethods());
    }
    
    /**
     * Get API key from credentials.
     */
    public function getApiKey(): ?string
    {
        if (!isset($this->credentials['api_key'])) {
            return null;
        }
        
        return $this->credentials['api_key'];
    }
    
    // O método getApiToken já está definido acima
    
    /**
     * Get API secret from credentials.
     */
    public function getApiSecret(): ?string
    {
        if (!isset($this->credentials['api_secret'])) {
            return null;
        }
        
        return $this->credentials['api_secret'];
    }
    
    /**
     * Scope a query to only include active gateways.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
