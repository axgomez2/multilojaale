<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\VinylMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SimpleCartController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Versão simplificada para adicionar um item ao carrinho.
     * Foca apenas no vinyl_master_id e ignora as verificações de produto.
     */
    public function addToCart(Request $request)
    {
        // Validar dados recebidos
        $validatedData = $request->validate([
            'vinyl_master_id' => 'required|exists:vinyl_masters,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        // Registrar para debug
        Log::info('Adicionando vinyl ao carrinho', [
            'vinyl_master_id' => $validatedData['vinyl_master_id'],
            'quantity' => $validatedData['quantity']
        ]);
        
        // Buscar o vinil
        $vinylMaster = VinylMaster::with('vinylSec')->find($validatedData['vinyl_master_id']);
        
        // Registrar para debug
        Log::info('VinylMaster encontrado', [
            'id' => $vinylMaster->id,
            'title' => $vinylMaster->title,
            'has_vinylSec' => $vinylMaster->vinylSec ? 'sim' : 'não',
            'in_stock' => $vinylMaster->vinylSec ? ($vinylMaster->vinylSec->in_stock ? 'sim' : 'não') : 'N/A',
            'stock' => $vinylMaster->vinylSec ? $vinylMaster->vinylSec->stock : 'N/A'
        ]);
        
        // Verificar disponibilidade - simplificado para garantir que funcione
        if (!$vinylMaster || !$vinylMaster->vinylSec) {
            return response()->json([
                'success' => false, 
                'message' => 'Disco não encontrado ou sem informações de estoque.'
            ]);
        }
        
        // Simplificando a verificação de estoque
        if ($vinylMaster->vinylSec->stock <= 0) {
            return response()->json([
                'success' => false, 
                'message' => 'Disco sem estoque disponível.'
            ]);
        }
        
        $user = Auth::user();
        
        // Encontrar ou criar um carrinho ativo para o usuário
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active'],
            ['session_id' => Session::getId()]
        );
        
        // Verificar se o item já existe no carrinho (independente de estar salvo para depois ou não)
        $existingCartItem = CartItem::where('user_id', $user->id)
                        ->where('vinyl_master_id', $validatedData['vinyl_master_id'])
                        ->first();
        
        if ($existingCartItem) {
            // Item já existe no carrinho
            if ($existingCartItem->saved_for_later) {
                // Item está salvo para depois, mover para o carrinho ativo
                $existingCartItem->saved_for_later = false;
                $existingCartItem->cart_id = $cart->id;
                
                // Validar estoque
                if ($validatedData['quantity'] > $vinylMaster->vinylSec->stock) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Quantidade solicitada excede o estoque disponível.'
                    ]);
                }
                
                $existingCartItem->quantity = $validatedData['quantity'];
                $existingCartItem->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Item movido de "Salvo para depois" para o carrinho!',
                    'cartCount' => CartItem::where('user_id', $user->id)->where('saved_for_later', false)->count()
                ]);
            } else {
                // Item já está no carrinho ativo, atualizar apenas a quantidade
                $newQuantity = $existingCartItem->quantity + $validatedData['quantity'];
                
                // Validar novamente estoque com quantidade total
                if ($newQuantity > $vinylMaster->vinylSec->stock) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Quantidade total excede o estoque disponível.'
                    ]);
                }
                
                $existingCartItem->quantity = $newQuantity;
                $existingCartItem->cart_id = $cart->id;
                $existingCartItem->save();
                
                // Contar itens no carrinho para atualização do contador na UI
                $cartCount = CartItem::where('user_id', $user->id)
                               ->where('saved_for_later', false)
                               ->count();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Quantidade atualizada no carrinho!',
                    'cartCount' => $cartCount
                ]);
            }
        } else {
            // Criar um novo item no carrinho
            $cartItem = new CartItem();
            $cartItem->user_id = $user->id;
            $cartItem->cart_id = $cart->id;
            $cartItem->quantity = $validatedData['quantity'];
            $cartItem->saved_for_later = false;
            $cartItem->vinyl_master_id = $validatedData['vinyl_master_id'];
            $cartItem->save();
            
            // Contar itens no carrinho para atualização do contador na UI
            $cartCount = CartItem::where('user_id', $user->id)
                           ->where('saved_for_later', false)
                           ->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Item adicionado ao carrinho!',
                'cartCount' => $cartCount
            ]);
        }
    }
}
