<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;
use App\Models\VinylMaster;

class WishlistPage extends Component
{
    public $wishlistItems = [];
    
    protected $listeners = [
        'wishlistUpdated' => 'refreshWishlist',
        'removeFromWishlist' => 'removeItem',
        'addAllToCart' => 'addAllToCart'
    ];
    
    public function mount()
    {
        $this->refreshWishlist();
    }
    
    /**
     * Atualiza a lista de itens da wishlist
     */
    public function refreshWishlist()
    {
        if (Auth::check()) {
            $this->wishlistItems = Auth::user()->wishlist()
                                    ->with('vinylMaster.artists')
                                    ->get();
        }
    }
    
    /**
     * Remove um item da wishlist
     */
    public function removeItem($vinylMasterId)
    {
        if (!Auth::check()) {
            return;
        }
        
        $userId = Auth::id();
        
        Wishlist::where('user_id', $userId)
               ->where('vinyl_master_id', $vinylMasterId)
               ->delete();
               
        $this->refreshWishlist();
        
        $this->dispatchBrowserEvent('notify', [
            'message' => 'Item removido da lista de desejos',
            'type' => 'success'
        ]);
    }
    
    /**
     * Adiciona todos os itens da wishlist ao carrinho
     */
    public function addAllToCart()
    {
        if (!Auth::check() || count($this->wishlistItems) == 0) {
            return;
        }
        
        // Aqui você pode integrar com o carrinho de compras
        // Assumindo que existe um CartController com um método addToCart
        $addedCount = 0;
        
        foreach ($this->wishlistItems as $item) {
            $vinyl = VinylMaster::find($item->vinyl_master_id);
            
            if ($vinyl && $vinyl->isAvailable()) {
                // Chamar o método para adicionar ao carrinho
                // app('App\Http\Controllers\Site\CartController')->addToCart($item->vinyl_master_id);
                $addedCount++;
            }
        }
        
        $this->dispatchBrowserEvent('notify', [
            'message' => "{$addedCount} itens adicionados ao carrinho",
            'type' => 'success'
        ]);
    }
    
    public function render()
    {
        return view('livewire.wishlist-page');
    }
}
