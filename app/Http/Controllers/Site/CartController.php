<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
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
        
        // Sincronizar todos os itens do carrinho do usuário para garantir que tenham cart_id correto
        CartItem::where('user_id', $user->id)
            ->where('saved_for_later', false)
            ->update(['cart_id' => $cart->id]);
        
        // Registrar na sessão qual é o carrinho ativo para consistência
        Session::put('active_cart_id', $cart->id);
        
        // Agora buscar os itens do carrinho já sincronizados
        $cartItems = $user->cartItems()
                          ->with('vinylMaster.vinylSec', 'vinylMaster.artists')
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
        
        // Se não há CEP na sessão, tentar usar o endereço padrão do usuário
        if (!$zipCode) {
            $defaultAddress = $user->addresses()
                ->where('is_default', true)
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
        
        // Obter produtos recomendados (até 3 vinis populares que não estão no carrinho)
        $recommendedVinyls = [];
        if (count($cartItems) > 0) {
            // Encontrar categorias dos produtos no carrinho
            $cartItemIds = $cartItems->pluck('vinyl_master_id')->toArray();
            
            // Buscar vinis similares baseados nos que estão no carrinho
            $recommendedVinyls = VinylMaster::with('vinylSec', 'artists')
                ->whereHas('vinylSec', function($query) {
                    $query->where('stock', '>', 0);
                })
                ->whereNotIn('id', $cartItemIds)
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
        $request->validate([
            'vinyl_master_id' => 'required|exists:vinyl_masters,id',
            'quantity' => 'sometimes|integer|min:1|max:10',
        ]);
        
        $user = Auth::user();
        $vinylMasterId = $request->vinyl_master_id;
        $quantity = $request->quantity ?? 1;
        
        // Verificar se o vinil existe e está disponível
        $vinylMaster = VinylMaster::findOrFail($vinylMasterId);
        
        if (!$vinylMaster->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'Este vinil não está disponível para compra no momento.'
            ], 422);
        }
        
        // Verificar se há estoque suficiente
        if ($vinylMaster->vinylSec->stock < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Quantidade solicitada não disponível em estoque. Disponível: ' . $vinylMaster->vinylSec->stock
            ], 422);
        }
        
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
        
        // Verificar se o item já está no carrinho
        $existingItem = CartItem::where('user_id', $user->id)
                               ->where('vinyl_master_id', $vinylMasterId)
                               ->first();
        
        if ($existingItem) {
            // Atualizar a quantidade
            $newQuantity = $existingItem->quantity + $quantity;
            
            // Verificar novamente o estoque com a nova quantidade
            if ($vinylMaster->vinylSec->stock < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quantidade total excede o estoque disponível. Disponível: ' . $vinylMaster->vinylSec->stock
                ], 422);
            }
            
            $existingItem->update([
                'quantity' => $newQuantity
            ]);
            
            $message = 'Quantidade atualizada no carrinho';
        } else {
            // Adicionar novo item ao carrinho
            CartItem::create([
                'user_id' => $user->id,
                'cart_id' => $cart->id,
                'vinyl_master_id' => $vinylMasterId,
                'quantity' => $quantity
            ]);
            
            $message = 'Item adicionado ao carrinho';
        }
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cart_count' => $user->cartItems()->sum('quantity')
            ]);
        }
        
        return redirect()->back()->with('success', $message);
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
        
        // Verificar se o vinil ainda está disponível
        if (!$cartItem->vinylMaster->isAvailable()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este vinil não está mais disponível para compra.'
                ], 422);
            }
            
            return redirect()->back()->with('error', 'Este vinil não está mais disponível para compra.');
        }
        
        // Verificar se há estoque suficiente
        if ($cartItem->vinylMaster->vinylSec->stock < $quantity) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quantidade solicitada não disponível em estoque. Disponível: ' . $cartItem->vinylMaster->vinylSec->stock
                ], 422);
            }
            
            return redirect()->back()->with('error', 'Quantidade solicitada não disponível em estoque. Disponível: ' . $cartItem->vinylMaster->vinylSec->stock);
        }
        
        // Atualizar a quantidade
        $cartItem->update([
            'quantity' => $quantity
        ]);
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Quantidade atualizada',
                'item' => [
                    'id' => $cartItem->id,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $cartItem->subtotal,
                ],
                'cart_total' => $user->cart_total
            ]);
        }
        
        return redirect()->back()->with('success', 'Quantidade atualizada');
    }
    
    /**
     * Remove um item do carrinho.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $cartItem = CartItem::where('user_id', $user->id)->findOrFail($id);
        
        $cartItem->delete();
        
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item removido do carrinho',
                'cart_count' => $user->cartItems()->sum('quantity'),
                'cart_total' => $user->cart_total
            ]);
        }
        
        return redirect()->back()->with('success', 'Item removido do carrinho');
    }
    
    /**
     * Limpa o carrinho completamente.
     */
    public function clear()
    {
        $user = Auth::user();
        
        $user->cartItems()->delete();
        
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Carrinho esvaziado'
            ]);
        }
        
        return redirect()->back()->with('success', 'Carrinho esvaziado');
    }
    
    /**
     * Transfere todos os itens da wishlist para o carrinho.
     */
    public function addFromWishlist()
    {
        $user = Auth::user();
        $wishlistItems = $user->wishlist()->with('vinylMaster')->get();
        $addedCount = 0;
        $notAvailable = 0;
        
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
        
        foreach ($wishlistItems as $wishlistItem) {
            // Verificar se o vinil existe e está disponível
            if ($wishlistItem->vinylMaster && $wishlistItem->vinylMaster->isAvailable()) {
                // Verificar se o item já está no carrinho
                $existingItem = CartItem::where('user_id', $user->id)
                                       ->where('vinyl_master_id', $wishlistItem->vinyl_master_id)
                                       ->first();
                
                if ($existingItem) {
                    // Atualizar a quantidade (limitado ao estoque disponível)
                    $newQuantity = min(
                        $existingItem->quantity + 1, 
                        $wishlistItem->vinylMaster->vinylSec->stock
                    );
                    
                    $existingItem->update([
                        'quantity' => $newQuantity
                    ]);
                } else {
                    // Adicionar novo item ao carrinho
                    CartItem::create([
                        'user_id' => $user->id,
                        'cart_id' => $cart->id,
                        'vinyl_master_id' => $wishlistItem->vinyl_master_id,
                        'quantity' => 1
                    ]);
                }
                
                // Remover da wishlist
                $wishlistItem->delete();
                
                $addedCount++;
            } else {
                $notAvailable++;
            }
        }
        
        if ($notAvailable > 0) {
            $message = "$addedCount itens adicionados ao carrinho. $notAvailable itens não estão disponíveis para compra no momento.";
        } else {
            $message = "$addedCount itens adicionados ao carrinho.";
        }
        
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cart_count' => $user->cartItems()->sum('quantity')
            ]);
        }
        
        return redirect()->back()->with('success', $message);
    }
    
    /**
     * Mover um item do carrinho para a lista "Salvar para depois".
     */
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
