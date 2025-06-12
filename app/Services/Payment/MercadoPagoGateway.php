<?php

namespace App\Services\Payment;

use App\Services\Payment\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MercadoPagoGateway implements PaymentGatewayInterface
{
    protected PaymentGateway $config;
    
    /**
     * Constructor.
     */
    public function __construct(PaymentGateway $config)
    {
        $this->config = $config;
    }
    
    /**
     * Get the name of the payment gateway.
     */
    public function getName(): string
    {
        return 'Mercado Pago';
    }
    
    /**
     * Get the code of the payment gateway.
     */
    public function getCode(): string
    {
        return 'mercadopago';
    }
    
    /**
     * Process payment data and return result.
     * 
     * @param array $data Payment data including order and customer details
     * @return array Result with success status and transaction details
     */
    public function processPayment(array $data): array
    {
        // Verificar se o método é suportado
        if (!in_array($data['payment_method'] ?? 'credit_card', $this->getAvailableMethods())) {
            return [
                'success' => false,
                'message' => 'Método de pagamento não suportado: ' . ($data['payment_method'] ?? 'desconhecido'),
            ];
        }
        
        try {
            // Obter dados do pedido
            $order = $data['order'] ?? null;
            
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'Pedido não fornecido para processamento de pagamento',
                ];
            }

            // Configurar SDK do Mercado Pago
            \MercadoPago\SDK::setAccessToken($this->getAccessToken());
            
            // Processar pagamento de acordo com o método
            switch ($data['payment_method']) {
                case 'credit_card':
                    return $this->processCreditCardPayment($order, $data);
                case 'pix':
                    return $this->processPixPayment($order, $data);
                case 'boleto':
                    return $this->processBoletoPayment($order, $data);
                default:
                    return [
                        'success' => false,
                        'message' => 'Método de pagamento não implementado: ' . $data['payment_method'],
                    ];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento no Mercado Pago: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erro ao processar pagamento: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Get payment status from Mercado Pago by reference ID.
     * 
     * @param string $referenceId External reference ID
     * @return string Status of the payment
     */
    public function getPaymentStatusByReference(string $referenceId): string
    {
        try {
            // Configurar SDK do Mercado Pago
            \MercadoPago\SDK::setAccessToken($this->getAccessToken());
            
            // Buscar informações do pagamento
            $filters = [
                'external_reference' => $referenceId
            ];
            
            $searchResult = \MercadoPago\Payment::search(['external_reference' => $referenceId]);
            
            if ($searchResult && count($searchResult) > 0) {
                $payment = $searchResult[0];
                return $payment->status;
            }
            
            return 'unknown';
            
        } catch (\Exception $e) {
            Log::error('Erro ao buscar status do pagamento no Mercado Pago: ' . $e->getMessage());
            return 'error';
        }
    }
    
    /**
     * Get the required fields for this gateway.
     */
    public function getRequiredFields(): array
    {
        return [
            'card_number' => 'Número do Cartão',
            'card_holder_name' => 'Nome no Cartão',
            'expiration_month' => 'Mês de Expiração',
            'expiration_year' => 'Ano de Expiração',
            'security_code' => 'Código de Segurança',
            'installments' => 'Número de Parcelas'
        ];
    }
    
    /**
     * Get the available payment methods for this gateway.
     */
    public function getAvailableMethods(): array
    {
        return [
            'credit_card',
            'pix',
            'boleto'
        ];
    }
    
    /**
     * Create a payment for the given order.
     */
    public function createPayment(Order $order, string $method, array $paymentData = []): array
    {
        // Verificar se o método é suportado
        if (!in_array($method, $this->getAvailableMethods())) {
            return [
                'success' => false,
                'error' => 'Método de pagamento não suportado: ' . $method,
            ];
        }
        
        try {
            // Configurar SDK do Mercado Pago
            // Nota: Você precisará instalar o SDK do Mercado Pago: composer require mercadopago/dx-php
            \MercadoPago\SDK::setAccessToken($this->getAccessToken());
            
            // Criar o objeto de preferência
            $preference = new \MercadoPago\Preference();
            
            // Criar os itens
            $items = [];
            foreach ($order->items as $item) {
                $mpItem = new \MercadoPago\Item();
                $mpItem->id = $item->id;
                $mpItem->title = $item->vinyl->title ?? 'Produto';
                $mpItem->quantity = $item->quantity;
                $mpItem->unit_price = floatval($item->unit_price);
                $mpItem->currency_id = 'BRL';
                $items[] = $mpItem;
            }
            
            // Adicionar item para o frete
            if ($order->shipping > 0) {
                $shippingItem = new \MercadoPago\Item();
                $shippingItem->id = 'shipping';
                $shippingItem->title = 'Frete';
                $shippingItem->quantity = 1;
                $shippingItem->unit_price = floatval($order->shipping);
                $shippingItem->currency_id = 'BRL';
                $items[] = $shippingItem;
            }
            
            $preference->items = $items;
            
            // Configurar URLs de callback
            $preference->back_urls = [
                'success' => route('site.checkout.payment.callback', ['status' => 'success']),
                'failure' => route('site.checkout.payment.callback', ['status' => 'failure']),
                'pending' => route('site.checkout.payment.callback', ['status' => 'pending']),
            ];
            
            $preference->external_reference = $order->id;
            $preference->auto_return = 'approved';
            
            // Configurações específicas para o método de pagamento
            switch ($method) {
                case 'credit_card':
                    $preference->payment_methods = [
                        'excluded_payment_types' => [
                            ['id' => 'ticket'],
                            ['id' => 'bank_transfer']
                        ],
                        'installments' => 12
                    ];
                    break;
                
                case 'pix':
                    $preference->payment_methods = [
                        'excluded_payment_types' => [
                            ['id' => 'credit_card'],
                            ['id' => 'ticket']
                        ]
                    ];
                    break;
                
                case 'boleto':
                    $preference->payment_methods = [
                        'excluded_payment_types' => [
                            ['id' => 'credit_card'],
                            ['id' => 'bank_transfer']
                        ]
                    ];
                    break;
            }
            
            // Salvar a preferência
            $preference->save();
            
            // Retornar os dados necessários
            return [
                'success' => true,
                'transaction_id' => $preference->id,
                'status' => 'pending',
                'init_point' => $preference->init_point,
                'sandbox_init_point' => $preference->sandbox_init_point,
                'payment_url' => $this->isSandboxMode() ? $preference->sandbox_init_point : $preference->init_point,
            ];
            
        } catch (\Exception $e) {
            Log::error('Erro ao criar pagamento no Mercado Pago: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'Erro ao processar pagamento: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Process a callback from the payment gateway.
     */
    public function processCallback(Request $request): array
    {
        $status = $request->get('status', 'pending');
        $preferenceId = $request->get('preference_id');
        $paymentId = $request->get('payment_id');
        
        Log::info('Callback do Mercado Pago', [
            'status' => $status,
            'preference_id' => $preferenceId,
            'payment_id' => $paymentId,
        ]);
        
        return [
            'success' => true,
            'status' => $this->mapStatus($status),
            'transaction_id' => $paymentId,
            'preference_id' => $preferenceId,
            'external_reference' => $request->get('external_reference'),
        ];
    }
    
    /**
     * Process a webhook notification from the payment gateway.
     */
    public function processWebhook(Request $request): array
    {
        $payload = $request->all();
        Log::info('Webhook do Mercado Pago', $payload);
        
        // Verificar o tipo de notificação
        if (isset($payload['type']) && $payload['type'] == 'payment') {
            $paymentId = $payload['data']['id'];
            
            // Buscar informações do pagamento
            \MercadoPago\SDK::setAccessToken($this->getAccessToken());
            $payment = \MercadoPago\Payment::find_by_id($paymentId);
            
            if ($payment) {
                return [
                    'success' => true,
                    'status' => $this->mapStatus($payment->status),
                    'transaction_id' => $payment->id,
                    'external_reference' => $payment->external_reference,
                ];
            }
        }
        
        return [
            'success' => false,
            'error' => 'Tipo de notificação não suportado ou pagamento não encontrado',
        ];
    }
    
    /**
     * Get the status of a payment.
     */
    public function getPaymentStatus(string $transactionId): string
    {
        try {
            \MercadoPago\SDK::setAccessToken($this->getAccessToken());
            $payment = \MercadoPago\Payment::find_by_id($transactionId);
            
            if ($payment) {
                return $this->mapStatus($payment->status);
            }
            
            return 'pending';
        } catch (\Exception $e) {
            Log::error('Erro ao consultar status de pagamento no Mercado Pago: ' . $e->getMessage());
            return 'pending';
        }
    }
    
    /**
     * Check if the gateway is in sandbox mode.
     */
    public function isSandboxMode(): bool
    {
        return $this->config ? $this->config->sandbox_mode : true;
    }
    
    /**
     * Get the access token for the API.
     */
    protected function getAccessToken(): string
    {
        if (!$this->config) {
            return '';
        }
        
        return $this->config->getApiToken() ?? '';
    }
    
    /**
     * Process credit card payment.
     */
    protected function processCreditCardPayment(Order $order, array $data): array
    {
        try {
            // Configurar SDK do Mercado Pago
            \MercadoPago\SDK::setAccessToken($this->getAccessToken());
            
            // Criar o token do cartão
            $token = new \MercadoPago\CardToken();
            $token->card_number = $data['card_number'] ?? '';
            $token->expiration_month = $data['expiration_month'] ?? '';
            $token->expiration_year = $data['expiration_year'] ?? '';
            $token->security_code = $data['security_code'] ?? '';
            $token->cardholder = [
                'name' => $data['card_holder_name'] ?? '',
                'identification' => [
                    'type' => 'CPF',
                    'number' => $data['cpf'] ?? ''
                ]
            ];
            
            $token->save();
            
            if (isset($token->error)) {
                throw new \Exception($token->error->message);
            }
            
            // Criar o pagamento
            $payment = new \MercadoPago\Payment();
            $payment->transaction_amount = (float) $order->total;
            $payment->token = $token->id;
            $payment->description = "Pedido #{$order->id}";
            $payment->installments = (int) ($data['installments'] ?? 1);
            $payment->payment_method_id = $data['payment_method_id'] ?? 'visa';
            $payment->payer = [
                'email' => $order->customer_email,
                'first_name' => $order->customer_first_name,
                'last_name' => $order->customer_last_name,
                'identification' => [
                    'type' => 'CPF',
                    'number' => $data['cpf'] ?? ''
                ],
                'address' => [
                    'zip_code' => $order->shipping_zipcode,
                    'street_name' => $order->shipping_street,
                    'street_number' => $order->shipping_number,
                    'neighborhood' => $order->shipping_neighborhood,
                    'city' => $order->shipping_city,
                    'federal_unit' => $order->shipping_state
                ]
            ];
            
            // Salvar o pagamento
            $payment->save();
            
            // Verificar se houve erro
            if ($payment->error) {
                throw new \Exception($payment->error->message);
            }
            
            return [
                'success' => $payment->status === 'approved',
                'status' => $this->mapStatus($payment->status),
                'transaction_id' => $payment->id,
                'external_reference' => $order->id,
                'payment_method' => 'credit_card',
                'payment_details' => [
                    'card' => [
                        'last_four_digits' => substr($data['card_number'], -4),
                        'installments' => $payment->installments
                    ]
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento com cartão: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'Erro ao processar pagamento com cartão: ' . $e->getMessage(),
                'status' => 'error'
            ];
        }
    }
    
    /**
     * Process PIX payment.
     */
    protected function processPixPayment(Order $order, array $data): array
    {
        try {
            // Configurar SDK do Mercado Pago
            \MercadoPago\SDK::setAccessToken($this->getAccessToken());
            
            // Criar o pagamento
            $payment = new \MercadoPago\Payment();
            $payment->transaction_amount = (float) $order->total;
            $payment->description = "Pedido #{$order->id}";
            $payment->payment_method_id = 'pix';
            $payment->payer = [
                'email' => $order->customer_email,
                'first_name' => $order->customer_first_name,
                'last_name' => $order->customer_last_name,
                'identification' => [
                    'type' => 'CPF',
                    'number' => $data['cpf'] ?? ''
                ]
            ];
            
            // Salvar o pagamento
            $payment->save();
            
            // Verificar se houve erro
            if ($payment->error) {
                throw new \Exception($payment->error->message);
            }
            
            return [
                'success' => true,
                'status' => 'pending',
                'transaction_id' => $payment->id,
                'external_reference' => $order->id,
                'payment_method' => 'pix',
                'pix_qr_code' => $payment->point_of_interaction->transaction_data->qr_code ?? null,
                'pix_qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64 ?? null,
                'pix_ticket_url' => $payment->point_of_interaction->transaction_data->ticket_url ?? null
            ];
            
        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento PIX: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'Erro ao processar pagamento PIX: ' . $e->getMessage(),
                'status' => 'error'
            ];
        }
    }
    
    /**
     * Process boleto payment.
     */
    protected function processBoletoPayment(Order $order, array $data): array
    {
        try {
            // Validar CPF
            if (empty($data['cpf'])) {
                throw new \Exception('CPF é obrigatório para pagamento com boleto');
            }

            // Configurar SDK do Mercado Pago
            \MercadoPago\SDK::setAccessToken($this->getAccessToken());
            
            // Criar o pagamento
            $payment = new \MercadoPago\Payment();
            $payment->transaction_amount = (float) $order->total;
            $payment->description = "Pedido #{$order->id}";
            $payment->payment_method_id = 'bolbradesco';
            $payment->notification_url = route('api.payments.webhook');
            $payment->date_of_expiration = now()->addDays(3)->toIso8601String();
            
            // Dados do pagador
            $payment->payer = [
                'email' => $order->customer_email,
                'first_name' => $order->customer_first_name,
                'last_name' => $order->customer_last_name,
                'identification' => [
                    'type' => 'CPF',
                    'number' => preg_replace('/[^0-9]/', '', $data['cpf'])
                ],
                'address' => [
                    'zip_code' => preg_replace('/[^0-9]/', '', $order->shipping_zipcode),
                    'street_name' => $order->shipping_street,
                    'street_number' => $order->shipping_number,
                    'neighborhood' => $order->shipping_neighborhood,
                    'city' => $order->shipping_city,
                    'federal_unit' => $order->shipping_state
                ]
            ];
            
            // Salvar o pagamento
            $payment->save();
            
            // Verificar se houve erro
            if ($payment->error) {
                $errorMsg = $payment->error->message;
                if (isset($payment->error->causes) && is_array($payment->error->causes)) {
                    $errorMsg .= ': ' . collect($payment->error->causes)->pluck('description')->implode(', ');
                }
                throw new \Exception($errorMsg);
            }
            
            // Log para depuração
            Log::info('Boleto gerado com sucesso', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'boleto_url' => $payment->transaction_details->external_resource_url ?? null,
                'barcode' => $payment->barcode->content ?? null
            ]);
            
            return [
                'success' => true,
                'status' => 'pending',
                'transaction_id' => $payment->id,
                'external_reference' => $order->id,
                'payment_method' => 'boleto',
                'boleto_url' => $payment->transaction_details->external_resource_url ?? null,
                'barcode' => $payment->barcode->content ?? null,
                'expiration_date' => $payment->date_of_expiration ?? null
            ];
            
        } catch (\Exception $e) {
            Log::error('Erro ao gerar boleto: ' . $e->getMessage(), [
                'order_id' => $order->id ?? null,
                'exception' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => 'Não foi possível gerar o boleto. Verifique os dados e tente novamente.',
                'debug_error' => $e->getMessage(),
                'status' => 'error'
            ];
        }
    }
    
    /**
     * Map the Mercado Pago status to our application status.
     */
    protected function mapStatus(string $mpStatus): string
    {
        return match($mpStatus) {
            'approved' => 'approved',
            'pending' => 'pending',
            'in_process' => 'processing',
            'rejected' => 'declined',
            'refunded' => 'refunded',
            'cancelled', 'canceled' => 'canceled',
            default => 'pending',
        };
    }
}
