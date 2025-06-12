<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\VinylMaster;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Serviço que gerencia as operações do carrinho
     * @var \App\Services\CartService
     */
    protected $cartService;
    
    /**
     * Construtor do controlador
     */
    public function __construct(CartService $cartService)
    {
        // Removido temporariamente o middleware 'verified' para permitir testes
        // TODO: Restaurar o middleware 'verified' após os testes
        $this->middleware(['auth']);
        $this->cartService = $cartService;
    }
    
    /**
     * Exibe o carrinho de compras do usuário.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obter ou criar o carrinho ativo do usuário
        $cart = Cart::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();
            
        if (!$cart) {
            $cart = new Cart();
            $cart->user_id = $user->id;
            $cart->session_id = Session::getId();
            $cart->status = 'active';
            $cart->subtotal = 0;
            $cart->discount = 0;
            $cart->shipping_cost = 0;
            $cart->total = 0;
            $cart->save();
        }
        
        // Sincronizar itens do carrinho (garantir que todos os itens do usuário estão associados ao carrinho)
        CartItem::where('user_id', $user->id)
            ->where('saved_for_later', false)
            ->update(['cart_id' => $cart->id]);
        
        // Salvar o ID do carrinho ativo na sessão
        Session::put('active_cart_id', $cart->id);
        
        // Obter os itens do carrinho com seus relacionamentos
        $cartItems = $user->cartItems()
                          ->with([
                              'product.productable', // Nova estrutura de produtos
                              'vinylMaster.vinylSec', 'vinylMaster.artists' // Manter retrocompatibilidade
                          ])
                          ->where('saved_for_later', false)
                          ->get();
                          
        // Verificar o estoque de cada item e marcar os que não têm estoque suficiente
        foreach ($cartItems as $item) {
            $item->has_stock = $item->hasEnoughStock();
            
            // Registrar no log os itens sem estoque
            if (!$item->has_stock) {
                \Illuminate\Support\Facades\Log::info('Item do carrinho sem estoque suficiente', [
                    'cart_item_id' => $item->id,
                    'user_id' => $user->id,
                    'product_id' => $item->product_id,
                    'vinyl_master_id' => $item->vinyl_master_id,
                    'quantity' => $item->quantity
                ]);
            }
        }
        
        // Separar itens com e sem estoque
        $itemsWithStock = $cartItems->filter(function($item) {
            return $item->has_stock;
        });
        
        $itemsWithoutStock = $cartItems->filter(function($item) {
            return !$item->has_stock;
        });
        
        $savedItems = $user->cartItems()
                           ->with('vinylMaster.vinylSec', 'vinylMaster.artists')
                           ->where('saved_for_later', true)
                           ->get();
        
        // Calcular o total do carrinho (apenas itens com estoque)
        $cartTotal = $itemsWithStock->sum(function($item) {
            return $item->subtotal;
        });
        
        // Calcular o total dos itens sem estoque (para exibição separada)
        $outOfStockTotal = $itemsWithoutStock->sum(function($item) {
            return $item->subtotal;
        });
        
        // Remover qualquer informação de frete da sessão
        Session::forget(['shipping_options', 'selected_shipping', 'shipping_calculation']);
        
        // Obter produtos recomendados (até 3 produtos populares que não estão no carrinho)
        $recommendedVinyls = [];
        if (count($cartItems) > 0) {
            // Identificar produtos já no carrinho (tanto por vinyl_master_id quanto por product_id)
            $cartVinylIds = $cartItems->pluck('vinyl_master_id')->filter()->toArray();
            $cartProductIds = $cartItems->pluck('product_id')->filter()->toArray();
            
            // Buscar produtos recomendados (vinis)
            $recommendedVinyls = Product::with('productable')
                ->where('productable_type', 'App\\Models\\VinylMaster')
                ->whereNotIn('id', $cartProductIds) // Não mostrar produtos já no carrinho
                ->whereHas('productable', function($query) use ($cartVinylIds) {
                    $query->whereNotIn('id', $cartVinylIds) // Não mostrar vinis já no carrinho
                          ->whereHas('vinylSec', function($query) {
                              $query->where('stock', '>', 0); // Apenas produtos em estoque
                          });
                })
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
        }
        
        // Obter itens da wishlist do usuário
        $wishlistItems = $user->wishlist()->pluck('vinyl_master_id')->toArray();
        
        // Obter o valor do desconto
        $discount = $cart ? $cart->discount : 0;
        
        return view('site.cart.index', [
            'cartItems' => $itemsWithStock,
            'itemsWithoutStock' => $itemsWithoutStock,
            'savedItems' => $savedItems,
            'cartTotal' => $cartTotal,
            'outOfStockTotal' => $outOfStockTotal,
            'discount' => $discount,
            'recommendedVinyls' => $recommendedVinyls,
            'wishlistItems' => $wishlistItems,
        ]);
    }

/**
 * Adiciona um item ao carrinho de compras.
 */
