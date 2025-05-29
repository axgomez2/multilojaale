<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\ShippingQuote;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\MelhorEnvio;
use App\Services\OrderService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewCheckoutController extends Controller
{
    protected CartService $cartService;
    protected OrderService $orderService;
    protected MelhorEnvio $melhorEnvio;
    
    /**
     * Create a new controller instance.
     */
    public function __construct(
        CartService $cartService,
        OrderService $orderService,
        MelhorEnvio $melhorEnvio
    ) {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->melhorEnvio = $melhorEnvio;
    }
    
    /**
     * Exibir a página principal do checkout simplificado.
     */
    public function index(Request $request)
    {
        // Verificar se o carrinho está vazio
        $cart = $this->cartService->getCurrentCart();
        
        // Carregar os relacionamentos necessários para exibição completa
        $cart->load(['items' => function($query) {
            $query->where('saved_for_later', false)
                  ->with(['vinylMaster' => function($q) {
                      $q->with(['artists', 'vinylSec']);
                  }]);
        }]);
        
        // Calcular o subtotal para garantir que o valor está atualizado
        $cart->updateTotals();
        
        // Obter os itens ativos do carrinho
        $cartItemCount = CartItem::where('cart_id', $cart->id)
            ->where('saved_for_later', false)
            ->count();
        
        if (!$cart || $cartItemCount === 0) {
            return redirect()->route('site.cart.index')
                ->with('error', 'Seu carrinho está vazio. Adicione produtos antes de prosseguir para o checkout.');
        }
        
        // Verificar se há frete selecionado
        $selectedShipping = session('selected_shipping');
        if (!$selectedShipping) {
            return redirect()->route('site.cart.index')
                ->with('error', 'É necessário selecionar uma opção de frete antes de prosseguir para o checkout.');
        }
        
        // Recuperar endereços do usuário
        $addresses = collect([]);
        $selectedAddress = null;
        
        if (auth()->check()) {
            $addresses = auth()->user()->addresses()->get();
            
            // Verificar se há um endereço padrão ou selecionar o primeiro
            if ($addresses->count() > 0) {
                $selectedAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();
            }
        }
        
        if (!$selectedAddress && $addresses->count() > 0) {
            $selectedAddress = $addresses->first();
        }
        
        // Registrar acesso à página
        Log::info('Acesso ao checkout simplificado', [
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'cart_id' => $cart->id,
        ]);
        
        // Configurar variáveis para o Mercado Pago
        $mpPublicKey = config('services.mercadopago.public_key');
        
        // Adicionar log para debug da chave
        Log::info('Mercado Pago public key', [
            'public_key' => $mpPublicKey,
            'config_exists' => !empty(config('services.mercadopago'))
        ]);
        
        return view('site.newcheckout.index', [
            'cart' => $cart,
            'addresses' => $addresses,
            'selectedAddress' => $selectedAddress,
            'selectedShipping' => $selectedShipping,
            'mpPublicKey' => $mpPublicKey
        ]);
    }
    
    /**
     * Processar o pagamento e finalizar o pedido.
     */
    public function processPayment(Request $request)
    {
        // Validar os dados de pagamento recebidos
        $request->validate([
            'payment_method' => 'required|in:credit_card,pix,boleto',
            'address_id' => 'required|exists:addresses,id',
        ]);
        
        // Obter o carrinho atual
        $cart = $this->cartService->getCurrentCart();
        
        // Carregar os itens do carrinho
        $cart->load(['items' => function($query) {
            $query->where('saved_for_later', false)
                  ->with(['vinylMaster' => function($q) {
                      $q->with(['vinylSec']);
                  }]);
        }]);
        
        // Verificar se há itens no carrinho
        if ($cart->items->isEmpty()) {
            return redirect()->route('site.cart.index')
                ->with('error', 'Não foi possível finalizar a compra: seu carrinho está vazio.');
        }
        
        // Verificar se há frete selecionado
        $selectedShipping = session('selected_shipping');
        if (!$selectedShipping) {
            return redirect()->route('site.cart.index')
                ->with('error', 'É necessário selecionar uma opção de frete antes de finalizar a compra.');
        }
        
        try {
            // Criar o pedido
            // Usando valores do enum OrderStatus
            $order = new Order();
            $order->user_id = auth()->id();
            $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            $order->status = \App\Enums\OrderStatus::PENDING->value;
            $order->shipping_address_id = $request->address_id;
            $order->billing_address_id = $request->address_id; // Usar mesmo endereço para faturamento
            $order->subtotal = $cart->subtotal;
            $order->shipping = $selectedShipping['price'] ?? 0;
            $order->discount = $cart->discount;
            $order->total = $cart->subtotal + ($selectedShipping['price'] ?? 0) - $cart->discount;
            $order->shipping_method = $selectedShipping['name'] ?? '';
            $order->tracking_number = null; // Será preenchido posteriormente
            $order->customer_note = $request->notes;
            $order->save();
            
            // Adicionar itens ao pedido e atualizar estoque
            foreach ($cart->items as $item) {
                $vinylMaster = $item->vinylMaster;
                $vinylSec = $vinylMaster->vinylSec;
                $price = $vinylSec->price ?? $item->price;
                
                // Criar item do pedido
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->vinyl_master_id = $item->vinyl_master_id;
                $orderItem->name = $vinylMaster->title ?? 'Produto';
                $orderItem->description = $vinylMaster->description ?? null;
                $orderItem->sku = $vinylMaster->barcode ?? null;
                $orderItem->quantity = $item->quantity;
                $orderItem->unit_price = $price;
                $orderItem->total_price = $item->quantity * $price;
                $orderItem->save();
                
                // Atualizar estoque
                if ($vinylSec && isset($vinylSec->stock)) {
                    $vinylSec->stock = max(0, $vinylSec->stock - $item->quantity);
                    $vinylSec->save();
                    
                    // Registrar a atualização de estoque no log
                    Log::info('Estoque atualizado', [
                        'vinyl_master_id' => $item->vinyl_master_id,
                        'vinyl_sec_id' => $vinylSec->id,
                        'quantidade_anterior' => $vinylSec->stock + $item->quantity,
                        'quantidade_atual' => $vinylSec->stock,
                        'pedido' => $order->order_number
                    ]);
                }
            }
            
            // Processar pagamento com Mercado Pago (simplificado)
            // Aqui seria feita a integração real com a API do Mercado Pago
            
            // Criar registro de pagamento
            $payment = new \App\Models\Payment();
            $payment->order_id = $order->id;
            $payment->gateway_code = 'mercadopago';
            $payment->method = $request->payment_method; // Aqui sim armazenamos o método de pagamento
            $payment->status = 'pending';
            $payment->amount = $order->total;
            $payment->save();
            
            // Atualizar status do pedido (simulado)
            $order->status = \App\Enums\OrderStatus::PAYMENT_APPROVED->value; // Mudando para um valor válido do enum
            $order->payment_status = \App\Enums\PaymentStatus::APPROVED->value; // Usando valor válido do enum PaymentStatus
            $order->save();
            
            // Registrar a conclusão do pedido no log
            Log::info('Pedido finalizado com sucesso', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total' => $order->total,
                'user_id' => auth()->id(),
                'payment_method' => $request->payment_method
            ]);
            
            // Limpar carrinho após pedido finalizado com sucesso
            $cart->status = 'ordered';
            $cart->save();
            
            // Remover explicitamente os itens do carrinho para não aparecerem na interface
            // Cria um novo carrinho vazio para o usuário
            if (auth()->check()) {
                $newCart = new \App\Models\Cart();
                $newCart->user_id = auth()->id();
                $newCart->status = 'active';
                $newCart->save();
                
                // Associa o novo carrinho à sessão atual
                session()->put('cart_id', $newCart->id);
            }
            
            // Limpar sessão
            session()->forget(['selected_shipping', 'shipping_quote_token', 'shipping_options']);
            
            // Redirecionar para página de sucesso
            return redirect()->route('site.checkout.success', ['order_number' => $order->order_number])
                ->with('success', 'Pedido realizado com sucesso!');
                
        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento', [
                'user_id' => auth()->id(),
                'cart_id' => $cart->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('site.newcheckout.index')
                ->with('error', 'Ocorreu um erro ao processar o pagamento. Por favor, tente novamente.');
        }
    }
    
    /**
     * Exibir página de sucesso após finalização do pedido.
     */
    public function success(Request $request)
    {
        $orderNumber = $request->order_number;
        
        // Buscar pedido pelo número
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with(['items.vinylMaster.artists', 'address'])
            ->first();
            
        if (!$order) {
            return redirect()->route('site.cart.index')
                ->with('error', 'Pedido não encontrado.');
        }
        
        return view('site.newcheckout.success', [
            'order' => $order
        ]);
    }
    
    /**
     * Processar o envio do formulário de endereço.
     */
    public function processAddress(Request $request)
    {
        $addressId = $request->input('address_id');
        
        if (!$addressId) {
            return redirect()->route('site.newcheckout.index', ['step' => 'address'])
                ->with('error', 'Por favor, selecione um endereço de entrega.');
        }
        
        // Armazenar o endereço selecionado na sessão
        session(['checkout_address_id' => $addressId]);
        
        try {
            // Buscar endereço para obter o CEP
            $address = auth()->user()->addresses()->find($addressId);
            if ($address) {
                // Calcular o frete automaticamente
                $zipcode = $address->zipcode;
                $shippingQuote = $this->calculateShipping($zipcode);
                
                // Verificar se obtivemos opções de frete com sucesso
                if (!$shippingQuote || !isset($shippingQuote['options']) || empty($shippingQuote['options'])) {
                    Log::warning('Nenhuma opção de frete disponível para o CEP: ' . $zipcode);
                    return redirect()->route('site.newcheckout.index', ['step' => 'shipping'])
                        ->with('error', 'Não foi possível obter opções de frete para o endereço selecionado. Por favor, tente outro endereço ou entre em contato com o suporte.');
                }
                
                // Registrar o sucesso no log para debug
                Log::info('Opções de frete calculadas com sucesso', [
                    'address_id' => $addressId,
                    'zipcode' => $zipcode,
                    'options_count' => count($shippingQuote['options']),
                    'quote_token' => $shippingQuote['quote_token'] ?? 'não disponível'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao calcular frete automático: ' . $e->getMessage());
            // Continuar mesmo se houver erro no cálculo de frete
        }
        
        // Redirecionar para a próxima etapa
        return redirect()->route('site.newcheckout.index', ['step' => 'shipping']);
    }
    
    /**
     * Processar o envio do formulário de frete.
     */
    public function processShipping(Request $request)
    {
        $serviceId = $request->input('shipping_service_id');
        $servicePrice = $request->input('shipping_price');
        $deliveryTime = $request->input('delivery_time');
        
        if (!$serviceId || !$servicePrice) {
            return redirect()->route('site.newcheckout.index', ['step' => 'shipping'])
                ->with('error', 'Por favor, selecione uma opção de frete.');
        }
        
        // Buscar a cotação atual
        $quoteToken = session('shipping_quote_token');
        if (!$quoteToken) {
            return redirect()->route('site.newcheckout.index', ['step' => 'shipping'])
                ->with('error', 'Cotação de frete inválida. Por favor, calcule o frete novamente.');
        }
        
        $shippingQuote = ShippingQuote::where('quote_token', $quoteToken)->first();
        if (!$shippingQuote) {
            return redirect()->route('site.newcheckout.index', ['step' => 'shipping'])
                ->with('error', 'Cotação de frete não encontrada. Por favor, calcule o frete novamente.');
        }
        
        // Atualizar a cotação com o serviço selecionado
        $shippingQuote->selected_service_id = $serviceId;
        $shippingQuote->selected_price = $servicePrice;
        $shippingQuote->selected_delivery_time = $deliveryTime;
        $shippingQuote->save();
        
        // Armazenar o serviço selecionado na sessão
        session(['selected_shipping' => [
            'id' => $serviceId,
            'price' => $servicePrice,
            'delivery_time' => $deliveryTime
        ]]);
        
        // Redirecionar para a próxima etapa
        return redirect()->route('site.newcheckout.index', ['step' => 'payment']);
    }
    
    /**
     * Calcular opções de frete com base no CEP do endereço
     */
    public function calculateShipping(string $zipcode)
    {
        // Limpar o CEP, removendo caracteres não numéricos
        $zipcode = preg_replace('/[^0-9]/', '', $zipcode);
        
        // Adicionar a formatação padrão do CEP
        $zipcode = substr($zipcode, 0, 5) . '-' . substr($zipcode, 5, 3);
        
        // Obter o carrinho atual
        $cart = $this->cartService->getCurrentCart();
        
        // Carregar os itens do carrinho
        $cart->load(['items' => function($query) {
            $query->where('saved_for_later', false)
                  ->with(['vinylMaster' => function($q) {
                      $q->with(['vinylSec']);
                  }]);
        }]);
        
        // Preparar itens para a API de frete
        $cartItems = [];
        foreach ($cart->items as $item) {
            if (!$item->saved_for_later && $item->vinylMaster) {
                // Adicione as dimensões e peso de cada produto
                $cartItems[] = [
                    'id' => $item->vinylMaster->id,
                    'width' => 32, // Largura média de um vinil em cm
                    'height' => 32, // Altura média de um vinil em cm
                    'length' => 1, // Espessura média de um vinil em cm
                    'weight' => 0.3, // Peso médio de um vinil em kg
                    'quantity' => $item->quantity,
                    'insurance_value' => $item->vinylMaster->vinylSec->price ?? 100
                ];
            }
        }
        
        if (empty($cartItems)) {
            return;
        }
        
        try {
            // Chamar o serviço de cálculo de frete
            $shippingQuote = $this->melhorEnvio->getOrCalculateShipping($zipcode, $cartItems);
            
            if ($shippingQuote && isset($shippingQuote['quote_token'])) {
                // Armazenar o token da cotação na sessão para uso posterior
                session(['shipping_quote_token' => $shippingQuote['quote_token']]);
                
                // Se houver um serviço selecionado anteriormente, verificar se ainda está disponível
                $selectedShipping = session('selected_shipping');
                
                if ($selectedShipping) {
                    $serviceId = $selectedShipping['id'];
                    $serviceStillAvailable = false;
                    
                    // Verificar se o serviço selecionado anteriormente ainda está disponível
                    if (isset($shippingQuote['options']) && is_array($shippingQuote['options'])) {
                        foreach ($shippingQuote['options'] as $service) {
                            if ($service['id'] == $serviceId) {
                                $serviceStillAvailable = true;
                                
                                // Atualizar o preço do serviço selecionado
                                session(['selected_shipping' => [
                                    'id' => $serviceId,
                                    'price' => $service['price'],
                                    'delivery_time' => $service['delivery_time'] ?? null
                                ]]);
                                
                                break;
                            }
                        }
                    }
                    
                    // Se o serviço não estiver mais disponível, limpar a seleção
                    if (!$serviceStillAvailable) {
                        session()->forget('selected_shipping');
                    }
                }
                
                return $shippingQuote;
            }
        } catch (\Exception $e) {
            Log::error('Erro ao calcular frete: ' . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Recalcular o frete com base em um novo CEP
     */
    public function recalculateShipping(Request $request)
    {
        $zipcode = $request->input('zipcode');
        
        if (!$zipcode) {
            return response()->json([
                'success' => false,
                'message' => 'CEP é obrigatório'
            ]);
        }
        
        try {
            $shippingQuote = $this->calculateShipping($zipcode);
            
            if ($shippingQuote) {
                return response()->json([
                    'success' => true,
                    'services' => $shippingQuote['services'] ?? [],
                    'quote_token' => $shippingQuote['quote_token'] ?? null
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível calcular o frete para este CEP'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao recalcular frete: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao calcular o frete: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Processar o envio do formulário de pagamento no fluxo de etapas.
     * @deprecated Use processPayment() do fluxo simplificado
     */
    public function processPaymentStep(Request $request)
    {
        $paymentMethod = $request->input('payment_method');
        
        if (!$paymentMethod || !in_array($paymentMethod, ['credit_card', 'pix', 'boleto'])) {
            return redirect()->route('site.newcheckout.index', ['step' => 'payment'], false)
                ->with('error', 'Por favor, selecione um método de pagamento válido.');
        }
        
        // Armazenar o método de pagamento na sessão
        session(['checkout_payment_method' => $paymentMethod]);
        
        // Processar informações adicionais específicas do método de pagamento
        if ($paymentMethod == 'credit_card') {
            $installments = $request->input('installments', 1);
            session(['checkout_payment_installments' => $installments]);
        }
        
        // Redirecionar para a próxima etapa
        return redirect()->route('site.newcheckout.index', ['step' => 'summary'], false);
    }
    
    /**
     * Finalizar o pedido a partir do resumo.
     */
    public function finalizeOrder(Request $request)
    {
        // Verificar se todas as informações necessárias estão disponíveis
        if (!session('checkout_address_id')) {
            return redirect()->route('site.newcheckout.index', ['step' => 'address'], false)
                ->with('error', 'Por favor, selecione um endereço de entrega.');
        }
        
        if (!session('selected_shipping')) {
            return redirect()->route('site.newcheckout.index', ['step' => 'shipping'], false)
                ->with('error', 'Por favor, selecione uma opção de frete.');
        }
        
        if (!session('checkout_payment_method')) {
            return redirect()->route('site.newcheckout.index', ['step' => 'payment'], false)
                ->with('error', 'Por favor, selecione um método de pagamento.');
        }
        
        try {
            // Recuperar os dados necessários
            $cart = $this->cartService->getCurrentCart();
            $addressId = session('checkout_address_id');
            $paymentMethod = session('checkout_payment_method');
            $shippingQuoteToken = session('shipping_quote_token');
            
            // Verificar o carrinho e a cotação de frete
            if (!$cart || $cart->items()->where('saved_for_later', false)->count() === 0) {
                return redirect()->route('site.cart.index', [], false)
                    ->with('error', 'Seu carrinho está vazio.');
            }
            
            if (!$shippingQuoteToken) {
                return redirect()->route('site.newcheckout.index', ['step' => 'shipping'], false)
                    ->with('error', 'Cotação de frete inválida.');
            }
            
            // Criar o pedido usando o OrderService
            $order = $this->orderService->createOrder($cart, $addressId, $shippingQuoteToken, $paymentMethod);
            
            // Limpar os dados do checkout da sessão
            session()->forget([
                'checkout_address_id',
                'selected_shipping',
                'shipping_quote_token',
                'checkout_payment_method',
                'checkout_payment_installments'
            ]);
            
            // Redirecionar para a página de confirmação
            return redirect()->route('site.newcheckout.confirmation', ['orderId' => $order->id], false);
            
        } catch (\Exception $e) {
            Log::error('Erro ao finalizar pedido: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'cart_id' => $this->cartService->getCurrentCart()->id ?? null,
                'exception' => $e
            ]);
            
            return redirect()->route('site.newcheckout.index', ['step' => 'summary'], false)
                ->with('error', 'Ocorreu um erro ao finalizar seu pedido: ' . $e->getMessage());
        }
    }
    
    /**
     * Exibir a página de confirmação após finalizar o pedido.
     */
    public function confirmation(string $orderId)
    {
        $order = $this->orderService->getUserOrder($orderId);
        
        if (!$order) {
            return redirect()->route('site.home', [], false)
                ->with('error', 'Pedido não encontrado.');
        }
        
        return view('site.newcheckout.confirmation', [
            'order' => $order,
        ]);
    }
}
