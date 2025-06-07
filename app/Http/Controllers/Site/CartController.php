<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\VinylMaster;
use App\Services\MelhorEnvio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * @var \App\Services\CartService
     */
    protected $cartService;
    
    /**
     * Construtor do controlador.
     */
    public function __construct(\App\Services\CartService $cartService)
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
        
        $savedItems = $user->cartItems()
                           ->with('vinylMaster.vinylSec', 'vinylMaster.artists')
                           ->where('saved_for_later', true)
                           ->get();
        
        // Calcular o total do carrinho (apenas itens ativos)
        $cartTotal = $cartItems->sum(function($item) {
            return $item->subtotal;
        });
        
        // Verificar se há um CEP e opções de frete na sessão
        $zipCode = Session::get('shipping_zip_code');
        
        // Se não há CEP na sessão, tentar usar o endereço padrão de entrega do usuário
        if (!$zipCode) {
            $defaultAddress = $user->addresses()
                ->where('is_default_shipping', true)
                ->first();
                
            if ($defaultAddress) {
                $zipCode = preg_replace('/\D/', '', $defaultAddress->zipcode);
                
                // Se temos o endereço padrão, calcular o frete automaticamente?
                // Vamos apenas armazenar o CEP na sessão para pré-preencher o campo
                Session::put('shipping_zip_code', $zipCode);
            }
        }
        
        $shippingOptions = Session::get('shipping_options');
        $selectedShipping = Session::get('selected_shipping');
        
        // Garantir que $selectedShipping tenha todas as chaves necessárias
        if ($selectedShipping) {
            // Aplicar valores padrão para chaves que possam estar faltando
            $defaults = [
                'id' => '',
                'name' => 'Frete selecionado',
                'price' => 0,
                'delivery_time' => 5,
                'delivery_estimate' => 'Prazo a calcular',
                'company_id' => 0,
                'company_name' => '',
            ];
            
            // Combinamos os valores padrão com os valores existentes em $selectedShipping
            foreach ($defaults as $key => $value) {
                if (!isset($selectedShipping[$key])) {
                    $selectedShipping[$key] = $value;
                }
            }
            
            // Atualizar na sessão
            Session::put('selected_shipping', $selectedShipping);
        }
        
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
        
        // Obter o valor do desconto (se houver)
        $discount = $cart ? $cart->discount : 0;
        
        // Verificar se há mensagem de cálculo de frete na sessão
        $shippingCalculation = Session::get('shipping_calculation');
        
        // Se a página está sendo carregada após o cálculo de frete, mas não há opções disponíveis
        // vamos verificar o motivo
        if (Session::has('shipping_calculation') && empty($shippingOptions)) {
            // Se for um cálculo bem-sucedido mas sem opções, isso é estranho
            if (Session::get('shipping_calculation.success')) {
                // Forçar a recuperação das opções da sessão novamente
                $shippingOptions = Session::get('shipping_options', []);
            }
        }
        
        // Se o usuário tiver um endereço padrão, vamos recuperá-lo para preencher o CEP
        $defaultAddress = $user->addresses()
            ->where('is_default_shipping', true)
            ->first();
            
        if ($defaultAddress) {
            $zipCode = preg_replace('/\D/', '', $defaultAddress->zipcode);
            
            // Se temos o endereço padrão, calcular o frete automaticamente?
            // Vamos apenas armazenar o CEP na sessão para pré-preencher o campo
            Session::put('shipping_zip_code', $zipCode);
        }
        
        $shippingOptions = Session::get('shipping_options');
        $selectedShipping = Session::get('selected_shipping');
        
        // Garantir que $selectedShipping tenha todas as chaves necessárias
        if ($selectedShipping) {
            // Aplicar valores padrão para chaves que possam estar faltando
            $defaults = [
                'id' => '',
                'name' => 'Frete selecionado',
                'price' => 0,
                'delivery_time' => 5,
                'delivery_estimate' => 'Prazo a calcular',
                'company_id' => 0,
                'company_name' => '',
            ];
            
            // Combinamos os valores padrão com os valores existentes em $selectedShipping
            foreach ($defaults as $key => $value) {
                if (!isset($selectedShipping[$key])) {
                    $selectedShipping[$key] = $value;
                }
            }
            
            // Atualizar na sessão
            Session::put('selected_shipping', $selectedShipping);
        }
        
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
        
        // Obter o valor do desconto (se houver)
        $discount = $cart ? $cart->discount : 0;
        
        // Verificar se há mensagem de cálculo de frete na sessão
        $shippingCalculation = Session::get('shipping_calculation');
        
        // Se a página está sendo carregada após o cálculo de frete, mas não há opções disponíveis
        // vamos verificar o motivo
        if (Session::has('shipping_calculation') && empty($shippingOptions)) {
            // Se for um cálculo bem-sucedido mas sem opções, isso é estranho
            if (Session::get('shipping_calculation.success')) {
                // Forçar a recuperação das opções da sessão novamente
                $shippingOptions = Session::get('shipping_options', []);
            }
        }
        
        return view('site.cart.index', [
            'cartItems' => $cartItems,
            'savedItems' => $savedItems,
            'cartTotal' => $cartTotal,
            'zipCode' => $zipCode,
            'shippingOptions' => $shippingOptions,
            'selectedShipping' => $selectedShipping,
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
private function jsonResponse(bool $success, string $message, array $data = [])
{
    return response()->json(array_merge(
        ['success' => $success, 'message' => $message],
        $data
    ));
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
    
    /**
     * Calcula o frete para os itens no carrinho
     */
    public function calculateShipping(Request $request)
    {
        $request->validate([
            'zip_code' => 'required|string',
        ]);
        
        // Remover qualquer caractere não numérico do CEP
        $zipCode = preg_replace('/\D/', '', $request->zip_code);
        
        // Validar se o CEP possui 8 dígitos
        if (strlen($zipCode) !== 8) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'O CEP deve conter 8 dígitos.'
                ], 422);
            }
            
            return redirect()->back()->with('error', 'O CEP deve conter 8 dígitos.');
        }
        
        // Variável zipCode já definida acima com a limpeza
        $user = Auth::user();
        
        // Obter os itens do carrinho
        $cartItems = $user->cartItems()
                          ->with('vinylMaster.vinylSec')
                          ->where('saved_for_later', false)
                          ->get();
        
        if ($cartItems->isEmpty()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seu carrinho está vazio.'
                ], 422);
            }
            
            return redirect()->back()->with('error', 'Seu carrinho está vazio.');
        }
        
        // Calcular o peso total e valor total dos itens
        $totalWeight = 0;
        $totalValue = 0;
        
        foreach ($cartItems as $item) {
            // Verificar se o peso existe e acessar seu valor
            if ($item->vinylMaster->vinylSec->weight) {
                $totalWeight += $item->vinylMaster->vinylSec->weight->value * $item->quantity;
            } else {
                // Usar um peso padrão caso não exista (ex: 250g para um disco de vinil)
                $totalWeight += 0.25 * $item->quantity; // 250g em kg
            }
            $totalValue += $item->subtotal;
        }
        
        // Chamar o serviço de cálculo de frete
        $shippingService = new MelhorEnvio();
        
        // Preparar os itens do carrinho no formato esperado pelo MelhorEnvio
        $preparedItems = [];
        foreach ($cartItems as $item) {
            $preparedItems[] = [
                'id' => $item->vinylMaster->id,
                'quantity' => $item->quantity
            ];
        }
        
        // Chamar o serviço MelhorEnvio para calcular o frete
        $shippingOptions = $shippingService->calculateShipping($zipCode, $preparedItems);
        
        if (!$shippingOptions['success']) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $shippingOptions['message'] ?? 'Erro ao calcular o frete.'
                ], 422);
            }
            
            return redirect()->back()->with('error', $shippingOptions['message'] ?? 'Erro ao calcular o frete.');
        }
        
        // Salvar o CEP e as opções de frete na sessão
        Session::put('shipping_zip_code', $zipCode);
        Session::put('shipping_options', $shippingOptions['options']);
        
        // Adicionar os dados de cálculo de frete na sessão com a estrutura que a view espera
        Session::put('shipping_calculation', [
            'success' => true,
            'message' => 'Frete calculado com sucesso'
        ]);
        
        // Gerar um token de cotação de frete e salvar no banco de dados
        $quoteToken = \Illuminate\Support\Str::uuid()->toString();
        Session::put('shipping_quote_token', $quoteToken);
        
        // Salvar as informações da cotação no banco de dados
        $userId = auth()->check() ? auth()->id() : null;
        $sessionId = Session::getId();
        
        // Obter itens do carrinho para calcular o hash
        $cart = $this->cartService->getCurrentCart();
        $cartItems = $cart->items()->where('saved_for_later', false)->get();
        $cartItemsData = $cartItems->map(function($item) {
            return [
                'id' => $item->id,
                'vinyl_id' => $item->vinyl_id,
                'quantity' => $item->quantity
            ];
        })->toArray();
        
        // Criar hash dos itens do carrinho para verificar se o conteúdo mudou
        $cartItemsHash = md5(json_encode($cartItemsData));
        
        // Salvar ou atualizar a cotação no banco de dados
        $shippingQuote = \App\Models\ShippingQuote::updateOrCreate(
            ['quote_token' => $quoteToken],
            [
                'user_id' => $userId,
                'session_id' => $sessionId,
                'cart_items_hash' => $cartItemsHash,
                'cart_items' => json_encode($cartItemsData),
                'zip_from' => config('shipping.zip_from', '09220360'), // Valor padrão caso a configuração seja nula
                'zip_to' => $zipCode,
                'products' => json_encode($preparedItems),
                'api_response' => json_encode($shippingOptions),
                'options' => json_encode($shippingOptions['options']),
                'expires_at' => now()->addDays(1)
            ]
        );
        
        \Log::info('Cotação de frete salva no banco de dados', [
            'quote_token' => $quoteToken,
            'cart_items_count' => count($cartItemsData),
            'shipping_options_count' => count($shippingOptions['options'])
        ]);
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Frete calculado com sucesso',
                'options' => $shippingOptions['options']
            ]);
        }
        
        return redirect()->back()->with('success', 'Frete calculado com sucesso');
    }
    
    /**
     * Seleciona uma opção de frete
     */
    /**
     * Retorna as opções de frete disponíveis
     */
    public function getShippingOptions()
    {
        $shippingOptions = Session::get('shipping_options');
        $selectedShipping = Session::get('selected_shipping');
        $zipCode = Session::get('shipping_zip_code');
        
        \Log::info('Consultando opções de frete', [
            'has_shipping_options' => !empty($shippingOptions),
            'has_zip_code' => !empty($zipCode),
            'zip_code' => $zipCode,
            'options_count' => is_array($shippingOptions) ? count($shippingOptions) : 0,
            'has_selected_shipping' => !empty($selectedShipping)
        ]);
        
        if (!$shippingOptions || !$zipCode) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma opção de frete disponível. Por favor, calcule o frete primeiro.'
            ], 404);
        }
        
        // Garantir que as opções de frete sejam um array
        $shippingOptions = is_array($shippingOptions) ? $shippingOptions : [];
        
        return response()->json([
            'success' => true,
            'zip_code' => $zipCode,
            'options' => $shippingOptions,
            'selected_shipping' => $selectedShipping
        ]);
    }
    
    /**
     * Seleciona uma opção de frete
     */
    public function selectShipping(Request $request)
    {
        $request->validate([
            'shipping_option' => 'required',
        ]);
        
        // Obter o ID da opção de frete (pode ser um ID simples ou um objeto JSON)
        $shippingOptionId = $request->shipping_option;
        
        // Se for uma string JSON, decodificar para obter o ID
        if (is_string($shippingOptionId) && strpos($shippingOptionId, '{') === 0) {
            try {
                $decodedOption = json_decode($shippingOptionId, true);
                if (isset($decodedOption['id'])) {
                    $shippingOptionId = $decodedOption['id'];
                }
            } catch (\Exception $e) {
                // Manter o valor original se não puder decodificar
            }
        }
        
        \Log::info('Selecionando opção de frete', [
            'shipping_option_id' => $shippingOptionId,
        ]);
        
        $shippingOptions = Session::get('shipping_options');
        $isCheckout = $request->is_checkout ?? false;
        
        if (!$shippingOptions) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Por favor, calcule o frete primeiro.'
                ], 422);
            }
            
            return redirect()->back()->with('error', 'Por favor, calcule o frete primeiro.');
        }
        
        // Encontrar a opção selecionada
        $selectedOption = null;
        foreach ($shippingOptions as $option) {
            if ($option['id'] == $shippingOptionId) {
                $selectedOption = $option;
                break;
            }
        }
        
        \Log::info('Resultado da busca por opção de frete', [
            'found_option' => !is_null($selectedOption),
            'options_count' => count($shippingOptions),
        ]);
        
        if (!$selectedOption) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Opção de frete inválida.'
                ], 422);
            }
            
            return redirect()->back()->with('error', 'Opção de frete inválida.');
        }
        
        // Garantir que a opção de frete selecionada tenha todos os campos necessários
        $selectedOptionComplete = [
            'id' => $selectedOption['id'] ?? '',
            'name' => $selectedOption['name'] ?? 'Frete',
            'price' => $selectedOption['price'] ?? 0,
            'delivery_time' => $selectedOption['delivery_time'] ?? null,
            'delivery_estimate' => $selectedOption['delivery_estimate'] ?? 'Prazo a calcular',
            'company_id' => $selectedOption['company_id'] ?? 0,
            'company_name' => $selectedOption['company_name'] ?? '',
        ];
        
        // Salvar a opção selecionada na sessão
        Session::put('selected_shipping', $selectedOptionComplete);
        
        // Atualizar ou criar um registro ShippingQuote
        $quoteToken = Session::get('shipping_quote_token');
        
        if ($quoteToken) {
            $shippingQuote = \App\Models\ShippingQuote::where('quote_token', $quoteToken)->first();
            
            if ($shippingQuote) {
                $shippingQuote->selected_service_id = $selectedOption['id'];
                $shippingQuote->selected_price = $selectedOption['price'];
                $shippingQuote->selected_delivery_time = $selectedOption['delivery_time'] ?? null;
                $shippingQuote->save();
                
                // Garantir que o token está na sessão para o checkout
                Session::put('shipping_quote_token', $quoteToken);
                
                \Log::info('Opção de frete atualizada no banco de dados', [
                    'quote_token' => $quoteToken,
                    'selected_service_id' => $selectedOption['id'],
                    'selected_price' => $selectedOption['price']
                ]);
            } else {
                // Se o token existe na sessão mas não no banco, vamos criar um novo
                // Isso pode acontecer se a sessão persistir mas o registro no banco for excluído
                $this->createNewShippingQuote($quoteToken, $selectedOption);
            }
        } else {
            // Se não temos token na sessão, vamos gerar um e criar um novo registro
            $quoteToken = \Illuminate\Support\Str::uuid()->toString();
            Session::put('shipping_quote_token', $quoteToken);
            $this->createNewShippingQuote($quoteToken, $selectedOption);
            
            \Log::info('Novo token de frete gerado e salvo na sessão', ['quote_token' => $quoteToken]);
        }
        
        // Se estiver no checkout, atualizar o carrinho com o valor do frete
        if ($isCheckout) {
            $user = Auth::user();
            $cart = Cart::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();
                
            if ($cart) {
                $cart->shipping_method = $selectedOption['name'];
                $cart->shipping_cost = $selectedOption['price'];
                $cart->total = $cart->subtotal - $cart->discount + $cart->shipping_cost;
                $cart->save();
            }
        } else {
            // Mesmo que não esteja no checkout, vamos atualizar o carrinho para manter a coerência
            $user = Auth::user();
            $cart = Cart::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();
                
            if ($cart) {
                $cart->shipping_method = $selectedOption['name'];
                $cart->shipping_cost = $selectedOption['price'];
                $cart->total = $cart->subtotal - $cart->discount + $cart->shipping_cost;
                $cart->save();
                
                \Log::info('Carrinho atualizado com informações de frete', [
                    'cart_id' => $cart->id,
                    'shipping_cost' => $selectedOption['price'],
                    'total' => $cart->total
                ]);
            }
        }
        
        if ($request->wantsJson()) {
            // Obter o carrinho atualizado para retornar os valores corretos
            $cart = Cart::where('user_id', Auth::user()->id)
                ->where('status', 'active')
                ->first();
                
            return response()->json([
                'success' => true,
                'message' => 'Opção de frete selecionada com sucesso',
                'selected_option' => $selectedOption,
                'shipping_price' => $selectedOption['price'],
                'cart_total' => $cart ? $cart->total : ($cart->subtotal + $selectedOption['price']),
                'formatted_shipping' => 'R$ ' . number_format($selectedOption['price'], 2, ',', '.'),
                'formatted_total' => 'R$ ' . number_format($cart ? $cart->total : ($cart->subtotal + $selectedOption['price']), 2, ',', '.')
            ]);
        }
        
        $returnRoute = $isCheckout ? 'site.checkout.index' : 'site.cart.index';
        
        return redirect()->route($returnRoute)
                         ->with('success', 'Opção de frete selecionada com sucesso');
    }
    
    /**
     * Cria um novo registro de cotação de frete
     */
    protected function createNewShippingQuote($quoteToken, $selectedOption)
    {
        $user = Auth::user();
        $sessionId = Session::getId();
        
        // Obter itens do carrinho para calcular o hash
        $cartItems = $user->cartItems()
                         ->with('vinylMaster.vinylSec')
                         ->where('saved_for_later', false)
                         ->get();
        
        // Preparar dados de itens para salvar na cotação
        $cartItemsData = $cartItems->map(function($item) {
            return [
                'id' => $item->id,
                'vinyl_master_id' => $item->vinyl_master_id,
                'quantity' => $item->quantity
            ];
        })->toArray();
        
        // Hash para identificar o conjunto de itens atual
        $cartItemsHash = md5(json_encode($cartItemsData));
        
        // Preparar itens no formato do Melhor Envio
        $preparedItems = [];
        foreach ($cartItems as $item) {
            $preparedItems[] = [
                'id' => $item->vinylMaster->id,
                'quantity' => $item->quantity
            ];
        }
        
        // Criar nova cotação no banco de dados
        $shippingQuote = \App\Models\ShippingQuote::create([
            'quote_token' => $quoteToken,
            'user_id' => $user->id,
            'session_id' => Session::getId(),
            'cart_items_hash' => $cartItemsHash,
            'cart_items' => json_encode($cartItemsData),
            'zip_from' => config('shipping.zip_from', '09220360'),
            'zip_to' => Session::get('shipping_zip_code'),
            'products' => json_encode($preparedItems),
            'options' => json_encode(Session::get('shipping_options', [])),
            'selected_service_id' => $selectedOption['id'],
            'selected_price' => $selectedOption['price'],
            'selected_delivery_time' => $selectedOption['delivery_time'] ?? null,
            'expires_at' => now()->addDays(1)
        ]);
        
        \Log::info('Nova cotação de frete criada', [
            'quote_token' => $quoteToken,
            'shipping_quote_id' => $shippingQuote->id,
            'selected_service_id' => $selectedOption['id']
        ]);
        
        return $shippingQuote;
    }
}