public function store(Request $request)
{
    // Validar dados recebidos
    $validatedData = $request->validate([
        'vinyl_master_id' => 'required_without:product_id|exists:vinyl_masters,id',
        'product_id' => 'required_without:vinyl_master_id|exists:products,id',
        'quantity' => 'required|integer|min:1|max:10',
    ]);

    // Verificar qual tipo de produto está sendo adicionado
    if (isset($validatedData['product_id'])) {
        // Obtém o produto do banco de dados
        $product = Product::find($validatedData['product_id']);
        
        // Verificar disponibilidade
        if (!$product || !$product->is_available) {
            return $this->jsonResponse(false, 'Produto não está disponível.');
        }
        
        // Verificar estoque
        if (!$product->has_stock($validatedData['quantity'])) {
            return $this->jsonResponse(false, 'Quantidade solicitada excede o estoque disponível.');
        }
        
        // Se temos um produto polimórfico que é um VinylMaster, vamos definir o vinyl_master_id para compatibilidade
        if ($product->productable_type === 'App\\Models\\VinylMaster') {
            $validatedData['vinyl_master_id'] = $product->productable_id;
        }
    } else {
        // Fluxo legado usando vinyl_master_id
        $vinylMaster = VinylMaster::with('vinylSec')->find($validatedData['vinyl_master_id']);
        
        // Verificar disponibilidade
        if (!$vinylMaster || !$vinylMaster->isAvailable()) {
            return $this->jsonResponse(false, 'Disco não está disponível.');
        }
        
        // Verificar estoque
        if (!$vinylMaster->hasEnoughStock($validatedData['quantity'])) {
            return $this->jsonResponse(false, 'Quantidade solicitada excede o estoque disponível.');
        }
        
        // Buscar product_id correspondente para o vinyl_master_id
        $product = Product::where('productable_type', 'App\\Models\\VinylMaster')
                       ->where('productable_id', $validatedData['vinyl_master_id'])
                       ->first();
        
        if ($product) {
            $validatedData['product_id'] = $product->id;
        }
    }
    
    $user = Auth::user();
    
    // Encontrar ou criar um carrinho ativo para o usuário
    $cart = Cart::firstOrCreate(
        ['user_id' => $user->id, 'status' => 'active'],
        ['session_id' => Session::getId()]
    );
    
    // Verificar se o item já existe no carrinho
    $cartItem = null;
    
    // Primeiro tenta encontrar pelo product_id (nova estrutura)
    if (isset($validatedData['product_id'])) {
        $cartItem = CartItem::where('user_id', $user->id)
                        ->where('product_id', $validatedData['product_id'])
                        ->where('saved_for_later', false)
                        ->first();
    }
    
    // Se não encontrou pelo product_id, tenta pelo vinyl_master_id (compatibilidade)
    if (!$cartItem && isset($validatedData['vinyl_master_id'])) {
        $cartItem = CartItem::where('user_id', $user->id)
                        ->where('vinyl_master_id', $validatedData['vinyl_master_id'])
                        ->where('saved_for_later', false)
                        ->first();
    }
    
    if ($cartItem) {
        // Item já existe, atualizar apenas a quantidade
        $newQuantity = $cartItem->quantity + $validatedData['quantity'];
        
        // Validar novamente estoque com quantidade total
        if (isset($validatedData['product_id'])) {
            $product = Product::find($validatedData['product_id']);
            if (!$product->has_stock($newQuantity)) {
                return $this->jsonResponse(false, 'Quantidade total excede o estoque disponível.');
            }
        } else {
            $vinylMaster = VinylMaster::with('vinylSec')->find($validatedData['vinyl_master_id']);
            if (!$vinylMaster->hasEnoughStock($newQuantity)) {
                return $this->jsonResponse(false, 'Quantidade total excede o estoque disponível.');
            }
        }
        
        $cartItem->quantity = $newQuantity;
        
        // Garantir que tenha tanto product_id quanto vinyl_master_id se disponível
        if (isset($validatedData['product_id']) && empty($cartItem->product_id)) {
            $cartItem->product_id = $validatedData['product_id'];
        }
        if (isset($validatedData['vinyl_master_id']) && empty($cartItem->vinyl_master_id)) {
            $cartItem->vinyl_master_id = $validatedData['vinyl_master_id'];
        }
        
        $cartItem->cart_id = $cart->id;
        $cartItem->save();
    } else {
        // Criar um novo item no carrinho
        $cartItem = new CartItem();
        $cartItem->user_id = $user->id;
        $cartItem->cart_id = $cart->id;
        $cartItem->quantity = $validatedData['quantity'];
        $cartItem->saved_for_later = false;
        
        // Garantir que tenha tanto product_id quanto vinyl_master_id se disponível
        if (isset($validatedData['product_id'])) {
            $cartItem->product_id = $validatedData['product_id'];
        }
        if (isset($validatedData['vinyl_master_id'])) {
            $cartItem->vinyl_master_id = $validatedData['vinyl_master_id'];
        }
        
        $cartItem->save();
    }
    
    // Contar itens no carrinho para atualização do contador na UI
    $cartCount = CartItem::where('user_id', $user->id)
                       ->where('saved_for_later', false)
                       ->count();
    
    // Se for uma requisição AJAX, retorna JSON
    if ($request->expectsJson() || $request->ajax()) {
        return $this->jsonResponse(true, 'Item adicionado ao carrinho!', ['cartCount' => $cartCount]);
    }
    
    // Se for uma requisição regular, redireciona
    return redirect()->back()->with('success', 'Item adicionado ao carrinho!');
}

