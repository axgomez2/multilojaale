<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingQuote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Create a new order from the cart.
     * 
     * @param ShippingQuote $shippingQuote The shipping quote containing shipping options
     * @param Address $address The delivery address
     * @param array $paymentData Payment data (method and details)
     * @return Order|null The created order or null if failed
     */
    public function createFromCart(ShippingQuote $shippingQuote, Address $address, array $paymentData = []): ?Order
    {
        // Obter o carrinho atual
        $cart = app(CartService::class)->getCurrentCart();
        
        if (!$cart || $cart->items()->count() === 0) {
            return null;
        }
        
        DB::beginTransaction();
        
        try {
            // Geração de número de pedido formatado
            $orderNumber = 'PED-' . date('Ymd') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT);
            
            // Criar o pedido com os campos corretos
            $order = new Order();
            $order->order_number = $orderNumber;
            $order->user_id = auth()->id();
            $order->session_id = session()->getId();
            
            // Definindo os status iniciais corretamente
            $order->status = OrderStatus::PENDING->value; // Usando ->value para string
            $order->payment_status = 'pending';
            $order->shipping_status = 'pending';
            
            // Valores monetários
            $order->subtotal = $cart->subtotal();
            $order->shipping = $shippingQuote->selected_price;
            $order->discount = 0;
            $order->tax = 0;
            $order->total = $cart->subtotal() + $shippingQuote->selected_price;
            
            // Informações de envio
            $order->shipping_quote_id = $shippingQuote->id;
            $order->shipping_address_id = $address->id; // Corrigido: usado shipping_address_id
            $order->billing_address_id = $address->id;  // Por padrão, usa o mesmo endereço para faturamento
            
            // Adicionar dados de pagamento, se fornecidos
            if (!empty($paymentData)) {
                $order->payment_method = $paymentData['method'] ?? null;
                
                // Se for PIX, aplicar desconto de 5%
                if (($paymentData['method'] ?? '') === 'pix') {
                    $discountAmount = $order->total * 0.05;
                    $order->discount = $discountAmount;
                    $order->total = $order->total - $discountAmount;
                }
                
                // Armazenando notas do cliente se fornecidas
                if (!empty($paymentData['customer_note'])) {
                    $order->customer_note = $paymentData['customer_note'];
                }
            }
            $order->save();
            
            // Criar os itens do pedido
            foreach ($cart->items as $item) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->vinyl_id = $item->vinyl_id;
                $orderItem->quantity = $item->quantity;
                $orderItem->unit_price = $item->vinyl->price;
                $orderItem->total_price = $item->vinyl->price * $item->quantity;
                $orderItem->save();
            }
            
            // Criar o registro de pagamento
            if (!empty($paymentData['method'])) {
                $payment = new \App\Models\Payment();
                $payment->order_id = $order->id;
                $payment->payment_method = $paymentData['method'];
                $payment->amount = $order->total;
                $payment->status = 'pending'; // Status inicial do pagamento
                
                // Processamento de detalhes específicos por método de pagamento
                if ($paymentData['method'] === 'credit_card' && !empty($paymentData['details'])) {
                    // Armazenar os últimos 4 dígitos e outros detalhes não sensíveis
                    $payment->details = json_encode([
                        'last_four' => $paymentData['details']['last_four'] ?? null,
                        'card_brand' => $paymentData['details']['card_brand'] ?? null,
                    ]);
                } else if ($paymentData['method'] === 'pix') {
                    $payment->details = json_encode(['discount_applied' => true]);
                }
                
                $payment->save();
            }
            
            // Disparar evento de criação de pedido (comentado até implementação)
            // event(new OrderCreated($order));
            
            // Limpar o carrinho
            $cart->items()->delete();
            $cart->delete();
            
            // Limpar dados de checkout da sessão
            session()->forget([
                'checkout_step',
                'checkout_address_id',
                'selected_shipping',
                'checkout_payment_method'
            ]);
            
            DB::commit();
            
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar pedido: ' . $e->getMessage());
            
            return null;
        }
    }
    
    /**
     * Cancel an order.
     */
    public function cancelOrder(Order $order): bool
    {
        // Verificar se o pedido pode ser cancelado
        if ($order->status === OrderStatus::SHIPPED || $order->status === OrderStatus::DELIVERED) {
            return false;
        }
        
        try {
            $order->status = OrderStatus::CANCELED;
            $order->save();
            
            // Disparar evento de cancelamento
            // event(new OrderCanceled($order));
            
            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar pedido: ' . $e->getMessage());
            
            return false;
        }
    }
    
    /**
     * Update the order status.
     */
    public function updateStatus(Order $order, OrderStatus $status): bool
    {
        try {
            $order->status = $status;
            $order->save();
            
            // Disparar evento de atualização de status
            // event(new OrderStatusUpdated($order, $status));
            
            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar status do pedido: ' . $e->getMessage());
            
            return false;
        }
    }
    
    /**
     * Get orders for the current user.
     */
    public function getUserOrders()
    {
        if (auth()->check()) {
            return Order::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return Order::where('session_id', session()->getId())
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    /**
     * Get a specific order for the current user.
     */
    public function getUserOrder(string $orderId)
    {
        $query = Order::where('id', $orderId);
        
        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $query->where('session_id', session()->getId());
        }
        
        return $query->first();
    }
}
