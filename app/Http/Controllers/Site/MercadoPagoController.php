<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\VinylSec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoController extends Controller
{
    public function __construct()
    {
        // Configurar o SDK do Mercado Pago
        $access_token = config('services.mercadopago.access_token');
        if (!empty($access_token)) {
            MercadoPagoConfig::setAccessToken($access_token);
            Log::info('SDK Mercado Pago inicializado com sucesso');
        } else {
            Log::error('Access token do Mercado Pago não configurado');
        }
    }
    
    public function createPreference(Order $order)
    {
        try {
            // Verificar se o token está configurado
            $access_token = config('services.mercadopago.access_token');
            $public_key = config('services.mercadopago.public_key');
            
            if (empty($access_token) || empty($public_key)) {
                Log::error('Credenciais do Mercado Pago não configuradas');
                return response()->json(['error' => 'Credenciais do Mercado Pago não configuradas'], 500);
            }
            
            // Garantir que o SDK está configurado com o token correto
            MercadoPagoConfig::setAccessToken($access_token);
            
            Log::info('Iniciando criação de preferência', [
                'order_id' => $order->id,
                'access_token_configured' => !empty($access_token),
                'public_key_configured' => !empty($public_key),
                'total' => $order->total
            ]);
            
            // Inicializar o cliente de preferência
            $client = new PreferenceClient();
            
            // Garantir que o order está com os relacionamentos carregados
            $order->load(['items', 'user', 'shippingAddress']);
            
            
            // Simplificar os itens para teste
            $items = [
                [
                    'id' => 'order-' . $order->id,
                    'title' => "Pedido #{$order->order_number}",
                    'description' => "Pedido na loja",
                    'quantity' => 1,
                    'unit_price' => (float) $order->total,
                    'currency_id' => 'BRL',
                ]
            ];
            
            // Usar URLs absolutas com o domínio do ngrok para garantir que sejam acessíveis
            $ngrok_domain = 'https://e952-2804-8acc-4019-3d00-51f5-dc3b-4fb-1cde.ngrok-free.app';
            
            $success_url = $ngrok_domain . '/mercadopago/success/' . $order->id;
            $failure_url = $ngrok_domain . '/mercadopago/failure/' . $order->id;
            $pending_url = $ngrok_domain . '/mercadopago/pending/' . $order->id;
            
            Log::info('URLs de retorno modificadas', [
                'success' => $success_url,
                'failure' => $failure_url,
                'pending' => $pending_url
            ]);
            
            // Simplificar ao máximo a configuração
            $preference_data = [
                'items' => $items,
                'payer' => [
                    'name' => $order->user->name,
                    'email' => $order->user->email,
                ],
                'back_urls' => [
                    'success' => $success_url,
                    'failure' => $failure_url,
                    'pending' => $pending_url,
                ],
                // Removendo auto_return completamente
                'external_reference' => $order->id,
                'notification_url' => $ngrok_domain . '/api/mercadopago/webhook',
            ];
            
            Log::info('Dados da preferência', [
                'preference_data' => json_encode($preference_data)
            ]);
            
            $preference = $client->create($preference_data);
            
            Log::info('Preferência criada com sucesso', [
                'preference_id' => $preference->id
            ]);
            
            return response()->json([
                'id' => $preference->id,
                'public_key' => $public_key,
                'items' => $items,
                'total' => $order->total,
            ]);
            
            
        } catch (MPApiException $e) {
            // Obter detalhes do erro da API
            $apiResponse = $e->getApiResponse();
            
            // Tentar extrair mais informações do erro
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
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'error_detail' => $errorDetail,
                'api_response' => $responseContent,
                'status_code' => $e->getCode()
            ]);
            
            return response()->json([
                'error' => 'Erro ao criar preferência de pagamento',
                'message' => $e->getMessage(),
                'error_detail' => $errorDetail,
                'details' => $responseContent
            ], 400);
        } catch (\Exception $e) {
            Log::error('Erro ao criar preferência no Mercado Pago', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Erro ao criar preferência de pagamento',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function processWebhook(Request $request)
    {
        try {
            Log::info('Webhook do Mercado Pago recebido', $request->all());
            
            $data = $request->all();
            
            // Verificar se é uma notificação de pagamento
            if (isset($data['action']) && $data['action'] === 'payment.created' || $data['action'] === 'payment.updated') {
                $paymentId = $data['data']['id'];
                
                // Buscar informações do pagamento
                $client = new PaymentClient();
                $payment = $client->get($paymentId);
                
                // Buscar o pedido pelo external_reference
                $orderId = $payment->external_reference;
                $order = Order::find($orderId);
                
                if (!$order) {
                    Log::error('Pedido não encontrado para pagamento', [
                        'payment_id' => $paymentId,
                        'external_reference' => $orderId
                    ]);
                    return response()->json(['error' => 'Pedido não encontrado'], 404);
                }
                
                // Atualizar status do pagamento
                switch ($payment->status) {
                    case 'approved':
                        $order->payment_status = 'paid';
                        $order->status = 'processing';
                        break;
                    case 'pending':
                    case 'in_process':
                        $order->payment_status = 'pending';
                        Log::info('Pagamento pendente ou em processamento via webhook', [
                            'order_id' => $order->id,
                            'payment_id' => $paymentId,
                            'status' => $payment->status
                        ]);
                        break;
                    case 'rejected':
                        $order->payment_status = 'failed';
                        Log::warning('Pagamento rejeitado via webhook', [
                            'order_id' => $order->id,
                            'payment_id' => $paymentId,
                            'status_detail' => $payment->status_detail ?? 'Sem detalhes'
                        ]);
                        break;
                    default:
                        $order->payment_status = 'pending';
                }
                
                $order->payment_id = $paymentId;
                $order->payment_method = $payment->payment_method_id;
                $order->payment_details = json_encode($payment);
                $order->save();
                
                // Verificar se já existe um pagamento para este pedido
                $existingPayment = Payment::where('order_id', $order->id)
                    ->where('transaction_id', $paymentId)
                    ->first();
                
                $paymentData = [
                    'order_id' => $order->id,
                    'gateway_code' => 'mercadopago',
                    'method' => $payment->payment_method_id,
                    'status' => $order->payment_status,
                    'transaction_id' => $paymentId,
                    'payment_method_id' => $payment->payment_method_id,
                    'amount' => $payment->transaction_amount,
                    'gateway_data' => json_encode($payment),
                    'paid_at' => $payment->status === 'approved' ? now() : null
                ];
                
                if ($existingPayment) {
                    // Atualizar o pagamento existente
                    $existingPayment->update($paymentData);
                    Log::info('Pagamento existente atualizado via webhook', ['payment_id' => $existingPayment->id]);
                } else {
                    // Criar um novo registro de pagamento
                    Payment::create($paymentData);
                    Log::info('Novo registro de pagamento criado via webhook');
                }
                
                // Atualizar estoque e limpar carrinho para pagamentos aprovados ou pendentes
                if ($payment->status === 'approved' || $payment->status === 'pending' || $payment->status === 'in_process') {
                    $this->updateInventoryAndClearCart($order);
                }
                
                Log::info('Status do pagamento atualizado', [
                    'order_id' => $order->id,
                    'payment_id' => $paymentId,
                    'status' => $payment->status
                ]);
                
                return response()->json(['success' => true]);
            }
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook do Mercado Pago', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'error' => 'Erro ao processar webhook',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function success(Order $order)
    {
        return view('site.checkout.success', compact('order'));
    }
    
    public function failure(Order $order)
    {
        return view('site.checkout.failure', compact('order'));
    }
    
    public function pending(Order $order)
    {
        return view('site.checkout.pending', compact('order'));
    }
    
    /**
     * Atualiza o estoque dos produtos e limpa os carrinhos após pagamento bem-sucedido ou pendente
     *
     * @param Order $order
     * @return void
     */
    private function updateInventoryAndClearCart(Order $order)
    {
        try {
            // Carregar os itens do pedido com seus relacionamentos
            $order->load(['items.vinylMaster']);
            
            // Lista de produtos que ficaram sem estoque após esta compra
            $outOfStockProductIds = [];
            
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
                        
                        // Se o produto ficou sem estoque, adicionar à lista
                        if ($newStock <= 0) {
                            $outOfStockProductIds[] = $item->vinyl_master_id;
                        }
                        
                        Log::info('Estoque atualizado via webhook', [
                            'vinyl_master_id' => $item->vinyl_master_id,
                            'vinyl_sec_id' => $vinylSec->id,
                            'previous_stock' => $vinylSec->stock + $item->quantity,
                            'new_stock' => $vinylSec->stock,
                            'quantity' => $item->quantity,
                            'in_stock' => $vinylSec->in_stock ? 'sim' : 'não'
                        ]);
                    }
                }
            }
            
            // IDs dos produtos vendidos neste pedido
            $soldProductIds = $order->items->pluck('vinyl_master_id')->toArray();
            
            // 1. Limpar o carrinho do usuário que fez a compra
            if ($order->user_id) {
                $cart = Cart::where('user_id', $order->user_id)
                    ->where('is_default', true)
                    ->first();
                
                if ($cart) {
                    // Carregar os itens do carrinho
                    $cartItems = CartItem::where('cart_id', $cart->id)->get();
                    
                    // Remover apenas os itens vendidos e os itens sem estoque
                    foreach ($cartItems as $cartItem) {
                        // Verificar se o item foi vendido neste pedido
                        $isSold = in_array($cartItem->vinyl_master_id, $soldProductIds);
                        
                        // Verificar se o item está sem estoque
                        $vinylSec = VinylSec::where('vinyl_master_id', $cartItem->vinyl_master_id)->first();
                        $outOfStock = !$vinylSec || $vinylSec->stock <= 0;
                        
                        // Verificar se o item não está salvo para depois
                        $notSavedForLater = !$cartItem->saved_for_later;
                        
                        // Remover apenas se for um item vendido ou sem estoque, e não estiver salvo para depois
                        if (($isSold || $outOfStock) && $notSavedForLater) {
                            $cartItem->delete();
                            
                            Log::info('Item removido do carrinho do comprador', [
                                'cart_id' => $cart->id,
                                'vinyl_master_id' => $cartItem->vinyl_master_id,
                                'reason' => $isSold ? 'item_sold' : 'out_of_stock'
                            ]);
                        }
                    }
                    
                    // Recalcular os totais do carrinho
                    $this->recalculateCartTotals($cart);
                }
            }
            
            // 2. Remover itens sem estoque dos carrinhos de todos os outros usuários
            if (!empty($outOfStockProductIds)) {
                // Buscar todos os itens de carrinho que contém produtos sem estoque
                $affectedCartItems = CartItem::whereIn('vinyl_master_id', $outOfStockProductIds)
                    ->where('saved_for_later', false) // Não remover itens salvos para depois
                    ->get();
                
                // Agrupar por cart_id para recalcular os totais depois
                $affectedCarts = [];
                
                foreach ($affectedCartItems as $cartItem) {
                    // Adicionar o cart_id à lista de carrinhos afetados
                    if (!in_array($cartItem->cart_id, $affectedCarts)) {
                        $affectedCarts[] = $cartItem->cart_id;
                    }
                    
                    // Remover o item do carrinho
                    $cartItem->delete();
                    
                    Log::info('Item sem estoque removido do carrinho de outro usuário', [
                        'cart_id' => $cartItem->cart_id,
                        'user_id' => $cartItem->user_id,
                        'vinyl_master_id' => $cartItem->vinyl_master_id
                    ]);
                }
                
                // Recalcular os totais para todos os carrinhos afetados
                foreach ($affectedCarts as $cartId) {
                    $cart = Cart::find($cartId);
                    if ($cart) {
                        $this->recalculateCartTotals($cart);
                    }
                }
                
                Log::info('Itens sem estoque removidos de todos os carrinhos', [
                    'out_of_stock_products' => $outOfStockProductIds,
                    'affected_carts' => count($affectedCarts)
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar estoque ou limpar carrinhos', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Recalcula os totais de um carrinho
     *
     * @param Cart $cart
     * @return void
     */
    private function recalculateCartTotals(Cart $cart)
    {
        // Recalcular os totais do carrinho
        $remainingItems = CartItem::where('cart_id', $cart->id)
            ->where('saved_for_later', false)
            ->get();
        
        $subtotal = $remainingItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
        
        $discount = $remainingItems->sum(function ($item) {
            return ($item->original_price - $item->price) * $item->quantity;
        });
        
        $cart->subtotal = $subtotal;
        $cart->discount = $discount;
        $cart->total = $subtotal;
        $cart->save();
        
        Log::info('Carrinho recalculado', [
            'cart_id' => $cart->id,
            'user_id' => $cart->user_id,
            'remaining_items' => $remainingItems->count(),
            'new_total' => $cart->total
        ]);
    }
    
    /**
     * Verifica o status de um pagamento PIX
     *
     * @param Order $order
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function checkStatus(Order $order)
    {
        try {
            // Verificar se o usuário tem permissão para acessar este pedido
            if (auth()->id() != $order->user_id) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Você não tem permissão para acessar este pedido.'
                    ], 403);
                }
                return redirect()->route('site.orders.index')
                    ->with('error', 'Você não tem permissão para acessar este pedido.');
            }
            
            // Buscar o pagamento mais recente deste pedido
            $payment = Payment::where('order_id', $order->id)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if (!$payment) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Não foi possível encontrar o pagamento para este pedido.'
                    ], 404);
                }
                return redirect()->route('site.mercadopago.pending', $order->id)
                    ->with('error', 'Não foi possível encontrar o pagamento para este pedido.');
            }
            
            // Se o pagamento não for PIX, retornar o status adequado
            if ($payment->method !== 'pix') {
                $status = $payment->status;
                $redirectRoute = 'site.mercadopago.pending';
                
                if ($status === 'approved') {
                    $redirectRoute = 'site.mercadopago.success';
                } elseif ($status === 'rejected' || $status === 'cancelled') {
                    $redirectRoute = 'site.mercadopago.failure';
                }
                
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'status' => $status,
                        'redirect' => route($redirectRoute, $order->id)
                    ]);
                }
                
                return redirect()->route($redirectRoute, $order->id);
            }
            
            // Verificar o status atual do pagamento no Mercado Pago
            $client = new PaymentClient();
            $mpPayment = $client->get($payment->transaction_id);
            
            // Atualizar o status do pagamento no banco de dados
            $payment->status = $mpPayment->status;
            $payment->save();
            
            // Preparar a resposta com base no status
            $status = $mpPayment->status;
            $redirectRoute = 'site.mercadopago.pending';
            $message = 'O pagamento ainda está sendo processado. Por favor, aguarde.';
            
            // Atualizar o status do pedido
            if ($status === 'approved') {
                $order->status = 'payment_approved';
                $order->save();
                
                // Atualizar estoque e limpar carrinho se ainda não foi feito
                $this->updateInventoryAndClearCart($order);
                
                $redirectRoute = 'site.mercadopago.success';
                $message = 'Pagamento aprovado com sucesso!';
            } elseif ($status === 'rejected' || $status === 'cancelled') {
                $order->status = 'canceled';
                $order->save();
                
                $redirectRoute = 'site.mercadopago.failure';
                $message = 'Pagamento foi rejeitado ou cancelado.';
            }
            
            // Retornar resposta adequada ao tipo de requisição
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'redirect' => route($redirectRoute, $order->id)
                ]);
            }
            
            return redirect()->route($redirectRoute, $order->id)
                ->with('info', $message);
            
        } catch (\Exception $e) {
            Log::error('Erro ao verificar status do pagamento PIX', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Ocorreu um erro ao verificar o status do pagamento. Por favor, tente novamente.'
                ], 500);
            }
            
            return redirect()->route('site.mercadopago.pending', $order->id)
                ->with('error', 'Ocorreu um erro ao verificar o status do pagamento. Por favor, tente novamente.');
        }
    }
}
