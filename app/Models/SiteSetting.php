<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'group',
        'description',
    ];

    /**
     * Obter um valor de configuração pelo nome da chave
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            return $setting->value;
        }
        
        return $default;
    }

    /**
     * Definir um valor de configuração pelo nome da chave
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @param string|null $description
     * @return bool
     */
    public static function set(string $key, $value, string $group = 'general', string $description = null)
    {
        $setting = self::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->group = $group;
        
        if ($description) {
            $setting->description = $description;
        }
        
        return $setting->save();
    }
}
