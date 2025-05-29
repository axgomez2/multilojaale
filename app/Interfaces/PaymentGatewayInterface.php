<?php

namespace App\Interfaces;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Get the name of the payment gateway.
     */
    public function getName(): string;
    
    /**
     * Get the available payment methods for this gateway.
     */
    public function getAvailableMethods(): array;
    
    /**
     * Create a payment for the given order.
     */
    public function createPayment(Order $order, string $method, array $paymentData = []): array;
    
    /**
     * Process a callback from the payment gateway.
     */
    public function processCallback(Request $request): array;
    
    /**
     * Process a webhook notification from the payment gateway.
     */
    public function processWebhook(Request $request): array;
    
    /**
     * Get the status of a payment.
     */
    public function getPaymentStatus(string $transactionId): string;
    
    /**
     * Check if the gateway is in sandbox mode.
     */
    public function isSandboxMode(): bool;
}
