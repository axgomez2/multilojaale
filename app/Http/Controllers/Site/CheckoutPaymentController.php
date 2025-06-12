<?php

namespace App\Http\Controllers\Site;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Enums\ShippingStatus;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingQuote;
use App\Models\VinylSec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class CheckoutPaymentController extends Controller
{
    /**
     * Constructor do controlador
     */
    public function __construct()
    {
        $this->middleware(['auth']);
        
        // Configurar o SDK do Mercado Pago se necessário
        if (class_exists('\MercadoPago\MercadoPagoConfig')) {
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
        }
    }
    
    /**
     * Criar pedido e redirecionar para página de pagamento
     */
    public function createOrder(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $selectedAddress = Session::get('selected_address');
            $selectedShipping = Session::get('selected_shipping');
            
            // Verificar se tem endereço e opção de frete selecionados
            if (!$selectedAddress || !isset($selectedShipping['id'])) {
                return redirect()->route('site.shipping.index')
                    ->with('error', 'Selecione um endereço e uma opção de frete para continuar.');
            }
            
            // Obter o carrinho do usuário
            $cart = Cart::where('user_id', $user->id)->first();
            
            if (!$cart) {
                return redirect()->route('site.cart.index')
                    ->with('error', 'Carrinho não encontrado.');
            }
            
            // Obter os itens do carrinho
            $cartItems = $user->cartItems()
                ->with(['product.productable', 'vinylMaster.vinylSec', 'vinylMaster.artists'])
                ->where('saved_for_later', false)
                ->get();
            
            // Se não tiver itens no carrinho, redirecionar para o carrinho
            if ($cartItems->isEmpty()) {
                return redirect()->route('site.cart.index')
                    ->with('error', 'Seu carrinho está vazio.');
            }
            
            // Obter o endereço selecionado
            $address = Address::where('id', $selectedAddress)
                ->where('user_id', $user->id)
                ->first();
            
            if (!$address) {
                return redirect()->route('site.shipping.index')
                    ->with('error', 'Endereço não encontrado.');
            }
            
            // Verificar se já existe um pedido pendente para este usuário
            $existingOrder = Order::where('user_id', $user->id)
                ->where('status', OrderStatus::PENDING->value)
                ->where('payment_status', PaymentStatus::PENDING->value)
                ->latest()
                ->first();
                
            Log::info('Verificando pedido existente', [
                'user_id' => $user->id,
                'existing_order' => $existingOrder ? $existingOrder->id : 'Nenhum'
            ]);
            
            // Verificar se precisa criar uma nova cotação de frete ou usar uma existente
            if ($existingOrder && !$existingOrder->items()->exists()) {
                // Se o pedido existente não tem itens, podemos reutilizar a cotação de frete
                $shippingQuote = ShippingQuote::find($existingOrder->shipping_quote_id);
                
                // Verificar se a cotação de frete é válida e tem os mesmos itens
                $sameItems = $shippingQuote && $shippingQuote->cart_items_hash === md5(json_encode($cartItems->pluck('id')->toArray()));
                
                if (!$sameItems) {
                    // Se os itens mudaram, criar uma nova cotação
                    $shippingQuote = new ShippingQuote();
                }
            } else {
                // Criar uma nova cotação de frete
                $shippingQuote = new ShippingQuote();
            }
            
            // Configurar a cotação de frete
            $shippingQuote->quote_token = (string) Str::uuid();
            $shippingQuote->user_id = $user->id;
            $shippingQuote->cart_items_hash = md5(json_encode($cartItems->pluck('id')->toArray()));
            
            // Preparar itens do carrinho para salvar
            $cartItemsData = $cartItems->map(function($item) {
                return [
                    'id' => $item->id,
                    'quantity' => $item->quantity,
                    'product_id' => $item->product_id ?? null,
                    'vinyl_master_id' => $item->vinyl_master_id ?? null
                ];
            })->toArray();
            
            $shippingQuote->cart_items = json_encode($cartItemsData);
            
            // Dados de CEP
            $shippingQuote->zip_from = config('melhorenvio.from.postal_code');
            $shippingQuote->zip_to = $address->zipcode;
            
            // Produtos e resposta da API
            $shippingOptions = Session::get('shipping_options', []);
            $shippingQuote->products = json_encode($shippingOptions['products'] ?? []);
            $shippingQuote->api_response = json_encode($shippingOptions);
            $shippingQuote->options = json_encode($shippingOptions);
            
            // Opção selecionada
            $shippingQuote->selected_service_id = $selectedShipping['id'];
            $shippingQuote->selected_price = $selectedShipping['price'];
            $shippingQuote->selected_delivery_time = $selectedShipping['days'] ?? null;
            
            // Expiração (7 dias)
            $shippingQuote->expires_at = now()->addDays(7);
            $shippingQuote->save();
            
            // Decidir se vamos usar um pedido existente ou criar um novo
            if ($existingOrder) {
                $order = $existingOrder;
                
                // Verificar se há alterações nos dados de envio ou frete
                $shippingChanged = $order->shipping != $selectedShipping['price'] || 
                                  $order->shipping_method != $selectedShipping['name'] ||
                                  $order->shipping_address_id != $address->id;
                
                // Verificar se os itens do carrinho mudaram
                $cartItemsHash = md5(json_encode($cartItems->pluck('id')->toArray()));
                $orderItemsChanged = true;
                
                if ($order->shippingQuote && $order->shippingQuote->cart_items_hash === $cartItemsHash) {
                    $orderItemsChanged = false;
                }
                
                if ($shippingChanged || $orderItemsChanged) {
                    Log::info('Atualizando pedido existente com novos dados', [
                        'order_id' => $order->id,
                        'shipping_changed' => $shippingChanged,
                        'items_changed' => $orderItemsChanged
                    ]);
                    
                    // Atualizar informações de envio
                    $order->shipping = $selectedShipping['price'];
                    $order->shipping_quote_id = $shippingQuote->id;
                    $order->shipping_address_id = $address->id;
                    $order->billing_address_id = $address->id;
                    $order->shipping_method = $selectedShipping['name'];
                    
                    // Limpar itens existentes para recriar
                    if ($orderItemsChanged) {
                        $order->items()->delete();
                    } else {
                        // Se apenas o frete mudou, não precisamos recriar os itens
                        // Apenas atualizar o total
                        $order->total = $order->subtotal + $order->shipping - $order->discount;
                        $order->save();
                        
                        return redirect()->route('site.checkout.payment', $order->id);
                    }
                } else {
                    // Se não houve alterações, apenas redirecionar para a página de pagamento
                    Log::info('Usando pedido existente sem alterações', ['order_id' => $order->id]);
                    return redirect()->route('site.checkout.payment', $order->id);
                }
            } else {
                // Criar um novo pedido
                $order = new Order();
                $order->id = (string) Str::uuid();
                $order->order_number = 'PED-' . date('Ymd') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT);
                $order->user_id = $user->id;
                $order->status = OrderStatus::PENDING->value;
                $order->payment_status = PaymentStatus::PENDING->value;
                $order->shipping_status = ShippingStatus::PENDING->value;
                
                // Valores monetários - inicializa com zero para recalcular depois
                $order->subtotal = 0; // Será calculado após criar os itens
                $order->shipping = $selectedShipping['price'];
                $order->discount = $cart->discount ?? 0;
                $order->tax = 0; // Impostos, se houver
                $order->total = $order->shipping; // Inicializa com o valor do frete, será atualizado após calcular o subtotal
                
                // Informações de envio
                $order->shipping_quote_id = $shippingQuote->id;
                $order->shipping_address_id = $address->id;
                $order->billing_address_id = $address->id; // Usando o mesmo endereço para faturamento
                $order->shipping_method = $selectedShipping['name'];
                
                // Informações do cliente
                $order->customer_ip_address = $request->ip();
                $order->customer_user_agent = $request->userAgent();
                
                Log::info('Criando novo pedido', ['order_id' => $order->id]);
            }
            
            $order->save();
            
            // Criar os itens do pedido
            $subtotal = 0;
            foreach ($cartItems as $cartItem) {
                $orderItem = new OrderItem();
                $orderItem->id = (string) Str::uuid();
                $orderItem->order_id = $order->id;
                
                // Log para depuração
                Log::info('Processando item do carrinho', [
                    'cart_item_id' => $cartItem->id,
                    'has_vinyl_master' => $cartItem->vinylMaster ? true : false,
                    'has_product' => $cartItem->product ? true : false,
                ]);
                
                if ($cartItem->vinylMaster) {
                    $orderItem->vinyl_master_id = $cartItem->vinylMaster->id;
                    $orderItem->name = $cartItem->vinylMaster->title;
                    $orderItem->description = $cartItem->vinylMaster->description;
                    $orderItem->unit_price = $cartItem->vinylMaster->vinylSec->price;
                    
                    // Log do preço do vinil
                    Log::info('Preço do vinil', [
                        'vinyl_id' => $cartItem->vinylMaster->id,
                        'vinyl_title' => $cartItem->vinylMaster->title,
                        'vinyl_price' => $cartItem->vinylMaster->vinylSec->price,
                    ]);
                } else if ($cartItem->product) {
                    $orderItem->product_id = $cartItem->product->id;
                    $orderItem->name = $cartItem->product->name;
                    $orderItem->description = $cartItem->product->description;
                    $orderItem->unit_price = $cartItem->product->price;
                    
                    // Log do preço do produto
                    Log::info('Preço do produto', [
                        'product_id' => $cartItem->product->id,
                        'product_name' => $cartItem->product->name,
                        'product_price' => $cartItem->product->price,
                    ]);
                }
                
                $orderItem->quantity = $cartItem->quantity;
                $orderItem->total_price = $orderItem->unit_price * $orderItem->quantity;
                $orderItem->save();
                
                // Acumular o subtotal
                $subtotal += $orderItem->total_price;
                
                // Log do item salvo e subtotal parcial
                Log::info('Item do pedido salvo', [
                    'order_item_id' => $orderItem->id,
                    'name' => $orderItem->name,
                    'quantity' => $orderItem->quantity,
                    'unit_price' => $orderItem->unit_price,
                    'total_price' => $orderItem->total_price,
                    'subtotal_parcial' => $subtotal
                ]);
            }
            
            // Atualizar o subtotal e total do pedido
            $order->subtotal = $subtotal;
            $order->total = $subtotal + $order->shipping - $order->discount;
            $order->save();
            
            // Log final com os valores do pedido
            Log::info('Pedido atualizado com subtotal e total', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'subtotal' => $order->subtotal,
                'shipping' => $order->shipping,
                'discount' => $order->discount,
                'total' => $order->total,
                'item_count' => count($cartItems),
                'is_new_order' => !isset($existingOrder),
                'is_update' => isset($existingOrder)
            ]);
            
            DB::commit();
            
            // Redirecionar para a página de pagamento
            return redirect()->route('site.checkout.payment', ['order' => $order->id]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar pedido: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('site.shipping.index')
                ->with('error', 'Ocorreu um erro ao processar seu pedido. Por favor, tente novamente.');
        }
    }
    
    /**
     * Mostrar página de pagamento
     */
    public function show(Order $order)
    {
        // Verificar se o pedido pertence ao usuário atual
        if ($order->user_id !== Auth::id()) {
            return redirect()->route('site.cart.index')
                ->with('error', 'Este pedido não pertence ao usuário logado.');
        }
        
        // Verificar se o pagamento já foi aprovado ou está pendente
        if (in_array($order->payment_status, [PaymentStatus::APPROVED->value, PaymentStatus::PENDING->value])) {
            // Verificar se existe um pagamento associado
            $payment = Payment::where('order_id', $order->id)->first();
            
            if ($payment) {
                // Se for pagamento PIX, redirecionar para a página de PIX
                if ($payment->payment_method === 'pix') {
                    return redirect()->route('site.pix.show', ['order' => $order->id])
                        ->with('info', 'Este pedido já possui um pagamento PIX em andamento.');
                }
                
                // Para outros métodos, redirecionar para a página de detalhes do pedido
                return redirect()->route('site.account.order', ['orderId' => $order->id])
                    ->with('info', 'Este pedido já possui um pagamento em processamento ou aprovado.');
            }
        }
        
        // Carregar relacionamentos necessários
        $order->load(['items', 'shippingAddress', 'user']);
        
        return view('site.checkout.payment', [
            'order' => $order
        ]);
    }
    
    /**
     * Processar pagamento
     */
    public function process(Request $request, $order_id)
    {
        try {
            // Buscar o pedido
            $order = Order::findOrFail($order_id);
            
            // Validar se o pedido pertence ao usuário logado
            if ($order->user_id !== Auth::id()) {
                return response()->json([
                    'error' => 'Você não tem permissão para processar este pedido',
                ], 403);
            }
            
            // Obter os dados do pagamento enviados pelo Payment Brick
            $paymentData = $request->all();
            
            Log::info('Dados de pagamento recebidos', [
                'order_id' => $order->id,
                'payment_data' => json_encode($paymentData)
            ]);
            
            // Criar o cliente de pagamento do Mercado Pago
            $client = new PaymentClient();
            
            // Preparar os dados do pagamento
            $payment_data = [
                'transaction_amount' => (float) $order->total,
                'description' => "Pedido #{$order->order_number}",
                'external_reference' => $order->id,
                'notification_url' => config('services.mercadopago.webhook_url'),
            ];
            
            // Verificar estrutura aninhada para encontrar o payment_method_id
            $paymentMethodId = null;
            
            // Verificar diretamente no nível principal
            if (isset($paymentData['payment_method_id'])) {
                $paymentMethodId = $paymentData['payment_method_id'];
            }
            // Verificar dentro de formData se existir
            elseif (isset($paymentData['formData']) && isset($paymentData['formData']['payment_method_id'])) {
                $paymentMethodId = $paymentData['formData']['payment_method_id'];
            }
            // Verificar pelo tipo de pagamento
            elseif (isset($paymentData['paymentType']) && $paymentData['paymentType'] === 'bank_transfer') {
                $paymentMethodId = 'pix';
            }
            elseif (isset($paymentData['selectedPaymentMethod']) && $paymentData['selectedPaymentMethod'] === 'bank_transfer') {
                $paymentMethodId = 'pix';
            }
            
            Log::info('Método de pagamento identificado', [
                'payment_method_id' => $paymentMethodId
            ]);
            
            // Definir o método de pagamento e dados do pagador
            if ($paymentMethodId) {
                $payment_data['payment_method_id'] = $paymentMethodId;
                
                // Dados para cartão de crédito
                if (isset($paymentData['token'])) {
                    $payment_data['token'] = $paymentData['token'];
                    $payment_data['installments'] = $paymentData['installments'] ?? 1;
                    
                    // Adicionar dados do pagador para cartão
                    if (isset($paymentData['payer'])) {
                        $payment_data['payer'] = [
                            'email' => $order->user->email,
                            'identification' => [
                                'type' => $paymentData['payer']['identification']['type'] ?? null,
                                'number' => $paymentData['payer']['identification']['number'] ?? null,
                            ],
                        ];
                    } else {
                        $payment_data['payer'] = [
                            'email' => $order->user->email,
                        ];
                    }
                }
                // Dados para PIX
                elseif (strtolower($paymentMethodId) === 'pix') {
                    $payment_data['payment_method_id'] = 'pix';
                    $payment_data['payer'] = [
                        'email' => $order->user->email,
                        'first_name' => $order->user->name,
                        'last_name' => '',
                    ];
                }
                // Dados para outros métodos
                else {
                    $payment_data['payer'] = [
                        'email' => $order->user->email,
                        'first_name' => $order->user->name,
                        'last_name' => '',
                    ];
                }
            } else {
                // Se não conseguiu identificar o método de pagamento, retornar erro
                return response()->json([
                    'error' => true,
                    'message' => 'Método de pagamento não identificado'
                ], 400);
            }
        
            Log::info('Enviando dados de pagamento para o Mercado Pago', [
                'payment_data' => json_encode($payment_data)
            ]);
            
            // Processar o pagamento
            $payment = $client->create($payment_data);
            
            // Atualizar o status do pedido
            switch ($payment->status) {
                case 'approved':
                    $order->status = OrderStatus::PAYMENT_APPROVED;
                    $paymentStatus = PaymentStatus::APPROVED->value;
                    break;
                case 'pending':
                case 'in_process':
                    $order->status = OrderStatus::PENDING;
                    $paymentStatus = PaymentStatus::PENDING->value;
                    Log::info('Pagamento pendente ou em processamento', [
                        'order_id' => $order->id,
                        'payment_id' => $payment->id,
                        'status' => $payment->status,
                        'status_detail' => $payment->status_detail ?? 'Sem detalhes'
                    ]);
                    break;
                case 'rejected':
                    $order->status = OrderStatus::CANCELED;
                    $paymentStatus = PaymentStatus::DECLINED->value;
                    Log::warning('Pagamento rejeitado pelo Mercado Pago', [
                        'order_id' => $order->id,
                        'payment_id' => $payment->id,
                        'status_detail' => $payment->status_detail ?? 'Sem detalhes'
                    ]);
                    break;
                default:
                    $order->status = OrderStatus::PENDING;
                    $paymentStatus = PaymentStatus::PENDING->value;
            }
            
            // Salvar o pedido com o status atualizado
            $order->save();
            
            // Verificar se já existe um pagamento para este pedido
            try {
                $existingPayment = Payment::where('order_id', $order->id)
                    ->where('transaction_id', $payment->id)
                    ->first();
                
                // Preparar dados específicos do PIX se for o caso
                $pixData = null;
                if ($payment->payment_method_id === 'pix') {
                    $pixData = [
                        'qr_code' => $payment->point_of_interaction->transaction_data->qr_code ?? null,
                        'qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64 ?? null,
                        'ticket_url' => $payment->point_of_interaction->transaction_data->ticket_url ?? null,
                    ];
                }
                
                $paymentData = [
                    'order_id' => $order->id,
                    'gateway_code' => 'mercadopago',
                    'method' => $payment->payment_method_id,
                    'status' => $paymentStatus,
                    'transaction_id' => $payment->id,
                    'payment_method_id' => $payment->payment_method_id,
                    'amount' => $payment->transaction_amount,
                    'gateway_data' => json_encode($payment),
                    'payment_data' => $pixData ? json_encode($pixData) : null,
                    'paid_at' => $payment->status === 'approved' ? now() : null
                ];
                
                if ($existingPayment) {
                    // Atualizar o pagamento existente
                    $existingPayment->update($paymentData);
                    Log::info('Pagamento existente atualizado', ['payment_id' => $existingPayment->id]);
                } else {
                    // Criar um novo registro de pagamento
                    Payment::create($paymentData);
                    Log::info('Novo registro de pagamento criado');
                }
                
                // Atualizar estoque e limpar carrinho para pagamentos aprovados ou pendentes
                if ($payment->status === 'approved' || $payment->status === 'pending' || $payment->status === 'in_process') {
                    $this->updateInventoryAndClearCart($order);
                }
                
                Log::info('Registro de pagamento criado com sucesso', [
                    'order_id' => $order->id,
                    'payment_id' => $payment->id,
                    'status' => $payment->status
                ]);
            } catch (\Exception $e) {
                Log::error('Erro ao criar registro de pagamento', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
            
            Log::info('Pagamento processado com sucesso', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'status' => $payment->status
            ]);
            
            // Verificar se o método de pagamento é PIX
            if ($payment->payment_method_id === 'pix') {
                // Redirecionar para a página de PIX dedicada
                return redirect()->route('site.pix.show', $order->id);
            } else {
                // Para outros métodos de pagamento, retornar resposta JSON
                $response = [
                    'status' => $payment->status,
                    'payment_id' => $payment->id,
                    'payment_method' => $payment->payment_method_id
                ];
                
                return response()->json($response);
            }
            
        } catch (MPApiException $e) {
            // Obter detalhes do erro da API
            $apiResponse = $e->getApiResponse();
            $responseContent = 'Sem detalhes';
            $errorDetail = 'Erro desconhecido';
            
            if ($apiResponse) {
                $responseContent = json_encode($apiResponse);
                
                // Tentar extrair a mensagem de erro específica
                if (method_exists($apiResponse, 'getContent')) {
                    $content = $apiResponse->getContent();
                    if (is_array($content) && isset($content['message'])) {
                        $errorDetail = $content['message'];
                    } elseif (is_array($content) && isset($content['error'])) {
                        $errorDetail = $content['error'];
                    } elseif (is_string($content)) {
                        $errorDetail = $content;
                    }
                }
            }
            
            Log::error('Erro na API do Mercado Pago', [
                'order_id' => $order_id,
                'error' => $e->getMessage(),
                'error_detail' => $errorDetail,
                'api_response' => $responseContent,
                'status_code' => $e->getCode()
            ]);
            
            return response()->json([
                'error' => 'Erro ao processar pagamento',
                'message' => 'Ocorreu um erro ao processar seu pagamento. Por favor, tente novamente.',
                'status' => 'failed'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento', [
                'order_id' => $order_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Erro ao processar pagamento',
                'message' => 'Não foi possível processar seu pagamento. Por favor, verifique os dados do cartão e tente novamente.',
                'status' => 'failed'
            ], 500);
        }
    }
    
    /**
     * Atualiza o estoque dos produtos e limpa o carrinho após pagamento bem-sucedido ou pendente
     *
     * @param Order $order
     * @return void
     */
    private function updateInventoryAndClearCart(Order $order)
    {
        try {
            // Carregar os itens do pedido com seus relacionamentos
            $order->load(['items.vinylMaster']);
            
            // Atualizar o estoque para cada item do pedido
            foreach ($order->items as $item) {
                // Verificar se o item tem um vinylMaster associado
                if ($item->vinylMaster) {
                    // Carregar o VinylSec relacionado
                    $vinylSec = VinylSec::where('vinyl_master_id', $item->vinyl_master_id)->first();
                    
                    if ($vinylSec) {
                        // Decrementar o estoque
                        $newStock = max(0, $vinylSec->stock - $item->quantity);
                        $vinylSec->stock = $newStock;
                        $vinylSec->in_stock = $newStock > 0;
                        $vinylSec->save();
                        
                        Log::info('Estoque atualizado', [
                            'vinyl_master_id' => $item->vinyl_master_id,
                            'vinyl_sec_id' => $vinylSec->id,
                            'previous_stock' => $vinylSec->stock + $item->quantity,
                            'new_stock' => $vinylSec->stock,
                            'quantity' => $item->quantity
                        ]);
                    }
                }
            }
            
            // Limpar o carrinho do usuário
            if ($order->user_id) {
                $cart = Cart::where('user_id', $order->user_id)
                    ->where('is_default', true)
                    ->first();
                
                if ($cart) {
                    // Remover todos os itens do carrinho
                    CartItem::where('cart_id', $cart->id)->delete();
                    
                    // Resetar os totais do carrinho
                    $cart->subtotal = 0;
                    $cart->discount = 0;
                    $cart->shipping_cost = 0;
                    $cart->total = 0;
                    $cart->save();
                    
                    Log::info('Carrinho limpo após pagamento', [
                        'cart_id' => $cart->id,
                        'user_id' => $order->user_id
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar estoque ou limpar carrinho', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}