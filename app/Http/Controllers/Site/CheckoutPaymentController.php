<?php

namespace App\Http\Controllers\Site;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckoutPaymentController extends Controller
{
    protected OrderService $orderService;
    protected PaymentService $paymentService;
    
    /**
     * Create a new controller instance.
     */
    public function __construct(
        OrderService $orderService,
        PaymentService $paymentService
    ) {
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
    }
    
    /**
     * Processar um pagamento.
     */
    public function process(Request $request, string $orderId)
    {
        // Validar a requisição
        $request->validate([
            'payment_method' => 'required|string|in:credit_card,pix,boleto',
        ]);
        
        // Buscar o pedido
        $order = Order::findOrFail($orderId);
        
        // Verificar se o pedido pertence ao usuário atual
        if (auth()->check() && $order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Este pedido não pertence ao usuário logado.'
            ], 403);
        } elseif (!auth()->check() && $order->session_id !== session()->getId()) {
            return response()->json([
                'success' => false,
                'message' => 'Este pedido não foi criado nesta sessão.'
            ], 403);
        }
        
        try {
            // Preparar os dados do pagamento
            $paymentData = [];
            
            // Se for cartão de crédito, adicionar os dados do cartão
            if ($request->payment_method === 'credit_card') {
                $request->validate([
                    'card_number' => 'required|string|size:19',
                    'card_holder_name' => 'required|string|max:255',
                    'card_expiration' => 'required|string|size:5',
                    'card_cvv' => 'required|string|size:3',
                    'installments' => 'required|integer|min:1|max:12',
                ]);
                
                $paymentData = [
                    'card_number' => preg_replace('/[^0-9]/', '', $request->card_number),
                    'card_holder_name' => $request->card_holder_name,
                    'card_expiration' => $request->card_expiration,
                    'card_cvv' => $request->card_cvv,
                    'installments' => $request->installments,
                ];
            }
            
            // Criar o pagamento
            $result = $this->paymentService->createPayment(
                $order,
                $request->payment_method,
                $paymentData
            );
            
            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'Erro ao processar o pagamento.'
                ], 500);
            }
            
            // Se for PIX ou boleto, retornar os dados de pagamento
            if ($request->payment_method === 'pix' || $request->payment_method === 'boleto') {
                return response()->json([
                    'success' => true,
                    'payment' => $result,
                    'redirect' => route('site.checkout.payment.waiting', [
                        'order_id' => $order->id,
                        'transaction_id' => $result['transaction_id'],
                    ]),
                ]);
            }
            
            // Para cartão de crédito, redirecionar para a página de confirmação
            return response()->json([
                'success' => true,
                'redirect' => route('site.checkout.confirmation', [
                    'order_id' => $order->id,
                ]),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar o pagamento: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Aguardar o pagamento (PIX ou boleto).
     */
    public function waiting(Request $request, string $orderId, string $transactionId)
    {
        // Buscar o pedido
        $order = Order::with('payment')->findOrFail($orderId);
        
        // Verificar se o pedido pertence ao usuário atual
        if (auth()->check() && $order->user_id !== auth()->id()) {
            return redirect()->route('site.home')
                ->with('error', 'Este pedido não pertence ao usuário logado.');
        } elseif (!auth()->check() && $order->session_id !== session()->getId()) {
            return redirect()->route('site.home')
                ->with('error', 'Este pedido não foi criado nesta sessão.');
        }
        
        // Verificar se o pagamento existe
        if (!$order->payment || $order->payment->transaction_id !== $transactionId) {
            return redirect()->route('site.home')
                ->with('error', 'Pagamento não encontrado.');
        }
        
        return view('site.checkout.waiting', [
            'order' => $order,
            'payment' => $order->payment,
        ]);
    }
    
    /**
     * Verificar o status do pagamento.
     */
    public function checkStatus(Request $request, string $orderId, string $transactionId)
    {
        try {
            // Buscar o pagamento
            $payment = Payment::where('order_id', $orderId)
                ->where('transaction_id', $transactionId)
                ->firstOrFail();
            
            // Verificar o status do pagamento
            $status = $this->paymentService->checkPaymentStatus($payment);
            
            return response()->json([
                'success' => true,
                'status' => $status,
                'redirect' => $status === 'approved' 
                    ? route('site.checkout.confirmation', ['order_id' => $orderId])
                    : null,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar status do pagamento: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Processar callback do gateway de pagamento.
     */
    public function callback(Request $request)
    {
        try {
            $result = $this->paymentService->processCallback($request);
            
            if (!$result['success']) {
                Log::error('Erro no callback de pagamento: ' . ($result['error'] ?? 'Erro desconhecido'));
                return redirect()->route('site.home')
                    ->with('error', 'Erro ao processar o pagamento.');
            }
            
            // Verificar se temos uma referência externa (ID do pedido)
            if (!isset($result['external_reference'])) {
                Log::error('Callback sem referência externa do pedido');
                return redirect()->route('site.home')
                    ->with('error', 'Erro ao processar o pagamento: referência do pedido não encontrada.');
            }
            
            // Buscar o pedido
            $order = Order::find($result['external_reference']);
            
            if (!$order) {
                Log::error('Pedido não encontrado: ' . $result['external_reference']);
                return redirect()->route('site.home')
                    ->with('error', 'Pedido não encontrado.');
            }
            
            // Atualizar o status do pedido
            if ($result['status'] === 'approved') {
                $this->orderService->updateStatus($order, OrderStatus::PAYMENT_APPROVED);
                
                return redirect()->route('site.checkout.confirmation', ['order_id' => $order->id])
                    ->with('success', 'Pagamento aprovado com sucesso!');
            }
            
            // Para outros status, manter o pedido como pendente
            return redirect()->route('site.checkout.payment.waiting', [
                'order_id' => $order->id,
                'transaction_id' => $result['transaction_id'],
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro no callback de pagamento: ' . $e->getMessage());
            
            return redirect()->route('site.home')
                ->with('error', 'Erro ao processar o pagamento: ' . $e->getMessage());
        }
    }
    
    /**
     * Processar webhook do gateway de pagamento.
     */
    public function webhook(Request $request)
    {
        try {
            Log::info('Webhook de pagamento recebido', $request->all());
            
            $result = $this->paymentService->processWebhook($request);
            
            if (!$result['success']) {
                Log::error('Erro no webhook de pagamento: ' . ($result['error'] ?? 'Erro desconhecido'));
                return response()->json(['error' => $result['error'] ?? 'Erro desconhecido'], 400);
            }
            
            // Verificar se temos uma referência externa (ID do pedido)
            if (!isset($result['external_reference'])) {
                Log::error('Webhook sem referência externa do pedido');
                return response()->json(['error' => 'Referência do pedido não encontrada'], 400);
            }
            
            // Buscar o pedido
            $order = Order::find($result['external_reference']);
            
            if (!$order) {
                Log::error('Pedido não encontrado: ' . $result['external_reference']);
                return response()->json(['error' => 'Pedido não encontrado'], 404);
            }
            
            // Atualizar o status do pedido
            if ($result['status'] === 'approved') {
                $this->orderService->updateStatus($order, OrderStatus::PAYMENT_APPROVED);
            } elseif ($result['status'] === 'canceled' || $result['status'] === 'declined') {
                $this->orderService->updateStatus($order, OrderStatus::CANCELED);
            }
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Erro no webhook de pagamento: ' . $e->getMessage());
            
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
