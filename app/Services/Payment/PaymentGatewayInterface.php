<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    public function getName(): string;
    public function getCode(): string;
    public function getRequiredFields(): array;
    public function processPayment(array $data): array;
    public function getPaymentStatus(string $referenceId): string;
}
