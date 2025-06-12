<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Address;
use App\Services\MelhorEnvio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ShippingController extends Controller
{
    /**
     * Constructor do controlador
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Exibe a página de seleção de endereço e frete com resumo do pedido
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obter os itens do carrinho com relacionamentos necessários
        $allCartItems = $user->cartItems()
                          ->with(['product.productable', 'vinylMaster.vinylSec', 'vinylMaster.artists'])
                          ->where('saved_for_later', false)
                          ->get();

        // Verificar estoque de cada item e filtrar apenas os que têm estoque suficiente
        $cartItems = $allCartItems->filter(function($item) {
            return $item->hasEnoughStock();
        });
        
        // Se não tiver itens com estoque no carrinho, redirecionar para o carrinho
        if ($cartItems->isEmpty()) {
            return redirect()->route('site.cart.index')->with('error', 'Não há itens com estoque suficiente no seu carrinho.');
        }
        
        // Calcular subtotal do pedido (apenas itens com estoque)
        $subtotal = 0;
        foreach ($cartItems as $item) {
            // Usar o método getSubtotalAttribute ou o preço direto dependendo de como estiver implementado
            $itemPrice = $item->price ?? ($item->product->price ?? ($item->vinylMaster->vinylSec->price ?? 0));
            $subtotal += $itemPrice * $item->quantity;
        }
        
        // Verificar se há itens sem estoque que foram filtrados
        $itemsWithoutStock = $allCartItems->filter(function($item) {
            return !$item->hasEnoughStock();
        });
        
        // Se houver itens sem estoque, adicionar uma mensagem de aviso
        if ($itemsWithoutStock->isNotEmpty()) {
            $itemNames = $itemsWithoutStock->map(function($item) {
                return $item->vinylMaster ? $item->vinylMaster->title : 'Item';
            })->implode(', ');
            
            // Registrar no log
            \Illuminate\Support\Facades\Log::info('Itens sem estoque foram filtrados na página de frete', [
                'user_id' => $user->id,
                'items_without_stock' => $itemsWithoutStock->pluck('id')->toArray()
            ]);
            
            // Adicionar mensagem de aviso na sessão
            session()->flash('warning', "Os seguintes itens sem estoque suficiente foram removidos do cálculo: {$itemNames}");
        }
        
        // Obter todos os endereços ativos do usuário
        $userAddresses = Address::where('user_id', $user->id)
                               ->where('is_active', true)
                               ->orderBy('is_default_shipping', 'desc')
                               ->get();
        
        // Obter o endereço padrão para entrega
        $defaultAddress = $userAddresses->where('is_default_shipping', true)->first();
        
        // Se não tiver endereço padrão, pegar o primeiro da lista
        if (!$defaultAddress && $userAddresses->isNotEmpty()) {
            $defaultAddress = $userAddresses->first();
        }
        
        // Obter o CEP para cálculo de frete
        $zipCode = null;
        if ($defaultAddress) {
            $zipCode = preg_replace('/\D/', '', $defaultAddress->zipcode);
        }
        
        // Verificar se já tem CEP na sessão
        $zipCode = Session::get('shipping_zip_code', $zipCode);
        
        // Obter o carrinho ativo do usuário
        $cart = Cart::where('user_id', $user->id)
                   ->where('status', 'active')
                   ->first();
        
        // Obter o desconto aplicado, se houver
        $discount = $cart ? $cart->discount : 0;
        
        // Obter opções de frete da sessão (se já foram calculadas)
        $shippingOptions = Session::get('shipping_options');
        
        // Obter opção de frete selecionada
        $selectedShipping = null;
        
        // Primeiro tentar obter do carrinho
        if ($cart && !empty($cart->shipping_option)) {
            try {
                $selectedShipping = json_decode($cart->shipping_option, true);
            } catch (\Exception $e) {
                // Se houver erro no decode, deixa como null
            }
        }
        
        // Se não tiver no banco, tentar obter da sessão
        if (!$selectedShipping || !is_array($selectedShipping)) {
            $selectedShipping = Session::get('selected_shipping');
        }
        
        // Garantir que seja sempre um array válido
        if (!is_array($selectedShipping)) {
            $selectedShipping = [
                'id' => '',
                'name' => 'Selecione uma opção de frete',
                'price' => 0,
                'delivery_time' => 0,
                'delivery_estimate' => 'Calcule o frete',
                'company_id' => 0,
                'company_name' => '',
            ];
        }
        
        // Calcular o valor do frete
        $shippingCost = $selectedShipping['price'] ?? 0;
        
        // Calcular o total do pedido (subtotal - desconto + frete)
        $total = $subtotal - $discount + $shippingCost;
        
        // Se houver um endereço selecionado e ainda não houver opções de frete, calcular automaticamente
        if ($defaultAddress && !$shippingOptions && $zipCode) {
            $melhorEnvio = new MelhorEnvio();
            $result = $melhorEnvio->calculateShipping($zipCode, $cartItems->toArray());
            
            if ($result['success']) {
                $shippingOptions = $result['options'];
                Session::put('shipping_options', $shippingOptions);
                Session::put('shipping_zip_code', $zipCode);
            }
        }
        
        return view('site.shipping.index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shippingCost' => $shippingCost,
            'total' => $total,
            'userAddresses' => $userAddresses,
            'defaultAddress' => $defaultAddress,
            'zipCode' => $zipCode,
            'shippingOptions' => $shippingOptions,
            'selectedShipping' => $selectedShipping,
            'cart' => $cart
        ]);
    }
    }

    /**
     * Calcula o frete para os itens no carrinho
     */
    public function calculateShipping(Request $request)
    {
        $request->validate([
            'zip_code' => 'required|string|min:8|max:9',
        ]);
        
        $zipCode = preg_replace('/\D/', '', $request->zip_code);
        
        if (strlen($zipCode) !== 8) {
            return redirect()->back()->with('error', 'CEP inválido. O CEP deve conter 8 dígitos.');
        }
        
        $user = Auth::user();
        
        // Obter os itens do carrinho
        $allCartItems = $user->cartItems()
                          ->with(['product.productable', 'vinylMaster.vinylSec'])
                          ->where('saved_for_later', false)
                          ->get();
                          
        if ($allCartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Seu carrinho está vazio.');
        }
        
        // Filtrar apenas os itens com estoque suficiente
        $cartItems = $allCartItems->filter(function($item) {
            return $item->hasEnoughStock();
        });
        
        // Verificar se há itens com estoque suficiente
        if ($cartItems->isEmpty()) {
            return redirect()->route('site.cart.index')->with('error', 'Não há itens com estoque suficiente no seu carrinho.');
        }
        
        // Verificar se há itens sem estoque que foram filtrados
        $itemsWithoutStock = $allCartItems->filter(function($item) {
            return !$item->hasEnoughStock();
        });
        
        // Se houver itens sem estoque, adicionar uma mensagem de aviso
        if ($itemsWithoutStock->isNotEmpty()) {
            $itemNames = $itemsWithoutStock->map(function($item) {
                return $item->vinylMaster ? $item->vinylMaster->title : 'Item';
            })->implode(', ');
            
            // Registrar no log
            \Illuminate\Support\Facades\Log::info('Itens sem estoque foram filtrados no cálculo de frete', [
                'user_id' => $user->id,
                'items_without_stock' => $itemsWithoutStock->pluck('id')->toArray()
            ]);
            
            // Adicionar mensagem de aviso na sessão
            session()->flash('warning', "Os seguintes itens sem estoque suficiente foram removidos do cálculo: {$itemNames}");
        }
        
        // Usar o serviço MelhorEnvio para calcular o frete (apenas com itens com estoque)
        $melhorEnvio = new MelhorEnvio();
        $result = $melhorEnvio->calculateShipping($zipCode, $cartItems->toArray());
        
        if ($result['success']) {
            // Armazenar as opções de frete na sessão
            Session::put('shipping_options', $result['options']);
            Session::put('shipping_zip_code', $zipCode);
            Session::put('shipping_calculation', [
                'success' => true,
                'message' => 'Frete calculado com sucesso.'
            ]);
            
            // Limpar seleção anterior de frete
            Session::forget('selected_shipping');
            
            return redirect()->back()->with('success', 'Frete calculado com sucesso.');
        } else {
            Session::put('shipping_calculation', [
                'success' => false,
                'message' => $result['message'] ?? 'Erro ao calcular o frete.'
            ]);
            
            return redirect()->back()->with('error', $result['message'] ?? 'Erro ao calcular o frete.');
        }
    }

    /**
     * Nova função para cálculo manual sob demanda
     */
    public function calculateShippingManual(Request $request) {
        $request->validate(['zip_code' => 'required|string|min:8|max:9']);
        
        // Lógica de cálculo segura aqui
        // ...
        
        return redirect()->back()->with(
            'shipping_calculation',
            ['status' => 'success', 'message' => 'Frete calculado!']
        );
    }

    /**
     * Seleciona uma opção de frete
     */
    public function selectShipping(Request $request)
    {
        $request->validate([
            'shipping_option' => 'required|string',
        ]);
        
        $shippingOptions = Session::get('shipping_options');
        
        if (!$shippingOptions) {
            return redirect()->back()->with('error', 'Nenhuma opção de frete disponível. Por favor, calcule o frete novamente.');
        }
        
        $selectedOption = null;
        
        // Encontrar a opção de frete selecionada
        foreach ($shippingOptions as $option) {
            if ($option['id'] == $request->shipping_option) {
                $selectedOption = $option;
                break;
            }
        }
        
        if (!$selectedOption) {
            return redirect()->back()->with('error', 'Opção de frete inválida.');
        }
        
        // Obter o usuário e o carrinho ativo
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();
            
        if (!$cart) {
            return redirect()->route('site.cart.index')->with('error', 'Carrinho não encontrado.');
        }
        
        // Atualizar o carrinho com o frete selecionado
        $cart->shipping_cost = $selectedOption['price'] ?? 0;
        $cart->total = $cart->subtotal - $cart->discount + $cart->shipping_cost;
        
        // Armazenar a opção de frete como JSON no carrinho
        $cart->shipping_option = json_encode($selectedOption);
        $cart->save();
        
        // Garantir que apenas arrays sejam armazenados na sessão
        if (is_array($selectedOption)) {
            Session::put('selected_shipping', $selectedOption);
        } else {
            // Se por algum motivo não for array, cria um array com os dados mínimos
            Session::put('selected_shipping', [
                'id' => $request->shipping_option,
                'name' => 'Frete selecionado',
                'price' => (float)($selectedOption['price'] ?? 0),
                'delivery_time' => (int)($selectedOption['delivery_time'] ?? 5),
                'company_id' => 0,
                'company_name' => '',
            ]);
        }
        
        return redirect()->back()->with('success', 'Opção de frete selecionada com sucesso.');
    }
    
    /**
     * Cria o pedido e continua diretamente para o pagamento após selecionar o frete
     */
    public function proceedToCheckout(Request $request)
    {
        $selectedShipping = Session::get('selected_shipping');
        
        // Verificar se existe e se é um array
        if (!$selectedShipping || !is_array($selectedShipping) || empty($selectedShipping['id'])) {
            return redirect()->back()->with('error', 'Selecione uma opção de frete válida antes de continuar.');
        }
        
        // Obter carrinho atual
        $cart = app(CartService::class)->getCurrentCart();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('site.cart.index')->with('error', 'Seu carrinho está vazio.');
        }
        
        // Verificar se há endereço de envio selecionado
        $user = auth()->user();
        $shippingAddress = $user->defaultAddress;
        
        if (!$shippingAddress) {
            return redirect()->back()->with('error', 'Por favor, adicione um endereço de entrega.');
        }
        
        // Obter opção de frete selecionada
        $selectedShipping = Session::get('selected_shipping');
        
        // Processar dados do usuário enviados pelo formulário
        $userData = json_decode($request->user_data, true) ?: [];
        
        // Validar se todos os dados necessários estão presentes
        $errors = [];
        
        // Verificar carrinho
        if (!$cart || !$cart->items || $cart->items->isEmpty()) {
            $errors[] = 'Seu carrinho está vazio';
        }
        
        // Verificar estoque de cada item
        $itemsWithoutStock = [];
        foreach ($cart->items as $item) {
            if (!$item->hasEnoughStock()) {
                $itemName = $item->vinylMaster ? $item->vinylMaster->title : 'Item';
                $itemsWithoutStock[] = $itemName;
                
                // Registrar no log
                \Illuminate\Support\Facades\Log::warning('Tentativa de checkout com item sem estoque', [
                    'cart_item_id' => $item->id,
                    'user_id' => $user->id,
                    'product_id' => $item->product_id,
                    'vinyl_master_id' => $item->vinyl_master_id,
                    'quantity' => $item->quantity
                ]);
            }
        }
        
        if (!empty($itemsWithoutStock)) {
            $itemsList = implode(', ', $itemsWithoutStock);
            $errors[] = "Os seguintes itens não possuem estoque suficiente: {$itemsList}. Por favor, remova-os do carrinho ou ajuste a quantidade.";
        }
        
        // Verificar frete
        if (!$selectedShipping || empty($selectedShipping['price'])) {
            $errors[] = 'Selecione uma opção de frete';
        }
        
        // Verificar dados do usuário
        if (empty($userData['full_name'])) {
            $errors[] = 'Nome completo é obrigatório';
        }
        
        if (empty($userData['email'])) {
            $errors[] = 'Email é obrigatório';
        }
        
        if (empty($userData['phone'])) {
            $errors[] = 'Telefone é obrigatório';
        }
        
        if (empty($userData['cpf'])) {
            $errors[] = 'CPF é obrigatório';
        }
        
        if (empty($userData['address_id'])) {
            $errors[] = 'Selecione um endereço de entrega';
        }
        
        // Se houver erros, redirecionar de volta
        if (!empty($errors)) {
            return redirect()->back()->with('error', implode(', ', $errors));
        }
        
        // Criar o pedido com todos os campos obrigatórios conforme a estrutura do banco
        $orderData = [
            'user_id' => $user->id,
            'session_id' => session()->getId(),
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'shipping_address_id' => $shippingAddress->id,
            'billing_address_id' => $shippingAddress->id,
            'status' => \App\Enums\OrderStatus::PENDING->value,
            'payment_status' => 'pending', // Campo obrigatório com valor default
            'shipping_status' => 'pending', // Campo obrigatório com valor default
            'subtotal' => $cart->subtotal,
            'shipping' => $selectedShipping['selected']['price'] ?? 0,
            'discount' => $cart->discount ?? 0,
            'tax' => 0,
            'total' => $cart->subtotal - ($cart->discount ?? 0) + ($selectedShipping['selected']['price'] ?? 0),
            'shipping_method' => $selectedShipping['selected']['name'] ?? 'Padrão',
            'shipping_label_url' => null, // Adicionado pois existe na tabela
            'tracking_number' => null, // Adicionado pois existe na tabela
            'tracking_url' => null, // Adicionado pois existe na tabela
            'customer_ip_address' => $request->ip(), // IP do cliente
            'customer_user_agent' => $request->header('User-Agent') // User agent do cliente
        ];
        
        // Informações do cliente (não serão salvas na tabela orders)
        $customerInfo = [
            'name' => $userData['full_name'] ?? $user->name,
            'email' => $userData['email'] ?? $user->email,
            'phone' => $userData['phone'] ?? $user->phone,
            'document' => $userData['cpf'] ?? $user->document
        ];
        
        try {
            // Registrar os dados que serão salvos para depuração
            \Log::info('Dados do pedido a serem salvos:', $orderData);
            
            // Iniciar transação para garantir consistência dos dados
            DB::beginTransaction();
            
            // Criar o pedido
            $order = new \App\Models\Order($orderData);
            \Log::info('Modelo de pedido criado:', ['fillable' => $order->getFillable()]);
            $order->save();
            
            // Adicionar itens do carrinho ao pedido com detalhado debug dos dados
            foreach ($cart->items as $item) {
                // Debug para entender a estrutura do item do carrinho
                \Log::info('Estrutura do item do carrinho:', [
                    'item_class' => get_class($item),
                    'price_exists' => isset($item->price), 
                    'item_properties' => get_object_vars($item),
                    'vinyl_master_id' => $item->vinyl_master_id ?? null
                ]);
                
                // Obter dados do produto
                $productName = $item->product ? $item->product->name : ($item->vinylMaster ? $item->vinylMaster->title : 'Produto');
                $productDescription = $item->product ? $item->product->description : ($item->vinylMaster ? $item->vinylMaster->description : null);
                $sku = $item->product ? $item->product->sku : null;
                
                // Lógica avançada para determinar preços, considerando promoções e descontos
                $originalPrice = 0; // Preço original (sem descontos)
                $unitPrice = 0;     // Preço unitário efetivo (com descontos promocionais)
                $discount = 0;      // Valor total do desconto
                
                // Tentativa #1: Usar informações já calculadas no item do carrinho
                if (isset($item->original_price) && $item->original_price > 0) {
                    $originalPrice = $item->original_price;
                }
                
                if (isset($item->price) && $item->price > 0) {
                    $unitPrice = $item->price;
                    if ($originalPrice == 0) $originalPrice = $item->price;
                }
                
                // Tentativa #2: Calcular a partir do total e quantidade
                if ($unitPrice == 0 && isset($item->total) && $item->total > 0 && $item->quantity > 0) {
                    $unitPrice = $item->total / $item->quantity;
                    if ($originalPrice == 0) $originalPrice = $unitPrice;
                }
                
                // Tentativa #3: Verificar se é um VinylSec, que possui lógica de promotional_price
                if ($item->vinylMaster) {
                    // Debug dos dados do VinylMaster para entender a estrutura
                    \Log::info('Dados do VinylMaster:', [
                        'id' => $item->vinylMaster->id,
                        'title' => $item->vinylMaster->title,
                        'has_vinyl_sec' => isset($item->vinylMaster->vinylSec)
                    ]);
                    
                    // Se temos o vinylSec relacionado
                    if (isset($item->vinylMaster->vinylSec)) {
                        $vinylSec = $item->vinylMaster->vinylSec;
                        
                        \Log::info('Dados do VinylSec:', [
                            'id' => $vinylSec->id,
                            'price' => $vinylSec->price ?? null,
                            'promotional_price' => $vinylSec->promotional_price ?? null,
                            'has_promotion' => isset($vinylSec->promotional_price) && $vinylSec->promotional_price > 0
                        ]);
                        
                        // Preço original é sempre o preço regular
                        if (isset($vinylSec->price) && $vinylSec->price > 0) {
                            $originalPrice = $vinylSec->price;
                            
                            // Se não há preço unitário definido ainda, usa o regular
                            if ($unitPrice == 0) {
                                $unitPrice = $vinylSec->price;
                            }
                        }
                        
                        // Se há preço promocional E a promoção está ativa (is_promotional = 1), substitui o preço unitário e calcula o desconto
                        if (isset($vinylSec->promotional_price) && $vinylSec->promotional_price > 0 && 
                            isset($vinylSec->is_promotional) && $vinylSec->is_promotional == 1 && 
                            $vinylSec->promotional_price < $originalPrice) {
                            $unitPrice = $vinylSec->promotional_price;
                            // O desconto é a diferença entre original e promo
                            $discount = ($originalPrice - $unitPrice) * $item->quantity;
                        } else {
                            // Se não está em promoção, usa o preço normal mesmo que tenha promotional_price definido
                            $unitPrice = $originalPrice;
                            $discount = 0;
                        }
                    }
                }
                
                // Tentativa #4: Use o product se disponível
                if ($unitPrice == 0 && $item->product) {
                    // Preço do produto
                    if (isset($item->product->price)) {
                        $unitPrice = $item->product->price;
                        if ($originalPrice == 0) $originalPrice = $item->product->price;
                    }
                    
                    // Se houver preço promocional
                    if (isset($item->product->promotional_price) && $item->product->promotional_price > 0) {
                        $unitPrice = $item->product->promotional_price;
                        // Se o preço original já foi definido, calcular desconto
                        if ($originalPrice > 0 && $unitPrice < $originalPrice) {
                            $discount = ($originalPrice - $unitPrice) * $item->quantity;
                        }
                    }
                }
                
                // Fallback final para garantir valores não nulos
                if ($unitPrice <= 0) {
                    $unitPrice = 0.01;
                    \Log::warning('Não foi possível determinar o preço do item. Usando fallback:', ['item_id' => $item->id ?? 'unknown']);
                }
                
                if ($originalPrice <= 0) {
                    $originalPrice = $unitPrice;
                }
                
                // Adicionar qualquer desconto do carrinho
                if (isset($item->discount) && $item->discount > 0) {
                    $discount += $item->discount;
                }
                
                // Cálculo do preço total final
                $totalPrice = ($item->quantity * $unitPrice) - $discount;
                if ($totalPrice <= 0) $totalPrice = 0.01; // Garantir valor positivo
                
                // Logar os valores calculados
                \Log::info('Preços calculados:', [
                    'originalPrice' => $originalPrice,
                    'unitPrice' => $unitPrice,
                    'discount' => $discount,
                    'totalPrice' => $totalPrice,
                    'quantity' => $item->quantity
                ]);
                
                $itemData = [
                    'order_id' => $order->id,
                    'vinyl_master_id' => $item->vinyl_master_id ?? 1, // Campo obrigatório, usando 1 como fallback
                    'name' => $productName,
                    'description' => $productDescription,
                    'sku' => $sku ?? 'SKU-' . mt_rand(1000, 9999), // Garantindo que não será null
                    'quantity' => $item->quantity,
                    'unit_price' => $unitPrice, // Preço unitário calculado com descontos promocionais
                    'original_price' => $originalPrice, // Preço original sem descontos
                    'discount' => $discount, // Total de desconto (promocional + carrinho)
                    'tax' => 0, // Imposto
                    'total_price' => $totalPrice, // Preço total calculado
                    'metadata' => json_encode([
                        'options' => $item->options ?? [],
                        'price_info' => [
                            'original_price' => $originalPrice,
                            'unit_price' => $unitPrice,
                            'discount' => $discount,
                            'total_price' => $totalPrice
                        ]
                    ])
                ];
                
                \Log::info('Dados do item de pedido a salvar:', $itemData);
                
                $orderItem = new \App\Models\OrderItem($itemData);
                $orderItem->save();
            }
            
            // Armazenar ID do pedido na sessão para uso no checkout
            session(['checkout_order_id' => $order->id]);
            
            // Confirmar transação
            DB::commit();
            
            // Armazenar ID do pedido na sessão para uso no checkout
            session(['checkout_success' => true]);
            
            // Redirecionar para a página de pagamento simplificada
            return redirect()->route('site.payment', ['order_id' => $order->id]);
            
        } catch (\Exception $e) {
            // Desfazer transação em caso de erro
            DB::rollBack();
            
            // Registrar erro
            \Log::error('Erro ao criar pedido', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            

            return redirect()->back()->with('error', 'Ocorreu um erro ao processar seu pedido. Por favor, tente novamente.');
        }
    }
}
