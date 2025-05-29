<?php

namespace App\Services;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\Payment\MercadoPagoGateway;
use App\Services\Payment\PagSeguroGateway;
use Exception;
use Illuminate\Http\Request;

class PaymentService
{
    protected array $gateways = [];
    
    /**
     * Load all available payment gateways.
     */
    public function __construct()
    {
        $this->loadGateways();
    }
    
    /**
     * Get the active payment gateway.
     *
     * @throws Exception if no active gateway is found
     */
    public function getActiveGateway(): PaymentGatewayInterface
    {
        $activeGateway = PaymentGateway::active()->first();
        
        if (!$activeGateway) {
            throw new Exception('Nenhum gateway de pagamento ativo');
        }
        
        if (!isset($this->gateways[$activeGateway->code])) {
            throw new Exception('Gateway de pagamento não implementado: ' . $activeGateway->code);
        }
        
        return $this->gateways[$activeGateway->code];
    }
    
    /**
     * Create a payment for the given order.
     */
    public function createPayment(Order $order, string $method, array $paymentData = []): array
    {
        $gateway = $this->getActiveGateway();
        $result = $gateway->createPayment($order, $method, $paymentData);
        
        // Salvar o pagamento no banco de dados
        $payment = new Payment([
            'order_id' => $order->id,
            'gateway' => $gateway->getName(),
            'method' => $method,
            'status' => $result['status'] ?? 'pending',
            'transaction_id' => $result['transaction_id'] ?? null,
            'amount' => $order->total,
            'gateway_data' => $result,
        ]);
        
        $payment->save();
        
        return $result;
    }
    
    /**
     * Process a callback from the payment gateway.
     */
    public function processCallback(Request $request): array
    {
        $gateway = $this->getActiveGateway();
        return $gateway->processCallback($request);
    }
    
    /**
     * Process a webhook notification from the payment gateway.
     */
    public function processWebhook(Request $request): array
    {
        $gateway = $this->getActiveGateway();
        return $gateway->processWebhook($request);
    }
    
    /**
     * Check the status of a payment.
     */
    public function checkPaymentStatus(Payment $payment): string
    {
        if (!isset($this->gateways[$payment->gateway])) {
            throw new Exception('Gateway de pagamento não implementado: ' . $payment->gateway);
        }
        
        $gateway = $this->gateways[$payment->gateway];
        $status = $gateway->getPaymentStatus($payment->transaction_id);
        
        // Atualizar o status do pagamento no banco de dados
        if ($status !== $payment->status) {
            $payment->status = $status;
            $payment->save();
        }
        
        return $status;
    }
    
    /**
     * Load all available payment gateways.
     */
    protected function loadGateways(): void
    {
        try {
            // Obter o gateway ativo do banco de dados
            $activeGateway = PaymentGateway::active()->first();
            
            if ($activeGateway) {
                // Registrar apenas o gateway ativo
                switch ($activeGateway->code) {
                    case 'mercadopago':
                        $this->gateways['mercadopago'] = new MercadoPagoGateway($activeGateway);
                        break;
                    case 'pagseguro':
                        // $this->gateways['pagseguro'] = new PagSeguroGateway($activeGateway);
                        break;
                    case 'rede':
                        // $this->gateways['rede'] = new RedeItauGateway($activeGateway);
                        break;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao carregar gateways de pagamento: ' . $e->getMessage());
        }
    }
}
