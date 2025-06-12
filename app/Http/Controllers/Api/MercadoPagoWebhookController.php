<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MercadoPagoWebhookController extends Controller
{
    protected $mercadoPagoService;

    /**
     * Construtor do controlador
     */
    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
    }

    /**
     * Processa notificações de webhook do Mercado Pago
     */
    public function handleWebhook(Request $request)
    {
        // Log da requisição recebida para debug
        Log::channel('mercadopago')->info('Webhook recebido', $request->all());

        $type = $request->input('type');
        $data = $request->input('data');

        // Se não for notificação de pagamento, ignorar
        if ($type !== 'payment') {
            Log::channel('mercadopago')->info('Notificação não é de pagamento, ignorando');
            return response()->json(['message' => 'Notificação recebida, mas não é de pagamento'], 200);
        }

        // Verificar se o ID do pagamento está presente
        if (empty($data['id'])) {
            Log::channel('mercadopago')->error('ID do pagamento não informado');
            return response()->json(['message' => 'ID do pagamento não informado'], 400);
        }

        // Obter os detalhes do pagamento diretamente da API do Mercado Pago
        try {
            $paymentInfo = $this->mercadoPagoService->getPaymentInfo($data['id']);
            Log::channel('mercadopago')->info('Informações do pagamento obtidas', ['payment_info' => $paymentInfo]);

            // Verificar se tem um external_reference (nosso order_id)
            if (empty($paymentInfo['external_reference'])) {
                Log::channel('mercadopago')->error('External reference não encontrada');
                return response()->json(['message' => 'External reference não encontrada'], 400);
            }

            // Buscar o pedido pelo ID
            $order = Order::where('id', $paymentInfo['external_reference'])
                        ->orWhere('order_number', $paymentInfo['external_reference'])
                        ->first();

            if (!$order) {
                Log::channel('mercadopago')->error('Pedido não encontrado', [
                    'external_reference' => $paymentInfo['external_reference']
                ]);
                return response()->json(['message' => 'Pedido não encontrado'], 404);
            }

            // Atualizar o status do pagamento
            $payment = Payment::where('order_id', $order->id)->first();
            
            if (!$payment) {
                // Se não existir um pagamento, criar um novo
                $payment = new Payment();
                $payment->order_id = $order->id;
                $payment->payment_method = 'mercadopago';
            }

            $payment->external_id = $paymentInfo['id'];
            $payment->amount = $paymentInfo['transaction_amount'];

            // Mapear o status do Mercado Pago para nosso status de pagamento
            switch ($paymentInfo['status']) {
                case 'approved':
                    $payment->status = PaymentStatus::PAID->value;
                    $order->payment_status = PaymentStatus::PAID->value;
                    $order->status = OrderStatus::PROCESSING->value;
                    break;
                case 'in_process':
                case 'pending':
                    $payment->status = PaymentStatus::PENDING->value;
                    $order->payment_status = PaymentStatus::PENDING->value;
                    break;
                case 'rejected':
                    $payment->status = PaymentStatus::FAILED->value;
                    $order->payment_status = PaymentStatus::FAILED->value;
                    break;
                case 'cancelled':
                case 'refunded':
                    $payment->status = PaymentStatus::REFUNDED->value;
                    $order->payment_status = PaymentStatus::REFUNDED->value;
                    $order->status = OrderStatus::CANCELED->value;
                    break;
                default:
                    $payment->status = PaymentStatus::PENDING->value;
                    $order->payment_status = PaymentStatus::PENDING->value;
            }

            // Salvar detalhes adicionais do pagamento
            $payment->details = json_encode([
                'status_detail' => $paymentInfo['status_detail'] ?? null,
                'payment_method_id' => $paymentInfo['payment_method_id'] ?? null,
                'payment_type_id' => $paymentInfo['payment_type_id'] ?? null,
                'installments' => $paymentInfo['installments'] ?? null,
                'last_four_digits' => $paymentInfo['card']['last_four_digits'] ?? null,
                'card_brand' => $paymentInfo['card']['brand'] ?? null
            ]);

            $payment->save();
            $order->save();

            // Log das atualizações realizadas
            Log::channel('mercadopago')->info('Status de pagamento atualizado', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_status' => $payment->status,
                'order_status' => $order->status
            ]);

            // Retornar sucesso
            return response()->json([
                'success' => true,
                'message' => 'Status de pagamento atualizado com sucesso',
                'order_id' => $order->id,
                'payment_status' => $payment->status
            ]);
        } catch (\Exception $e) {
            Log::channel('mercadopago')->error('Erro ao processar webhook', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar notificação: ' . $e->getMessage()
            ], 500);
        }
    }
}