/**
 * Retorna uma resposta JSON padronizada
 */
private function jsonResponse(bool $success, string $message, array $data = [], int $status = 200)
{
    return response()->json(array_merge(
        ['success' => $success, 'message' => $message],
        $data
    ), $status);
}

/**
 * Redireciona o usuário para a página de frete
 */
public function moveToShipping()
{
    // Verifica se o carrinho tem itens
    $user = Auth::user();
    $cart = Cart::where('user_id', $user->id)
        ->where('status', 'active')
        ->first();
        
    if (!$cart || $cart->items->count() === 0) {
        return redirect()->route('site.cart.index')
            ->with('error', 'Seu carrinho está vazio. Adicione produtos antes de continuar.');
    }
    
    // Limpar a sessão de informações de frete anteriores
    Session::forget([
        'shipping_options', 
        'selected_shipping', 
        'shipping_calculation'
    ]);
    
    return redirect()->route('site.shipping.index');
}

/**
 * Atualiza a quantidade de um item no carrinho.
 */
public function update(Request $request, $id)
{
    $request->validate([
        'quantity' => 'required|integer|min:1|max:10',
    ]);
    
    $user = Auth::user();
    $cartItem = CartItem::where('user_id', $user->id)->findOrFail($id);
    $quantity = $request->quantity;
    
    // Verificar disponibilidade e estoque com base no tipo de produto
    // Primeiro tenta usar o sistema de produtos polimórficos
    if ($cartItem->product_id) {
        $product = $cartItem->product;
        
        // Verificar disponibilidade
        if (!$product || !$product->is_available) {
            return $this->jsonResponse(false, 'Produto não está mais disponível para compra.', [], 422);
        }
        
        // Verificar estoque
        if (!$product->has_stock($quantity)) {
            $availableStock = $product->stock ?? 0;
            return $this->jsonResponse(false, 
                "Quantidade solicitada não disponível em estoque. Disponível: {$availableStock}", 
                [], 
                422
            );
        }
    } 
    // Sistema legado (vinyl_master_id)
    else if ($cartItem->vinyl_master_id) {
        // Verificar se o vinil ainda está disponível
        if (!$cartItem->vinylMaster->isAvailable()) {
            return $this->jsonResponse(false, 'Este disco não está mais disponível para compra.', [], 422);
        }
        
        // Verificar se há estoque suficiente
        if ($cartItem->vinylMaster->vinylSec->stock < $quantity) {
            return $this->jsonResponse(false, 
                "Quantidade solicitada não disponível em estoque. Disponível: {$cartItem->vinylMaster->vinylSec->stock}", 
                [], 
                422
            );
        }
    }
    
    // Atualizar a quantidade
    $cartItem->update([
        'quantity' => $quantity
    ]);
    
    // Contar itens no carrinho para atualização do contador na UI
    $cartCount = CartItem::where('user_id', $user->id)
                       ->where('saved_for_later', false)
                       ->count();
    
    // Se for uma requisição AJAX, retorna JSON padronizado
    if ($request->expectsJson() || $request->ajax()) {
        return $this->jsonResponse(true, 'Quantidade atualizada', [
            'item' => [
                'id' => $cartItem->id,
                'quantity' => $cartItem->quantity,
                'subtotal' => $cartItem->subtotal,
            ],
            'cart_total' => $user->cart_total,
            'cartCount' => $cartCount
        ]);
    }
    
    // Se for uma requisição regular, redireciona
    return redirect()->back()->with('success', 'Quantidade atualizada');
}

