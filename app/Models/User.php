<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Models\UserPermission;
use App\Models\Address;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'role',
        'phone',
        'cpf',
        'birth_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'workos_id',
        'remember_token',
    ];

    /**
     * Get the user's initials.
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'integer',
            'birth_date' => 'date',
        ];
    }
    
    /**
     * Verifica se o usuário é um administrador
     */
    public function isAdmin(): bool
    {
        // Log para debug
        \Log::info('Verificando permissão de administrador', [
            'user_id' => $this->id,
            'role' => $this->role,
            'role_type' => gettype($this->role)
        ]);
        
        // Verificando por valor numérico (66) ou string ('66')
        return $this->role == 66 || $this->role === 'admin';
    }
    
    /**
     * Verifica se o usuário é um usuário comum
     */
    public function isUser(): bool
    {
        return $this->role === 20;
    }
    
    /**
     * Obtém as permissões do usuário
     */
    public function permission()
    {
        return $this->hasOne(UserPermission::class);
    }
    
    /**
     * Verifica se o usuário é um desenvolvedor
     */
    public function isDeveloper(): bool
    {
        return $this->permission && $this->permission->is_developer;
    }
    
    /**
     * Obtém os endereços do usuário
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    
    /**
     * Obtém o endereço padrão do usuário
     */
    public function defaultAddress()
    {
        return $this->hasOne(Address::class)->where('is_default', true)->where('is_active', true);
    }
    
    /**
     * Verifica se o email do usuário foi verificado
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }
}
