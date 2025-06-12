<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PixPaymentController extends Controller
{
    /**
     * Exibe a página de pagamento PIX
     *
     * @param int $orderId
     * @return \Illuminate\View\View
     */
    public function show($orderId)
    {
        try {
            // Buscar o pedido
            $order = Order::findOrFail($orderId);
            
            // Verificar se o usuário tem permissão para acessar este pedido
            if ($order->user_id != auth()->id()) {
                return redirect()->route('site.profile.orders.index')
                    ->with('error', 'Você não tem permissão para acessar este pedido.');
            }
            
            // Buscar o pagamento PIX associado ao pedido
            $payment = Payment::where('order_id', $order->id)
                ->where('method', 'pix')
                ->orderBy('created_at', 'desc')
                ->first();
            
            if (!$payment) {
                return redirect()->route('site.checkout.payment', $order->id)
                    ->with('error', 'Não foi encontrado um pagamento PIX para este pedido.');
            }
            
            // Obter dados do PIX
            $pixData = json_decode($payment->payment_data, true);
            
            // Renderizar a view com os dados
            return view('site.checkout.pix', [
                'order' => $order,
                'payment' => $payment,
                'pixData' => $pixData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao exibir página de pagamento PIX', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('site.profile.orders.index')
                ->with('error', 'Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente.');
        }
    }
}
