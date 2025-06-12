<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\VinylMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Obter o carrinho atual do usuário ou sessão.
     *
     * @return Cart|null
     */
    public function getCurrentCart()
    {
        $userId = Auth::id();
        $sessionId = Session::getId();
        
        $cart = null;
        
        // Buscar o carrinho do usuário logado
        if ($userId) {
            $cart = Cart::where('user_id', $userId)
                ->where('status', 'active')
                ->first();
        }
        
        // Se não encontrou pelo user_id, tentar pelo session_id
        if (!$cart && $sessionId) {
            $cart = Cart::where('session_id', $sessionId)
                ->where('status', 'active')
                ->first();
        }
        
        // Se ainda não encontrou, criar um novo carrinho
        if (!$cart) {
            $cart = new Cart();
            $cart->user_id = $userId;
            $cart->session_id = $sessionId;
            $cart->status = 'active';
            $cart->subtotal = 0;
            $cart->discount = 0;
            $cart->shipping_cost = 0;
            $cart->total = 0;
            $cart->save();
        }
        
        return $cart;
    }
    
    /**
     * Obter apenas os itens ativos do carrinho (não salvos para depois).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveItems()
    {
        $cart = $this->getCurrentCart();
        
        if (!$cart) {
            return collect([]);
        }
        
        // Retornar apenas os itens que não estão salvos para depois
        return CartItem::where('cart_id', $cart->id)
            ->where('saved_for_later', false)
            ->with(['product.productable', 'vinylMaster.vinylSec', 'vinylMaster.artists'])
            ->get();
    }
    
    /**
     * Adicionar um produto ao carrinho.
     *
     * @param string $vinylId
     * @param int $quantity
     * @return CartItem|null
     */
    public function addItem($vinylId, $quantity = 1)
    {
        $cart = $this->getCurrentCart();
        $vinyl = VinylMaster::find($vinylId);
        
        if (!$vinyl || $quantity <= 0) {
            return null;
        }
        
        // Verificar se o item já existe no carrinho
        $cartItem = $cart->items()->where('vinyl_id', $vinylId)->first();
        
        if ($cartItem) {
            // Atualizar a quantidade
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Criar um novo item no carrinho
            $cartItem = new CartItem();
            $cartItem->cart_id = $cart->id;
            $cartItem->vinyl_id = $vinylId;
            $cartItem->quantity = $quantity;
            $cartItem->saved_for_later = false;
            $cartItem->save();
        }
        
        // Atualizar os totais do carrinho
        $this->updateCartTotals($cart);
        
        return $cartItem;
    }
    
    /**
     * Remover um item do carrinho.
     *
     * @param string $itemId
     * @return bool
     */
    public function removeItem($itemId)
    {
        $cart = $this->getCurrentCart();
        $cartItem = $cart->items()->where('id', $itemId)->first();
        
        if (!$cartItem) {
            return false;
        }
        
        $cartItem->delete();
        
        // Atualizar os totais do carrinho
        $this->updateCartTotals($cart);
        
        return true;
    }
    
    /**
     * Atualizar a quantidade de um item no carrinho.
     *
     * @param string $itemId
     * @param int $quantity
     * @return CartItem|null
     */
    public function updateItemQuantity($itemId, $quantity)
    {
        $cart = $this->getCurrentCart();
        $cartItem = $cart->items()->where('id', $itemId)->first();
        
        if (!$cartItem || $quantity <= 0) {
            return null;
        }
        
        $cartItem->quantity = $quantity;
        $cartItem->save();
        
        // Atualizar os totais do carrinho
        $this->updateCartTotals($cart);
        
        return $cartItem;
    }
    
    /**
     * Mover um item para "salvar para depois".
     *
     * @param string $itemId
     * @return CartItem|null
     */
    public function saveItemForLater($itemId)
    {
        $cart = $this->getCurrentCart();
        $cartItem = $cart->items()->where('id', $itemId)->first();
        
        if (!$cartItem) {
            return null;
        }
        
        $cartItem->saved_for_later = true;
        $cartItem->save();
        
        // Atualizar os totais do carrinho
        $this->updateCartTotals($cart);
        
        return $cartItem;
    }
    
    /**
     * Mover um item de "salvar para depois" para o carrinho ativo.
     *
     * @param string $itemId
     * @return CartItem|null
     */
    public function moveToCart($itemId)
    {
        $cart = $this->getCurrentCart();
        $cartItem = $cart->items()->where('id', $itemId)->first();
        
        if (!$cartItem) {
            return null;
        }
        
        $cartItem->saved_for_later = false;
        $cartItem->save();
        
        // Atualizar os totais do carrinho
        $this->updateCartTotals($cart);
        
        return $cartItem;
    }
    
    /**
     * Limpar o carrinho completamente.
     *
     * @return bool
     */
    public function clearCart()
    {
        $cart = $this->getCurrentCart();
        
        // Remover todos os itens do carrinho
        $cart->items()->delete();
        
        // Atualizar os totais do carrinho
        $cart->subtotal = 0;
        $cart->discount = 0;
        $cart->shipping_cost = 0;
        $cart->total = 0;
        $cart->save();
        
        return true;
    }
    
    /**
     * Atualizar os totais do carrinho.
     *
     * @param Cart $cart
     * @return void
     */
    private function updateCartTotals($cart)
    {
        // Recalcular os totais apenas com os itens ativos (não salvos para depois)
        $subtotal = $cart->items()
            ->where('saved_for_later', false)
            ->get()
            ->sum(function ($item) {
                return $item->quantity * $item->vinylMaster->vinylSec->price;
            });
        
        $cart->subtotal = $subtotal;
        $cart->total = $subtotal - $cart->discount + $cart->shipping_cost;
        $cart->save();
    }
}
