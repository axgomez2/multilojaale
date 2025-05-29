<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Models\UserPermission;
use App\Models\Address;
use App\Models\Wishlist;
use App\Models\Wantlist;
use App\Models\CartItem;
use App\Models\Order;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasUuids, Notifiable;
    
    /**
     * Envia a notificação de verificação de email personalizada.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }
    
    /**
     * Envia a notificação de recuperação de senha personalizada.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

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
     * Obter a wishlist do usuário
     */
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class, 'user_id', 'id');
    }
    
    /**
     * Obtém os itens da lista de interesse (Wantlist) do usuário
     */
    public function wantlist()
    {
        return $this->hasMany(Wantlist::class, 'user_id', 'id');
    }
    
    /**
     * Verifica se um vinil específico está na lista de desejos do usuário
     */
    public function hasInWishlist($vinylMasterId)
    {
        return Wishlist::hasItem($this->id, $vinylMasterId);
    }
    
    /**
     * Verifica se um vinil específico está na lista de interesse do usuário
     */
    public function hasInWantlist($vinylMasterId)
    {
        return Wantlist::hasItem($this->id, $vinylMasterId);
    }
    
    /**
     * Obtém os itens do carrinho de compras do usuário
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    
    /**
     * Verifica se um vinil específico está no carrinho do usuário
     */
    public function hasInCart($vinylMasterId)
    {
        return $this->cartItems()->where('vinyl_master_id', $vinylMasterId)->exists();
    }
    
    /**
     * Calcula o total do carrinho do usuário
     */
    public function getCartTotalAttribute()
    {
        return $this->cartItems()->with('vinylMaster.vinylSec')->get()->sum(function($item) {
            return $item->vinylMaster->vinylSec->price * $item->quantity;
        });
    }
    
    /**
     * Verificar se o email do usuário foi verificado
     */
    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null;
    }
    
    /**
     * Obtém os pedidos do usuário
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