/**
 * Remove um item do carrinho.
 */
public function destroy(Request $request, $id)
{
    $user = Auth::user();
    $cartItem = CartItem::where('user_id', $user->id)->findOrFail($id);
    
    $cartItem->delete();
    
    // Contar itens no carrinho para atualização do contador na UI
    $cartCount = CartItem::where('user_id', $user->id)
                       ->where('saved_for_later', false)
                       ->count();
                       
    // Se for uma requisição AJAX, retorna JSON padronizado
    if ($request->expectsJson() || $request->ajax()) {
        return $this->jsonResponse(true, 'Item removido do carrinho', [
            'cartCount' => $cartCount,
            'cart_total' => $user->cart_total
        ]);
    }
    
    // Se for uma requisição regular, redireciona
    return redirect()->back()->with('success', 'Item removido do carrinho');
}

/**
 * Limpa o carrinho completamente.
 */
// public function clear(Request $request)
// {
//     $user = Auth::user();
    
//     $user->cartItems()->delete();
    
//     // Se for uma requisição AJAX, retorna JSON padronizado
//     if ($request->expectsJson() || $request->ajax()) {
//         return $this->jsonResponse(true, 'Carrinho esvaziado');
//     }
    
//     // Se for uma requisição regular, redireciona
//     return redirect()->back()->with('success', 'Carrinho esvaziado');
// }

