<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\VinylMaster;
use App\Models\Weight;
use App\Models\Dimension;
use App\Models\ShippingQuote;
use App\Http\Controllers\MelhorEnvioAuthController;

class MelhorEnvio
{
    protected $baseUrl;
    protected $token;
    protected $sandbox;
    protected $storeZipCode;
    
    /**
     * Construtor do serviço
     */
    public function __construct()
    {
        $this->baseUrl = env('MELHOR_ENVIO_URL', 'https://sandbox.melhorenvio.com.br/api/v2/');
        $this->token = $this->getAccessToken();
        $this->sandbox = env('MELHOR_ENVIO_SANDBOX', true);
        $this->storeZipCode = env('STORE_ZIP_CODE', '09220360');
        
        Log::info('Token configurado: ' . ($this->token ? 'Sim' : 'Não'));
    }
    
    /**
     * Obtém o token de acesso, renovando se necessário
     */
    protected function getAccessToken()
    {
        // Primeiro tenta pegar do cache
        $token = Cache::get('melhorenvio_access_token');
        
        // Se não encontrou no cache, usa o token do .env
        if (!$token) {
            $token = env('MELHOR_ENVIO_TOKEN');
        }
        
        return $token;
    }
    
    /**
     * Renova o token de acesso se estiver expirado
     */
    protected function refreshTokenIfNeeded()
    {
        try {
            // Testar se o token ainda é válido
            $response = Http::withToken($this->token)
                ->get($this->baseUrl . 'me');
                
            // Se o token estiver inválido, tenta renovar
            if ($response->status() === 401) {
                $authController = new MelhorEnvioAuthController();
                $result = $authController->refreshToken();
                
                if ($result && isset($result['success']) && $result['success']) {
                    $this->token = $result['token'];
                    return true;
                }
                
                Log::error('Não foi possível renovar o token do Melhor Envio');
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao verificar/renovar token: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Faz uma requisição HTTP para a API
     */
    protected function request($method, $endpoint, $data = [], $retries = 1)
    {
        try {
            $url = $this->baseUrl . trim($endpoint, '/');
            
            Log::info('URL da API: ' . $url);
            
            // Configurar headers corretamente
            $http = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]);
            
            // Adicionar token de autorização se disponível
            if ($this->token) {
                $http = $http->withToken($this->token);
            }
            
            // Usar método correto (GET, POST, etc)
            $response = $http->$method($url, $data);
            
            Log::info('Status da resposta: ' . $response->status());
            Log::info('Corpo da resposta: ' . $response->body());
            
            // Se o token expirou durante a requisição, tenta renovar e tentar novamente
            if ($response->status() === 401 && $retries > 0) {
                if ($this->refreshTokenIfNeeded()) {
                    return $this->request($method, $endpoint, $data, $retries - 1);
                }
            }
            
            return $response;
        } catch (\Exception $e) {
            Log::error('Erro na requisição para o Melhor Envio: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Busca uma cotação existente ou calcula uma nova
     *
     * @param string $destinationZip CEP de destino
     * @param array $cartItems Itens do carrinho
     * @return array
     */
    public function getOrCalculateShipping(string $destinationZip, array $cartItems)
    {
        // Limpeza dos CEPs
        $toZip = $this->cleanZipCode($destinationZip);
        $fromZip = $this->cleanZipCode($this->storeZipCode);
        
        // Criar um hash único para esta combinação de itens do carrinho e CEP
        $cartItemsHash = md5(json_encode($cartItems) . $toZip);
        
        // Buscar pelo usuário atual ou sessão
        $userId = null; // Não usaremos user_id até resolver o problema de UUID vs integer
        $sessionId = Session::getId(); // Sempre usaremos session_id para simplificar
        
        // Verificar se já existe uma cotação válida
        $query = ShippingQuote::where('cart_items_hash', $cartItemsHash)
            ->where('zip_from', $fromZip)
            ->where('zip_to', $toZip)
            ->notExpired();
            
        // Filtrar pela sessão ou usuário
        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        }
        
        $existingQuote = $query->first();
        
        // Se encontrou uma cotação válida, retorna-a
        if ($existingQuote) {
            Log::info('Cotação existente encontrada com ID: ' . $existingQuote->id);
            return [
                'success' => true,
                'quote_token' => $existingQuote->quote_token,
                'options' => $existingQuote->options
            ];
        }
        
        // Caso contrário, calcula uma nova cotação
        $result = $this->calculateShipping($destinationZip, $cartItems);
        
        // Se o cálculo foi bem-sucedido, salva a cotação
        if ($result['success'] && !empty($result['options'])) {
            try {
                $products = $this->prepareProductsForCalculation($cartItems);
                
                $quote = ShippingQuote::create([
                    // Removemos user_id temporariamente devido ao problema de UUID vs Integer
                    'session_id' => $sessionId,
                    'cart_items_hash' => $cartItemsHash,
                    'cart_items' => $cartItems,
                    'zip_from' => $fromZip,
                    'zip_to' => $toZip,
                    'products' => $products,
                    'api_response' => isset($result['api_response']) ? $result['api_response'] : null,
                    'options' => $result['options'],
                    'expires_at' => now()->addHours(24),
                ]);
                
                $result['quote_token'] = $quote->quote_token;
                Log::info('Nova cotação criada com ID: ' . $quote->id);
            } catch (\Exception $e) {
                Log::error('Erro ao salvar cotação: ' . $e->getMessage());
            }
        }
        
        return $result;
    }
    
    /**
     * Seleciona um serviço de entrega para uma cotação
     *
     * @param string $quoteToken Token da cotação
     * @param string $serviceId ID do serviço selecionado
     * @return array
     */
    public function selectShippingService(string $quoteToken, string $serviceId)
    {
        // Buscar a cotação pelo token
        $sessionId = Session::getId();
        
        $query = ShippingQuote::where('quote_token', $quoteToken)
            ->notExpired();
            
        // Filtrar apenas pela sessão por enquanto
        if ($sessionId) {
            $query->where('session_id', $sessionId);
        }
        
        $quote = $query->first();
        
        if (!$quote) {
            return [
                'success' => false,
                'message' => 'Cotação não encontrada ou expirada.'
            ];
        }
        
        // Buscar o serviço selecionado nas opções
        $selectedOption = null;
        foreach ($quote->options as $option) {
            if ($option['id'] == $serviceId) {
                $selectedOption = $option;
                break;
            }
        }
        
        if (!$selectedOption) {
            return [
                'success' => false,
                'message' => 'Serviço de entrega não encontrado na cotação.'
            ];
        }
        
        // Atualizar a cotação com o serviço selecionado
        $quote->selected_service_id = $serviceId;
        $quote->selected_price = $selectedOption['price'];
        $quote->selected_delivery_time = $selectedOption['delivery_time'];
        $quote->save();
        
        return [
            'success' => true,
            'selected_option' => $selectedOption,
            'formatted_price' => 'R$ ' . number_format($selectedOption['price'], 2, ',', '.'),
            'delivery_estimate' => $selectedOption['delivery_estimate'] ?? null
        ];
    }
    
    /**
     * Recupera uma cotação pelo token
     *
     * @param string $quoteToken Token da cotação
     * @return ShippingQuote|null
     */
    public function getQuoteByToken(string $quoteToken)
    {
        $sessionId = Session::getId();
        
        $query = ShippingQuote::where('quote_token', $quoteToken)
            ->notExpired();
            
        // Filtrar apenas pela sessão por enquanto
        if ($sessionId) {
            $query->where('session_id', $sessionId);
        }
        
        return $query->first();
    }
    
    /**
     * Calcula o frete para os produtos no carrinho
     *
     * @param string $destinationZip CEP de destino
     * @param array $cartItems Itens do carrinho
     * @return array
     */
    public function calculateShipping(string $destinationZip, array $cartItems)
    {
        try {
            Log::info('Iniciando cálculo de frete para CEP: ' . $destinationZip);
            Log::info('CEP da loja: ' . $this->storeZipCode);
            Log::info('Itens do carrinho: ' . json_encode($cartItems));
            
            // Preparar os produtos para o cálculo
            $products = $this->prepareProductsForCalculation($cartItems);
            
            // Se não houver produtos, retorna erro
            if (empty($products)) {
                return [
                    'success' => false,
                    'message' => 'Não há produtos no carrinho para calcular o frete.'
                ];
            }
            
            Log::info('Produtos preparados para cálculo: ' . json_encode($products));
            
            // Limpar os CEPs
            $fromZip = $this->cleanZipCode($this->storeZipCode);
            $toZip = $this->cleanZipCode($destinationZip);
            
            // Prepara um único pacote consolidado para o cálculo
            $totalWeight = 0;
            $totalPrice = 0;
            $totalItems = 0;
            
            // Variáveis para calcular dimensões máximas
            $maxWidth = 0;
            $maxLength = 0;
            $totalDiscs = 0;
            
            foreach ($products as $product) {
                // Verificar se o peso é um objeto ou um valor
                if (is_object($product['weight'])) {
                    $totalWeight += $product['weight']->value * $product['quantity'];
                } else {
                    $totalWeight += $product['weight'] * $product['quantity'];
                }
                $totalPrice += $product['unitary_value'] * $product['quantity'];
                $totalItems += $product['quantity'];
                
                // Atualizar dimensões máximas (largura e comprimento)
                $maxWidth = max($maxWidth, $product['width']);
                $maxLength = max($maxLength, $product['length']);
                
                // Contar discos (para cálculo de altura)
                $totalDiscs += $product['quantity'];
            }
            
            // A API exige dimensões mínimas
            $width = max(16, $maxWidth);   // Mínimo 16 cm, ou a maior largura de disco
            $length = max(16, $maxLength); // Mínimo 16 cm, ou o maior comprimento de disco
            
            // Cálculo de altura com base em regras específicas para faixas de quantidade
            // Regras definidas pelo cliente com base em experiência prática
            if ($totalDiscs <= 5) {
                // Até 5 discos: manter a altura mínima
                $height = 2;
            } elseif ($totalDiscs <= 10) {
                // De 6 a 10 discos: 7 cm de altura
                $height = 7;
            } elseif ($totalDiscs <= 20) {
                // De 11 a 20 discos: 9 cm de altura
                $height = 9;
            } elseif ($totalDiscs <= 30) {
                // De 20 a 30 discos: 15 cm de altura
                $height = 15;
            } else {
                // 30+ discos: 35 cm de altura
                $height = 35;
            }
            
            // Converter de gramas para o formato esperado pela API (a API multiplica por 100)
            // Então enviamos peso_em_gramas/100 para obter o valor correto
            $totalWeight = max(3, $totalWeight / 100); // Min 300g = 3 no formato da API
            
            // Montando o payload no formato mais simples possível
            $payload = [
                'from' => [
                    'postal_code' => $fromZip
                ],
                'to' => [
                    'postal_code' => $toZip
                ],
                'package' => [
                    'height' => $height,
                    'width' => $width,
                    'length' => $length,
                    'weight' => $totalWeight
                ],
                'options' => [
                    'receipt' => false,
                    'own_hand' => false,
                    'insurance_value' => $totalPrice
                ],
                'services' => '1,2,3,4,15,16'  // 1=PAC, 2=SEDEX, 3=Jadlog Package, 4=Jadlog Com.Package, 15=Azul E-commerce, 16=Azul Amanhã
            ];
            
            // Payload alternativo mais simplificado
            if ($this->sandbox) {
                // No ambiente de sandbox, usar estrutura ainda mais simples
                $payload = [
                    'from' => ['postal_code' => $fromZip],
                    'to' => ['postal_code' => $toZip],
                    'package' => [
                        'weight' => $totalWeight,
                        'width' => $width,
                        'height' => $height,
                        'length' => $length
                    ],
                    'services' => '1,2,3,4,15,16'
                ];
            }
            
            Log::info('Enviando payload para Melhor Envio: ' . json_encode($payload));
            
            // Faz a requisição para a API
            $response = $this->request('post', 'me/shipment/calculate', $payload);
            
            // Verificar se a requisição foi bem sucedida
            if ($response->successful()) {
                $responseData = $response->json();
                $options = $this->formatShippingOptions($responseData);
                
                return [
                    'success' => true,
                    'options' => $options,
                    'api_response' => $responseData
                ];
            } else {
                Log::error('Erro ao calcular frete: ' . $response->body());
                return [
                    'success' => false,
                    'message' => 'Não foi possível calcular o frete. Verifique se o CEP está correto e tente novamente.'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exceção ao calcular frete: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Ocorreu um erro ao calcular o frete: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Prepara os produtos do carrinho para o cálculo de frete
     *
     * @param array $cartItems
     * @return array
     */
    protected function prepareProductsForCalculation(array $cartItems)
    {
        $products = [];
        Log::info('Preparando produtos para cálculo de frete: ' . json_encode($cartItems));
        
        // Se não houver itens, usar um produto de teste para permitir o cálculo
        if (empty($cartItems)) {
            Log::info('Nenhum item no carrinho. Usando produto de teste.');
            return [
                [
                    'id' => 'test-product',
                    'width' => 31,     // Largura em cm para LP padrão
                    'height' => 2,      // Altura em cm (espessura)
                    'length' => 31,     // Comprimento em cm para LP padrão
                    'weight' => 300,    // Peso em GRAMAS
                    'insurance_value' => 100,  // Valor para seguro
                    'quantity' => 1     // Quantidade
                ]
            ];
        }
        
        foreach ($cartItems as $item) {
            $vinylId = $item['id'];
            $quantity = $item['quantity'];
            
            // Buscar informações do vinil no banco de dados
            $vinyl = VinylMaster::with('vinylSec')->find($vinylId);
            
            if ($vinyl && $vinyl->vinylSec) {
                $price = $vinyl->vinylSec->price ?: 100; // Usar valor padrão se o preço for zero
                
                // Buscar peso e dimensões reais do banco de dados
                $weight = null;
                $dimensions = null;
                
                if ($vinyl->vinylSec->weight_id) {
                    $weight = Weight::find($vinyl->vinylSec->weight_id);
                }
                
                if ($vinyl->vinylSec->dimension_id) {
                    $dimensions = Dimension::find($vinyl->vinylSec->dimension_id);
                }
                
                // Valores padrão para discos de vinil caso não encontre no banco
                $weightValue = 300; // 300 gramas
                $width = 31;       // 31 cm (LP padrão)
                $height = 2;       // 2 cm (espessura com embalagem)
                $length = 31;      // 31 cm (LP padrão)
                
                // Usar valores do banco se disponíveis
                if ($weight) {
                    // Se estiver em kg, converte para gramas
                    if (strtolower($weight->unit) === 'kg') {
                        $weightValue = $weight->value * 1000;
                    } else {
                        $weightValue = $weight->value;
                    }
                }
                
                if ($dimensions) {
                    $width = $dimensions->width;
                    $height = 2; // Fixar altura em 2cm para embalagem
                    $length = $width; // Disco é redondo, largura = comprimento
                }
                
                Log::info('Dados de produto para cálculo:', [
                    'produto' => $vinyl->title,
                    'peso' => $weightValue . 'g',
                    'dimensoes' => "{$width}cm x {$height}cm x {$length}cm"
                ]);
                
                // A API exige dimensões mínimas e está limitando peso/tamanho
                // Ajustar para valores aceitos pela API
                $width = max(16, min($width, 30)); // Entre 16 e 30 cm
                $height = max(2, min($height, 30)); // Entre 2 e 30 cm
                $length = max(16, min($length, 30)); // Entre 16 e 30 cm
                
                // Ajustar peso se necessário (mínimo 0.3kg = 300g)
                $weightValue = max(300, min($weightValue, 10000)); // Entre 300g e 10kg
                
                $products[] = [
                    'name' => $vinyl->title,  // Nome do produto
                    'quantity' => (int) $quantity, // Garantir que seja inteiro
                    'unitary_value' => (float) $price, // Preço unitário
                    'weight' => $weightValue, // Peso em GRAMAS
                    'width' => $width, // Largura em cm
                    'height' => $height, // Altura em cm (espessura)
                    'length' => $length, // Comprimento em cm
                    'insurance_value' => (float) $price // Valor para seguro
                ];
            } else {
                Log::warning('Vinil não encontrado ou sem dados de preço: ' . $vinylId);
            }
        }
        
        // Se nenhum produto válido foi encontrado, usar um produto de teste
        if (empty($products)) {
            Log::info('Nenhum produto válido encontrado. Usando produto de teste.');
            return [
                [
                    'name' => 'Disco de Vinil',
                    'quantity' => 1,
                    'unitary_value' => 100.00,
                    'weight' => 300, // em GRAMAS
                    'width' => 31,  // cm
                    'height' => 2,   // cm
                    'length' => 31,  // cm
                    'insurance_value' => 100.00
                ]
            ];
        }
        
        return $products;
    }
    
    /**
     * Retorna os serviços de entrega disponíveis
     *
     * @return array
     */
    public function getAvailableServices()
    {
        return [1, 2]; // 1 = PAC, 2 = SEDEX
    }
    
    /**
     * Calcula o valor total do seguro baseado nos produtos
     *
     * @param array $products
     * @return float
     */
    protected function calculateTotalInsuranceValue(array $products)
    {
        $total = 0;
        
        foreach ($products as $product) {
            $total += $product['unitary_value'] * $product['quantity'];
        }
        
        return $total;
    }
    
    /**
     * Formata as opções de frete retornadas pela API
     *
     * @param array $options
     * @return array
     */
    protected function formatShippingOptions(array $options)
    {
        $formatted = [];
        
        foreach ($options as $option) {
            // Verifica se temos pelo menos id e preço para considerar um serviço válido
            if (isset($option['id']) && (isset($option['price']) || isset($option['custom_price']))) {
                // Gerar o nome do serviço de forma segura
                $companyName = isset($option['company']['name']) ? $option['company']['name'] : (isset($option['company']) ? $option['company'] : '');
                $serviceName = $companyName . ' ' . (isset($option['name']) ? $option['name'] : (isset($option['title']) ? $option['title'] : 'Serviço'));
                
                // Formatar estimativa de entrega se disponível
                $deliveryTime = isset($option['delivery_time']) ? (int)$option['delivery_time'] : (isset($option['delivery_days']) ? (int)$option['delivery_days'] : 5);
                $deliveryEstimate = $this->formatDeliveryEstimate($deliveryTime);
                
                // Determinar o preço usando price ou custom_price
                $price = isset($option['price']) ? (float)$option['price'] : (isset($option['custom_price']) ? (float)$option['custom_price'] : 0);
                
                // Determinar IDs e informações da empresa com segurança
                $companyId = isset($option['company']['id']) ? $option['company']['id'] : (isset($option['company_id']) ? $option['company_id'] : 0);
                $companyPicture = isset($option['company']['picture']) ? $option['company']['picture'] : (isset($option['company_picture']) ? $option['company_picture'] : '');
                
                // Garantir que todos os campos necessários estejam presentes
                $formatted[] = [
                    'id' => $option['id'] ?? '',
                    'name' => $serviceName ?? 'Frete',
                    'price' => $price,
                    'delivery_time' => $deliveryTime,
                    'delivery_estimate' => $deliveryEstimate,
                    'formatted_price' => 'R$ ' . number_format($price, 2, ',', '.'),
                    'company_id' => $companyId,
                    'company_name' => $companyName,
                    'company_picture' => $companyPicture
                ];
            }
        }
        
        // Ordenar por preço
        usort($formatted, function($a, $b) {
            return $a['price'] <=> $b['price'];
        });
        
        return $formatted;
    }
    
    /**
     * Formata a estimativa de entrega
     *
     * @param int $days
     * @return string
     */
    protected function formatDeliveryEstimate($days)
    {
        if ($days == 1) {
            return '1 dia útil';
        }
        
        return $days . ' dias úteis';
    }
    
    /**
     * Limpa o CEP removendo caracteres não numéricos
     *
     * @param string $zipCode
     * @return string
     */
    protected function cleanZipCode($zipCode)
    {
        return preg_replace('/[^0-9]/', '', $zipCode);
    }
    
    /**
     * Verifica se o usuário está autenticado no Melhor Envio
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        return !empty($this->token);
    }
}
