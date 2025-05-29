<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request as HttpRequest;
use App\Models\CartItem;
use App\Models\ShippingQuote;
use App\Services\CartService;
use App\Services\MelhorEnvio;
use App\Services\OrderService;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
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
     * Exibir a página de checkout.
     */
    public function index(HttpRequest $request)
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
        
        // Obter os itens ativos do carrinho (não salvos para depois)
        $cartItemCount = CartItem::where('cart_id', $cart->id)
            ->where('saved_for_later', false)
            ->count();
        
        \Log::info('Verificando itens do carrinho no checkout', [
            'cart_id' => $cart->id,
            'itemCount' => $cartItemCount,
            'relationalCount' => $cart->items()->count(),
            'activeItemCount' => $cart->items()->where('saved_for_later', false)->count()
        ]);
        
        if (!$cart || $cartItemCount === 0) {
            return redirect()->route('site.cart.index')
                ->with('error', 'Seu carrinho está vazio. Adicione produtos antes de prosseguir para o checkout.');
        }
        
        // Verificar se há frete selecionado na sessão
        $selectedShipping = session('selected_shipping');
        $shippingQuote = null;
        
        // Se temos frete selecionado na sessão, podemos prosseguir mesmo sem o token
        if ($selectedShipping) {
            \Log::info('Frete selecionado na sessão', ['selected_shipping' => $selectedShipping]);
            
            // Recuperar o token da cotação de frete
            $quoteToken = session('shipping_quote_token');
            
            if ($quoteToken) {
                $shippingQuote = ShippingQuote::where('quote_token', $quoteToken)->first();
            }
            
            // Se não encontrou a cotação mas temos a seleção, criar uma nova
            if (!$shippingQuote) {
                \Log::warning('Cotação de frete não encontrada, mas há seleção de frete na sessão. Criando nova cotação.');
                
                // Gerar novo token
                $quoteToken = \Illuminate\Support\Str::uuid()->toString();
                session(['shipping_quote_token' => $quoteToken]);
                
                // Obter itens do carrinho para a cotação
                $cartItems = $cart->items()->with('vinylMaster.vinylSec')->where('saved_for_later', false)->get();
                
                // Criar hash e preparar dados
                $cartItemsData = $cartItems->map(function($item) {
                    return [
                        'id' => $item->id,
                        'vinyl_master_id' => $item->vinyl_master_id,
                        'quantity' => $item->quantity
                    ];
                })->toArray();
                $cartItemsHash = md5(json_encode($cartItemsData));
                
                // Preparar produtos para o frete
                $preparedItems = [];
                foreach ($cartItems as $item) {
                    $preparedItems[] = [
                        'id' => $item->vinylMaster->id,
                        'quantity' => $item->quantity
                    ];
                }
                
                // Criar nova cotação com os dados disponíveis
                $shippingQuote = ShippingQuote::create([
                    'quote_token' => $quoteToken,
                    'user_id' => auth()->id(),
                    'session_id' => session()->getId(),
                    'cart_items_hash' => $cartItemsHash,
                    'cart_items' => json_encode($cartItemsData),
                    'zip_from' => config('shipping.zip_from', '09220360'),
                    'zip_to' => session('shipping_zip_code'),
                    'products' => json_encode($preparedItems),
                    'options' => json_encode(session('shipping_options', [])),
                    'selected_service_id' => $selectedShipping['id'],
                    'selected_price' => $selectedShipping['price'],
                    'selected_delivery_time' => $selectedShipping['delivery_time'] ?? null,
                    'expires_at' => now()->addDays(1)
                ]);
                
                \Log::info('Nova cotação de frete criada no checkout', [
                    'quote_token' => $quoteToken,
                    'selected_service_id' => $selectedShipping['id']
                ]);
            }
            
            // Atualizar ou garantir que a cotação tem o serviço selecionado
            if ($shippingQuote && !$shippingQuote->selected_service_id) {
                $shippingQuote->selected_service_id = $selectedShipping['id'];
                $shippingQuote->selected_price = $selectedShipping['price'];
                $shippingQuote->selected_delivery_time = $selectedShipping['delivery_time'] ?? null;
                $shippingQuote->save();
                
                \Log::info('Cotação de frete atualizada com serviço selecionado', [
                    'quote_token' => $quoteToken,
                    'selected_service_id' => $selectedShipping['id']
                ]);
            }
        } else {
            // Se não temos frete selecionado na sessão, verificar se há no banco
            $quoteToken = session('shipping_quote_token');
            
            if ($quoteToken) {
                $shippingQuote = ShippingQuote::where('quote_token', $quoteToken)->first();
            }
            
            // Se não tiver cotação de frete, redirecionar para o carrinho
            if (!$shippingQuote) {
                return redirect()->route('site.cart.index')
                    ->with('error', 'É necessário calcular o frete antes de prosseguir para o checkout.');
            }
            
            // Se a cotação não tem serviço selecionado, redirecionar
            if (!$shippingQuote->selected_service_id) {
                return redirect()->route('site.cart.index')
                    ->with('error', 'É necessário selecionar uma opção de frete antes de prosseguir para o checkout.');
            }
            
            // Recuperar opção selecionada do banco e salvar na sessão para coerência
            if ($shippingQuote->selected_service_id) {
                $shippingOptions = json_decode($shippingQuote->options, true);
                
                if ($shippingOptions) {
                    foreach ($shippingOptions as $option) {
                        if ($option['id'] == $shippingQuote->selected_service_id) {
                            session(['selected_shipping' => $option]);
                            break;
                        }
                    }
                }
            }
        }
        
        // Garantir que a etapa inicial seja sempre 'address' quando entramos no checkout
        if (!session()->has('checkout_step')) {
            session(['checkout_step' => 'address']);
        }
        
        // Se foi solicitado uma etapa específica via parâmetro GET
        if ($request->has('step')) {
            $requestedStep = $request->step;
            // Verificar se a etapa solicitada é válida
            if (in_array($requestedStep, ['address', 'shipping', 'payment', 'confirmation'])) {
                session(['checkout_step' => $requestedStep]);
            }
        }
        
        // Se foi solicitado um reset da etapa via parâmetro GET
        if ($request->has('reset_step')) {
            session(['checkout_step' => 'address']);
        }
        
        // Recuperar o passo atual do checkout
        $currentStep = session('checkout_step', 'address');
        
        // Verificar se os passos anteriores foram concluídos
        if ($currentStep == 'shipping' && !session('checkout_address_id')) {
            session(['checkout_step' => 'address']);
            $currentStep = 'address';
        } else if ($currentStep == 'payment' && (!session('checkout_address_id') || !isset($shippingQuote) || !$shippingQuote->selected_service_id)) {
            if (!session('checkout_address_id')) {
                session(['checkout_step' => 'address']);
                $currentStep = 'address';
            } else {
                session(['checkout_step' => 'shipping']);
                $currentStep = 'shipping';
            }
        } else if ($currentStep == 'confirmation' && (!session('checkout_address_id') || !isset($shippingQuote) || !$shippingQuote->selected_service_id || !session('checkout_payment_method'))) {
            if (!session('checkout_address_id')) {
                session(['checkout_step' => 'address']);
                $currentStep = 'address';
            } else if (!isset($shippingQuote) || !$shippingQuote->selected_service_id) {
                session(['checkout_step' => 'shipping']);
                $currentStep = 'shipping';
            } else {
                session(['checkout_step' => 'payment']);
                $currentStep = 'payment';
            }
        }
        
        // Recuperar endereços do usuário logado
        $addresses = collect([]);
        if (auth()->check()) {
            // Usar o relacionamento diretamente com get() para garantir uma coleção
            $addresses = auth()->user()->addresses()->get();
            
            // Se não há endereço selecionado na sessão mas o usuário tem endereços,
            // selecionar automaticamente o endereço padrão ou o primeiro endereço
            if (!session('checkout_address_id') && $addresses->count() > 0) {
                // Procurar pelo endereço padrão de entrega
                $defaultAddress = $addresses->first(function($address) {
                    return $address->is_default_shipping;
                });
                
                // Se não encontrou um padrão, usar o primeiro endereço
                $selectedAddress = $defaultAddress ?? $addresses->first();
                
                // Salvar na sessão
                session(['checkout_address_id' => $selectedAddress->id]);
            }
        }
        
        return view('site.checkout.index', [
            'cart' => $cart,
            'shippingQuote' => $shippingQuote,
            'currentStep' => $currentStep,
            'addresses' => $addresses,
        ]);
    }
    
    /**
     * Avançar para o próximo passo do checkout.
     */
    public function nextStep(HttpRequest $request)
    {
        $currentStep = session('checkout_step', 'address');
        $nextStep = '';
        
        switch ($currentStep) {
            case 'address':
                // Verificar se um endereço foi selecionado
                if (!session()->has('checkout_address_id')) {
                    return redirect()->route('site.checkout.index')
                        ->with('error', 'Por favor, selecione um endereço de entrega.');
                }
                $nextStep = 'shipping';
                break;
                
            case 'shipping':
                // Verificar se um método de frete foi selecionado
                if (!session()->has('selected_shipping')) {
                    return redirect()->route('site.checkout.index')
                        ->with('error', 'Por favor, selecione uma opção de frete.');
                }
                $nextStep = 'payment';
                break;
                
            case 'payment':
                // Verificar se um método de pagamento foi selecionado diretamente do formulário
                $paymentMethod = $request->input('payment_method');
                
                if (!$paymentMethod) {
                    return redirect()->route('site.checkout.index')
                        ->with('error', 'Por favor, selecione uma forma de pagamento.');
                }
                
                // Validar o método de pagamento
                if (!in_array($paymentMethod, ['credit_card', 'pix', 'boleto'])) {
                    return redirect()->route('site.checkout.index')
                        ->with('error', 'Método de pagamento inválido.');
                }
                
                // Armazenar o método de pagamento na sessão
                session(['checkout_payment_method' => $paymentMethod]);
                
                // Registrar informações adicionais conforme o método
                switch ($paymentMethod) {
                    case 'credit_card':
                        // No futuro, aqui serão processados os dados do cartão
                        // Por enquanto, apenas registramos o método
                        \Log::info('Método de pagamento selecionado: Cartão de Crédito');
                        break;
                        
                    case 'pix':
                        \Log::info('Método de pagamento selecionado: PIX');
                        break;
                        
                    case 'boleto':
                        \Log::info('Método de pagamento selecionado: Boleto');
                        break;
                }
                
                $nextStep = 'confirmation';
                break;
                
            default:
                $nextStep = 'address';
        }
        
        session(['checkout_step' => $nextStep]);
        
        return redirect()->route('site.checkout.index', [], false);
    }
    
    /**
     * Voltar para o passo anterior do checkout.
     */
    public function previousStep(HttpRequest $request)
    {
        $currentStep = session('checkout_step', 'address');
        $prevStep = '';
        
        switch ($currentStep) {
            case 'shipping':
                $prevStep = 'address';
                break;
            case 'payment':
                $prevStep = 'shipping';
                break;
            case 'confirmation':
                $prevStep = 'payment';
                break;
            default:
                $prevStep = 'address';
        }
        
        session(['checkout_step' => $prevStep]);
        
        return redirect()->route('site.checkout.index', [], false);
    }
    
    /**
     * Salvar o endereço selecionado na sessão.
     */
    public function selectAddress(HttpRequest $request)
    {
        $addressId = $request->input('address_id');
        
        if ($addressId) {
            session(['checkout_address_id' => $addressId]);
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 400);
    }
    
    /**
     * Exibir a página de confirmação após finalizar o pedido.
     */
    public function confirmation(string $orderId)
    {
        $order = $this->orderService->getUserOrder($orderId);
        
        if (!$order) {
            return redirect()->route('site.home')
                ->with('error', 'Pedido não encontrado.');
        }
        
        return view('site.checkout.confirmation', [
            'order' => $order,
        ]);
    }
}
