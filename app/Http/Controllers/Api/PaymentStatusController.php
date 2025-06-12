<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentStatusController extends Controller
{
    /**
     * Verifica o status do pagamento de um pedido
     *
     * @param string $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus($orderId)
    {
        try {
            // Buscar o pedido pelo ID
            $order = Order::find($orderId);
            
            if (!$order) {
                return response()->json([
                    'error' => true,
                    'message' => 'Pedido não encontrado',
                    'status' => 'not_found'
                ], 404);
            }
            
            // Buscar o último pagamento associado ao pedido
            $payment = Payment::where('order_id', $order->id)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if (!$payment) {
                return response()->json([
                    'error' => false,
                    'message' => 'Pagamento ainda não processado',
                    'status' => 'pending'
                ]);
            }
            
            // Retornar o status do pagamento
            return response()->json([
                'error' => false,
                'message' => 'Status do pagamento obtido com sucesso',
                'status' => $payment->status,
                'payment_id' => $payment->payment_id,
                'payment_method' => $payment->payment_method,
                'updated_at' => $payment->updated_at->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao verificar status do pagamento', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => 'Erro ao verificar status do pagamento',
                'status' => 'error'
            ], 500);
        }
    }
}
