<?php

namespace App\Services\Payment;

use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $gateway;

    public function __construct()
    {
        $this->loadActiveGateway();
    }

    protected function loadActiveGateway()
    {
        $activeGateway = PaymentGateway::where('active', true)->first();
        
        if (!$activeGateway) {
            return null;
        }
        
        switch ($activeGateway->code) {
            case 'mercadopago':
                $this->gateway = new MercadoPagoGateway($activeGateway);
                break;
            case 'pagseguro':
                $this->gateway = new PagSeguroGateway($activeGateway);
                break;
            case 'rede':
                $this->gateway = new RedeGateway($activeGateway);
                break;
            default:
                Log::error('Gateway de pagamento não suportado: ' . $activeGateway->code);
                return null;
        }
        
        return $this->gateway;
    }

    public function getActiveGateway()
    {
        return $this->gateway;
    }

    public function processPayment(array $data)
    {
        if (!$this->gateway) {
            throw new \Exception("Nenhum gateway de pagamento está ativo");
        }
        
        return $this->gateway->processPayment($data);
    }

    public function getPaymentStatus(string $referenceId)
    {
        if (!$this->gateway) {
            throw new \Exception("Nenhum gateway de pagamento está ativo");
        }
        
        return $this->gateway->getPaymentStatus($referenceId);
    }
}
