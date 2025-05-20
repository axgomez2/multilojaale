<?php

namespace App\Traits;

use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

trait HasWishlist
{
    public function wishlists()
    {
        return $this->morphMany(Wishlist::class, 'product');
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
            ->where('is_wantlist', false)
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
        
        return $this->wishlists()
            ->where('user_id', Auth::id())
            ->where('is_wantlist', true)
            ->exists();
    }
}

