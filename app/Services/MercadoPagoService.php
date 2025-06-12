<?php

namespace App\Services;

use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

class MercadoPagoService
{
    protected $preferenceClient;
    protected $paymentClient;
    protected $isSandbox;
    
    public function __construct()
    {
        // Configurar o SDK com a chave de acesso do ambiente
        MercadoPagoConfig::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));
        
        $this->preferenceClient = new PreferenceClient();
        $this->paymentClient = new PaymentClient();
        $this->isSandbox = env('MERCADOPAGO_SANDBOX', true);
    }
    
    /**
     * Obter informações de um pagamento pelo ID
     * 
     * @param string $paymentId ID do pagamento no Mercado Pago
     * @return array Informações do pagamento
     * @throws \Exception Se ocorrer algum erro ao buscar o pagamento
     */
    public function getPaymentInfo(string $paymentId): array
    {
        try {
            Log::channel('mercadopago')->info('Buscando informações do pagamento', ['payment_id' => $paymentId]);
            
            // Obter o pagamento da API do Mercado Pago
            $response = $this->paymentClient->get($paymentId);
            
            // A resposta já vem como array associativo
            $payment = $response->getData();
            
            Log::channel('mercadopago')->info('Informações do pagamento recebidas', ['payment' => $payment]);
            
            return $payment;
        } catch (MPApiException $e) {
            Log::channel('mercadopago')->error('Erro na API do Mercado Pago ao obter pagamento', [
                'payment_id' => $paymentId,
                'status' => $e->getApiResponse()->getStatusCode(),
                'message' => $e->getApiResponse()->getContent()
            ]);
            
            throw new \Exception('Erro ao buscar informações do pagamento: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::channel('mercadopago')->error('Erro ao buscar pagamento', [
                'payment_id' => $paymentId,
                'message' => $e->getMessage()
            ]);
            
            throw new \Exception('Erro ao buscar informações do pagamento: ' . $e->getMessage());
        }
    }
    
    /**
     * Cria uma preferência para pagamento com Mercado Pago
     * 
     * @param Order $order Ordem de compra
     * @return array Dados de preferência para inicialização do Checkout do Mercado Pago
     */
    public function createPreference(Order $order)
    {
        try {
            // Verificar se há itens antes de prosseguir
            if ($order->items->isEmpty()) {
                throw new Exception('Pedido sem itens. Impossível criar preferência de pagamento.');
            }
            
            // Garantir que temos um valor válido para o total do pedido
            $orderTotal = floatval($order->total);
            if ($orderTotal <= 0) {
                $orderTotal = 0.01; // Valor mínimo aceito pelo Mercado Pago
                Log::warning('Pedido com valor zero ou negativo. Usando valor mínimo.', ['order_id' => $order->id, 'order_total' => $order->total]);
            }
            
            // Registrar os dados do pedido para debug
            Log::info('Dados do pedido antes de criar preferência', [
                'order_id' => $order->id,
                'order_items_count' => $order->items->count(),
                'total' => $order->total,
                'item_prices' => $order->items->map(function($item) {
                    return ['id' => $item->id, 'unit_price' => $item->unit_price];
                })
            ]);
            
            // Usar abordagem com array simples que funciona melhor
            $preference_data = [
                'items' => [
                    [
                        'id' => (string)$order->id,
                        'title' => "Pedido #{$order->order_number}",
                        'quantity' => 1,
                        'currency_id' => 'BRL',
                        'unit_price' => (float)$orderTotal,
                        'description' => "Compra em ".env('APP_NAME', 'Loja'),
                        'category_id' => 'services'
                    ]
                ],
                'payer' => [
                    'email' => $order->user->email ?? 'cliente@example.com',
                    'name' => $order->user->name ?? 'Cliente',
                    'surname' => explode(' ', $order->user->name ?? 'Cliente')[0] ?? ''
                ],
                'back_urls' => [
                    'success' => route('site.checkout.success', ['order_number' => $order->order_number]),
                    'failure' => route('site.shipping.index'),
                    'pending' => route('site.shipping.index')
                ],
                'auto_return' => 'approved',
                'external_reference' => $order->order_number,
                'statement_descriptor' => env('APP_NAME', 'Loja'),
                'payment_methods' => [
                    'excluded_payment_types' => [],
                    'installments' => 12
                ],
                'notification_url' => route('webhook.mercadopago')
            ];
            
            // Log dos dados que serão enviados ao Mercado Pago
            Log::debug('Dados de preferência enviados ao Mercado Pago', $preference_data);
            
            // Fazer a chamada real para a API do Mercado Pago para criar a preferência
            $response = $this->preferenceClient->create(['body' => $preference_data]);
            
            // Registrar o sucesso da criação da preferência
            Log::info('Preferência Mercado Pago criada com sucesso', [
                'preference_id' => $response->id ?? 'no-id',
                'init_point' => $response->init_point ?? 'no-init-point',
                'sandbox_init_point' => $response->sandbox_init_point ?? 'no-sandbox-init-point'
            ]);
            
            // Retorna o objeto de resposta da API
            return $response;
        } catch (MPApiException $e) {
            // Captura erros específicos da API do Mercado Pago
            $errorDetails = [
                'message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'preference_data' => $preference_data ?? []
            ];
            
            if ($e->getApiResponse()) {
                $response = $e->getApiResponse();
                $errorDetails['api_response'] = [
                    'status' => $response->getStatusCode(),
                    'content' => $response->getContent()
                ];
            }
            
            Log::error('Erro da API Mercado Pago', $errorDetails);
            throw new Exception('Erro na API do Mercado Pago: ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error('Erro ao criar preferência Mercado Pago', [
                'message' => $e->getMessage(), 
                'trace' => $e->getTraceAsString(),
                'order_id' => $order->id
            ]);
            
            throw new Exception('Erro ao processar o pagamento: ' . $e->getMessage());
        }
    }
    
    /**
     * Processa um pagamento pelo Mercado Pago
     * 
     * @param array $paymentData Dados do pagamento
     * @param Order $order Ordem de compra
     * @return array Resposta do processamento do pagamento
     */
    public function processPayment(array $paymentData, Order $order)
    {
        try {
            // Adicionar informação da ordem
            $paymentData['external_reference'] = $order->order_number;
            
            // Processar o pagamento
            $paymentResponse = $this->paymentClient->create(['body' => $paymentData]);
            
            // Atualizar a ordem com os dados do pagamento
            $order->payment_id = $paymentResponse->id;
            $order->payment_method = $paymentResponse->payment_method_id;
            $order->payment_type = $paymentResponse->payment_type_id;
            $order->payment_status = $paymentResponse->status;
            
            // Se o pagamento foi aprovado, atualizar o status da ordem
            if ($paymentResponse->status === 'approved') {
                $order->status = 'paid';
            } elseif (in_array($paymentResponse->status, ['in_process', 'pending'])) {
                $order->status = 'pending';
            } else {
                $order->status = 'payment_failed';
            }
            
            $order->save();
            
            return [
                'status' => $paymentResponse->status,
                'message' => $this->getPaymentStatusMessage($paymentResponse->status),
                'payment_id' => $paymentResponse->id,
            ];
        } catch (MPApiException $e) {
            Log::error('Erro MercadoPago API: ' . $e->getMessage());
            
            $errorMessage = 'Erro no processamento do pagamento';
            
            // Obter detalhes do erro, se disponíveis
            if ($e->getApiResponse()) {
                $response = $e->getApiResponse()->getContent();
                if (isset($response['message'])) {
                    $errorMessage .= ': ' . $response['message'];
                }
            }
            
            $order->status = 'payment_failed';
            $order->save();
            
            return [
                'status' => 'error',
                'message' => $errorMessage,
            ];
        } catch (Exception $e) {
            Log::error('Erro ao processar pagamento MercadoPago: ' . $e->getMessage());
            
            $order->status = 'payment_failed';
            $order->save();
            
            return [
                'status' => 'error',
                'message' => 'Erro no processamento do pagamento: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Retorna uma mensagem amigável para o status do pagamento
     * 
     * @param string $status Status do pagamento
     * @return string Mensagem amigável
     */
    protected function getPaymentStatusMessage($status)
    {
        $messages = [
            'approved' => 'Pagamento aprovado!',
            'pending' => 'Pagamento pendente de confirmação.',
            'in_process' => 'Pagamento em processamento.',
            'rejected' => 'Pagamento rejeitado. Por favor, tente novamente.',
            'refunded' => 'Pagamento devolvido.',
            'cancelled' => 'Pagamento cancelado.',
            'in_mediation' => 'Pagamento em disputa.',
            'charged_back' => 'Pagamento estornado.',
        ];
        
        return $messages[$status] ?? 'Status de pagamento: ' . $status;
    }
    
    /**
     * Valida se o webhook recebido é autêntico e válido
     * 
     * @param \Illuminate\Http\Request $request A requisição recebida
     * @return bool Se o webhook é válido
     */
    public function validateWebhook($request)
    {
        // Por enquanto, validamos apenas se há dados válidos na requisição
        // Em produção, seria ideal verificar assinaturas ou outros métodos de autenticação
        
        // Verificar se é um tipo válido de notificação
        $type = $request->input('type', '');
        $validTypes = ['payment', 'test', 'merchant_order'];
        
        if (empty($type) || !in_array($type, $validTypes)) {
            Log::warning('Webhook com tipo inválido', ['type' => $type]);
            return false;
        }
        
        // Se tiver dados de ID, vamos considerá-lo válido
        return !empty($request->input('data.id')) || !empty($request->input('id'));
    }

    /**
     * Processa o pagamento recebido pelo webhook
     * 
     * @param array $data Dados do webhook
     * @return array Resultado do processamento
     */
    public function processWebhookPayment($data)
    {
        try {
            // Obter o ID do pagamento
            $paymentId = $data['id'] ?? null;
            
            if (empty($paymentId)) {
                return [
                    'success' => false,
                    'message' => 'ID de pagamento não fornecido'
                ];
            }
            
            // Buscar detalhes do pagamento
            $paymentInfo = $this->getPaymentInfo($paymentId);
            
            // Validar se temos a referência externa
            if (empty($paymentInfo['external_reference'])) {
                return [
                    'success' => false,
                    'message' => 'Referência externa não encontrada no pagamento'
                ];
            }
            
            // Mapear o status do pagamento
            $mappedStatus = $this->mapPaymentStatus($paymentInfo['status']);
            
            return [
                'success' => true,
                'payment_id' => $paymentId,
                'external_reference' => $paymentInfo['external_reference'],
                'status' => $mappedStatus,
                'gateway_data' => $paymentInfo
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook de pagamento: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao processar pagamento: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Mapeia o status do Mercado Pago para o status da aplicação
     * 
     * @param string $mpStatus Status do Mercado Pago
     * @return string Status mapeado
     */
    protected function mapPaymentStatus($mpStatus)
    {
        $statusMap = [
            'approved' => 'approved',
            'pending' => 'pending',
            'in_process' => 'processing',
            'rejected' => 'declined',
            'refunded' => 'refunded',
            'cancelled' => 'canceled',
            'canceled' => 'canceled',
        ];
        
        return $statusMap[$mpStatus] ?? 'pending';
    }
    
    /**
     * Processa o webhook do Mercado Pago
     * 
     * @param array $data Dados do webhook
     * @return bool
     */
    public function processWebhook(array $data)
    {
        try {
            // Verificar se é uma notificação de pagamento
            if (!isset($data['action']) || $data['action'] !== 'payment.updated' || !isset($data['data']['id'])) {
                return false;
            }
            
            // Obter o ID do pagamento
            $paymentId = $data['data']['id'];
            
            // Buscar detalhes do pagamento
            $paymentInfo = $this->paymentClient->get($paymentId);
            
            // Buscar a ordem relacionada ao pagamento
            $order = Order::where('order_number', $paymentInfo->external_reference)->first();
            
            if (!$order) {
                Log::error('Ordem não encontrada para o pagamento: ' . $paymentId);
                return false;
            }
            
            // Atualizar o status do pagamento na ordem
            $order->payment_status = $paymentInfo->status;
            
            // Atualizar o status da ordem de acordo com o status do pagamento
            if ($paymentInfo->status === 'approved') {
                $order->status = 'paid';
            } elseif (in_array($paymentInfo->status, ['in_process', 'pending'])) {
                $order->status = 'pending';
            } elseif (in_array($paymentInfo->status, ['rejected', 'cancelled'])) {
                $order->status = 'payment_failed';
            }
            
            $order->save();
            
            Log::info('Webhook de pagamento processado com sucesso: ' . $paymentId);
            return true;
        } catch (Exception $e) {
            Log::error('Erro ao processar webhook MercadoPago: ' . $e->getMessage());
            return false;
        }
    }
}
