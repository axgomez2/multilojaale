<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\ShippingQuote;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected OrderService $orderService;
    
    /**
     * Create a new controller instance.
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    
    /**
     * Criar um novo pedido a partir do checkout.
     */
    public function store(Request $request)
    {
        try {
            // Obter o endereço e a cotação de frete da sessão
            $addressId = session('checkout_address_id');
            $shippingQuoteToken = session('shipping_quote_token');
            $paymentMethod = $request->input('selected_payment_method');
            
            if (!$addressId) {
                return redirect()->route('site.checkout.index')
                    ->with('error', 'Nenhum endereço selecionado. Por favor, escolha um endereço de entrega.');
            }
            
            if (!$shippingQuoteToken) {
                return redirect()->route('site.checkout.index')
                    ->with('error', 'Nenhuma opção de frete selecionada. Por favor, escolha uma opção de frete.');
            }
            
            if (!$paymentMethod) {
                return redirect()->route('site.checkout.index')
                    ->with('error', 'Nenhuma forma de pagamento selecionada. Por favor, escolha uma forma de pagamento.');
            }
            
            // Buscar o endereço e a cotação de frete
            $address = Address::findOrFail($addressId);
            $shippingQuote = ShippingQuote::where('quote_token', $shippingQuoteToken)->firstOrFail();
            
            // Verificar se o endereço pertence ao usuário
            if (auth()->check() && $address->user_id !== auth()->id()) {
                return redirect()->route('site.checkout.index')
                    ->with('error', 'Endereço não pertence ao usuário logado.');
            }
            
            // Adicionar o método de pagamento aos dados do pedido
            $paymentData = [
                'method' => $paymentMethod,
                'details' => []
            ];
            
            // Se for cartão de crédito, adicionar os detalhes
            if ($paymentMethod === 'credit_card') {
                $paymentData['details'] = [
                    'card_number' => $request->input('card_number'),
                    'card_expiration' => $request->input('card_expiration'),
                    'card_holder_name' => $request->input('card_holder_name'),
                    'installments' => $request->input('installments', 1)
                ];
            }
            
            // Criar o pedido
            $order = $this->orderService->createFromCart($shippingQuote, $address, $paymentData);
            
            if (!$order) {
                return redirect()->route('site.checkout.index')
                    ->with('error', 'Erro ao criar o pedido. Verifique se o carrinho está vazio.');
            }
            
            // Limpar os dados de checkout da sessão
            session()->forget([
                'checkout_step',
                'checkout_address_id',
                'shipping_quote_token'
            ]);
            
            // Redirecionar para a página de confirmação
            return redirect()->route('site.checkout.confirmation', ['orderId' => $order->id])
                ->with('success', 'Pedido criado com sucesso!');
            
        } catch (\Exception $e) {
            Log::error('Erro ao criar pedido: ' . $e->getMessage());
            
            return redirect()->route('site.checkout.index')
                ->with('error', 'Erro ao criar o pedido: ' . $e->getMessage());
        }
    }
    
    /**
     * Exibir detalhes de um pedido.
     */
    public function show(string $orderId)
    {
        $order = $this->orderService->getUserOrder($orderId);
        
        if (!$order) {
            return redirect()->route('site.account.orders')
                ->with('error', 'Pedido não encontrado.');
        }
        
        return view('site.account.order-details', [
            'order' => $order,
        ]);
    }
    
    /**
     * Listar os pedidos do usuário.
     */
    public function index()
    {
        $orders = $this->orderService->getUserOrders();
        
        return view('site.account.orders', [
            'orders' => $orders,
        ]);
    }
    
    /**
     * Cancelar um pedido.
     */
    public function cancel(Request $request, string $orderId)
    {
        $order = $this->orderService->getUserOrder($orderId);
        
        if (!$order) {
            return redirect()->route('site.account.orders')
                ->with('error', 'Pedido não encontrado.');
        }
        
        $success = $this->orderService->cancelOrder($order);
        
        if (!$success) {
            return redirect()->route('site.account.order', ['orderId' => $orderId])
                ->with('error', 'Não foi possível cancelar o pedido. Pedidos enviados ou entregues não podem ser cancelados.');
        }
        
        return redirect()->route('site.account.order', ['orderId' => $orderId])
            ->with('success', 'Pedido cancelado com sucesso.');
    }
}
