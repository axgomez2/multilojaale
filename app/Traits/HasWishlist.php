<?php

namespace App\Traits;

use App\Models\Wishlist;
use App\Models\Wantlist;
use Illuminate\Support\Facades\Auth;

trait HasWishlist
{
    /**
     * Relacionamento com a lista de desejos (para produtos disponíveis)
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'vinyl_master_id');
    }
    
    /**
     * Relacionamento com a lista de interesse (para produtos indisponíveis)
     */
    public function wantlists()
    {
        return $this->hasMany(Wantlist::class, 'vinyl_master_id');
    }

    /**
     * Verifica se o produto está na wishlist do usuário atual
     * Usado para produtos disponíveis
     * 
     * @return bool
     */
    public function inWishlist()
    {
        if (!Auth::check()) {
            return false;
        }

        return $this->wishlists()
            ->where('user_id', Auth::id())
            ->exists();
    }
    
    /**
     * Verifica se o produto está na wantlist do usuário atual
     * Usado para produtos indisponíveis
     * 
     * @return bool
     */
    public function inWantlist()
    {
        if (!Auth::check()) {
            return false;
        }
        
        return $this->wantlists()
            ->where('user_id', Auth::id())
            ->exists();
    }
}

