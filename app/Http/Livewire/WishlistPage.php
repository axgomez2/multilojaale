<?php

namespace App\Http\Livewire;

use App\Models\CartItem;
use App\Models\Wishlist;
use App\Models\VinylMaster;
use Livewire\Component;

class WishlistPage extends Component
{
    public $wishlistItems = [];
    protected $listeners = ['refreshWishlist' => '$refresh', 'addToCart' => 'addItemToCart'];

    public function mount()
    {
        $this->loadWishlistItems();
    }

    public function loadWishlistItems()
    {
        if (auth()->check()) {
            $this->wishlistItems = Wishlist::where('user_id', auth()->id())
                ->with(['vinylMaster.vinylSec', 'vinylMaster.artists'])
                ->get();
        }
    }

    public function removeItem($vinylMasterId)
    {
        if (auth()->check()) {
            Wishlist::where('user_id', auth()->id())
                ->where('vinyl_master_id', $vinylMasterId)
                ->delete();
                
            $this->loadWishlistItems();
            $this->emit('wishlistUpdated');
            
            $this->dispatchBrowserEvent('notify', [
                'type' => 'success', 
                'message' => 'Item removido da lista de desejos'
            ]);
        }
    }
    
    public function addItemToCart($vinylMasterId)
    {
        if (!auth()->check()) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error', 
                'message' => 'Você precisa estar logado para adicionar itens ao carrinho'
            ]);
            return;
        }
        
        $vinylMaster = VinylMaster::with('vinylSec')->find($vinylMasterId);
        
        if (!$vinylMaster || !$vinylMaster->isAvailable()) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error', 
                'message' => 'Este vinil não está disponível para compra no momento'
            ]);
            return;
        }
        
        $userId = auth()->id();
        
        // Verificar se o item já está no carrinho
        $existingItem = CartItem::where('user_id', $userId)
                             ->where('vinyl_master_id', $vinylMasterId)
                             ->first();
        
        if ($existingItem) {
            // Atualizar a quantidade (limitado ao estoque disponível)
            $newQuantity = min($existingItem->quantity + 1, $vinylMaster->vinylSec->stock);
            
            $existingItem->update([
                'quantity' => $newQuantity
            ]);
            
            $message = 'Quantidade atualizada no carrinho';
        } else {
            // Adicionar novo item ao carrinho
            CartItem::create([
                'user_id' => $userId,
                'vinyl_master_id' => $vinylMasterId,
                'quantity' => 1
            ]);
            
            $message = 'Item adicionado ao carrinho';
        }
        
        $this->dispatchBrowserEvent('notify', [
            'type' => 'success', 
            'message' => $message
        ]);
        
        $this->emit('cartUpdated');
    }
    
    public function addAllToCart()
    {
        if (!auth()->check()) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error', 
                'message' => 'Você precisa estar logado para adicionar itens ao carrinho'
            ]);
            return;
        }
        
        $addedCount = 0;
        $notAvailableCount = 0;
        
        foreach ($this->wishlistItems as $item) {
            if ($item->vinylMaster && $item->vinylMaster->isAvailable()) {
                // Verificar se o item já está no carrinho
                $existingItem = CartItem::where('user_id', auth()->id())
                                     ->where('vinyl_master_id', $item->vinyl_master_id)
                                     ->first();
                
                if ($existingItem) {
                    // Atualizar a quantidade (limitado ao estoque disponível)
                    $newQuantity = min($existingItem->quantity + 1, $item->vinylMaster->vinylSec->stock);
                    
                    $existingItem->update([
                        'quantity' => $newQuantity
                    ]);
                } else {
                    // Adicionar novo item ao carrinho
                    CartItem::create([
                        'user_id' => auth()->id(),
                        'vinyl_master_id' => $item->vinyl_master_id,
                        'quantity' => 1
                    ]);
                }
                
                $addedCount++;
            } else {
                $notAvailableCount++;
            }
        }
        
        $message = '';
        
        if ($addedCount > 0) {
            $message = $addedCount . ' ' . ($addedCount == 1 ? 'item adicionado' : 'itens adicionados') . ' ao carrinho';
        }
        
        if ($notAvailableCount > 0) {
            $message .= ($addedCount > 0 ? '. ' : '') . $notAvailableCount . ' ' . ($notAvailableCount == 1 ? 'item não estava disponível' : 'itens não estavam disponíveis');
        }
        
        if ($addedCount == 0 && $notAvailableCount == 0) {
            $message = 'Nenhum item para adicionar ao carrinho';
        }
        
        $this->dispatchBrowserEvent('notify', [
            'type' => $addedCount > 0 ? 'success' : 'warning', 
            'message' => $message
        ]);
        
        $this->emit('cartUpdated');
    }

    public function render()
    {
        return view('livewire.wishlist-page');
    }
}