/**
 * Transfere todos os itens da wishlist para o carrinho.
 */
    public function addFromWishlist()
    {
        $user = Auth::user();
        $wishlistItems = $user->wishlist()->with('vinylMaster.vinylSec')->get();
        
        // Obter ou criar o carrinho ativo
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active'],
            [
                'session_id' => Session::getId(),
                'subtotal' => 0,
                'discount' => 0,
                'shipping_cost' => 0,
                'total' => 0
            ]
        );
        
        $addedCount = 0;
        $notAvailable = 0;
        
        foreach ($wishlistItems as $wishlistItem) {
            // Verificar disponibilidade
            if (!$wishlistItem->vinylMaster || !$wishlistItem->vinylMaster->isAvailable()) {
                $notAvailable++;
                continue;
            }
            
            // Adicionar ou atualizar item no carrinho
            CartItem::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'vinyl_master_id' => $wishlistItem->vinyl_master_id
                ],
                [
                    'cart_id' => $cart->id,
                    'price' => $wishlistItem->vinylMaster->vinylSec->price,
                    'quantity' => DB::raw("LEAST(quantity + 1, {$wishlistItem->vinylMaster->vinylSec->stock})")
                ]
            );
            
            // Remover da wishlist e incrementar contador
            $wishlistItem->delete();
            $addedCount++;
        }
        
        // Atualizar totais do carrinho
        $this->cartService->updateCartTotals($cart);
        
        // Preparar mensagem
        $message = $this->prepareTransferMessage($addedCount, $notAvailable);
        
        // Retornar resposta apropriada
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cart_count' => $user->cartItems()->sum('quantity')
            ]);
        }
        
        return redirect()->back()->with('success', $message);
    }

    // Método auxiliar para construir a mensagem
    private function prepareTransferMessage($addedCount, $notAvailable)
    {
        $message = "";
        if ($addedCount > 0) {
            $message = $addedCount . " " . ($addedCount == 1 ? 'item transferido' : 'itens transferidos') . " para o carrinho.";
        }
        if ($notAvailable > 0) {
            $message .= ($message ? " " : "") . $notAvailable . " " . ($notAvailable == 1 ? 'item não está' : 'itens não estão') . " disponível para compra.";
        }
        return $message ?: "Nenhum item foi transferido para o carrinho.";
    }
    public function saveForLater($id)
    {
        $user = Auth::user();
        $cartItem = CartItem::where('user_id', $user->id)->findOrFail($id);
        
        $cartItem->update([
            'saved_for_later' => true
        ]);
        
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item salvo para depois',
                'cart_count' => $user->cartItems()->where('saved_for_later', false)->sum('quantity')
            ]);
        }
        
        return redirect()->back()->with('success', 'Item salvo para depois');
    }
    
    /**
     * Mover um item da lista "Salvar para depois" de volta para o carrinho.
     */
    public function moveToCart($id)
    {
        $user = Auth::user();
        $cartItem = CartItem::where('user_id', $user->id)->findOrFail($id);
        
        // Verificar se o vinil ainda está disponível
        if (!$cartItem->vinylMaster->isAvailable()) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este vinil não está mais disponível para compra.'
                ], 422);
            }
            
            return redirect()->back()->with('error', 'Este vinil não está mais disponível para compra.');
        }
        
        // Verificar se há estoque suficiente
        if ($cartItem->vinylMaster->vinylSec->stock < $cartItem->quantity) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não há estoque suficiente para este item. Disponível: ' . $cartItem->vinylMaster->vinylSec->stock
                ], 422);
            }
            
            return redirect()->back()->with('error', 'Não há estoque suficiente para este item. Disponível: ' . $cartItem->vinylMaster->vinylSec->stock);
        }
        
        // Obter o carrinho ativo do usuário
        $cart = Cart::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();
            
        if (!$cart) {
            $cart = new Cart();
            $cart->user_id = $user->id;
            $cart->session_id = Session::getId();
            $cart->status = 'active';
            $cart->subtotal = 0;
            $cart->discount = 0;
            $cart->shipping_cost = 0;
            $cart->total = 0;
            $cart->save();
        }
        
        $cartItem->update([
            'saved_for_later' => false,
            'cart_id' => $cart->id
        ]);
        
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item movido para o carrinho',
                'cart_count' => $user->cartItems()->where('saved_for_later', false)->sum('quantity')
            ]);
        }
        
        return redirect()->back()->with('success', 'Item movido para o carrinho');
    }
    
    /**
     * Exibe os itens salvos para comprar depois.
     */
    public function savedItems()
    {
        $user = Auth::user();
        
        $savedItems = $user->cartItems()
                           ->with('vinylMaster.vinylSec', 'vinylMaster.artists')
                           ->where('saved_for_later', true)
                           ->get();
        
        return view('site.cart.saved', [
            'savedItems' => $savedItems
        ]);
    }
    
    // Método calculateShipping removido - agora está no ShippingController
    
    // Método getShippingOptions removido - agora está no ShippingController
    
    // Método selectShipping removido - agora está no ShippingController
    
    // Método createNewShippingQuote removido - agora está no ShippingController
}
