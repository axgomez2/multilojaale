<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

class MelhorEnvioService
{
    protected $baseUrl;
    protected $token;
    protected $from;
    protected $options;
    protected $services;

    public function __construct()
    {
        $this->baseUrl = config('melhorenvio.base_url');
        $this->token = config('melhorenvio.token');
        $this->from = config('melhorenvio.from');
        $this->options = config('melhorenvio.options');
        $this->services = config('melhorenvio.services');
    }

    /**
     * Calcula opções de frete usando a API do Melhor Envio
     *
     * @param Address $address Endereço de entrega
     * @param Cart $cart Carrinho do usuário
     * @return array Opções de frete disponíveis
     */
    public function calculateShipping(Address $address, Cart $cart)
    {
        try {
            // Preparar os produtos do carrinho
            $products = [];
            $cartItems = CartItem::where('cart_id', $cart->id)
                ->with(['product', 'product.productable', 'product.productable.weight', 'product.productable.dimension'])
                ->get();
            
            Log::info('Calculando frete para ' . count($cartItems) . ' itens do carrinho');
            
            foreach ($cartItems as $item) {
                $product = $item->product;
                
                // Para produtos do tipo VinylSec, buscar peso e dimensões
                if ($product->productable_type === 'App\\Models\\VinylSec') {
                    $vinylSec = $product->productable;
                    $weight = $vinylSec->weight;
                    $dimension = $vinylSec->dimension;
                    
                    // Verificar se tem peso e dimensões
                    if (!$weight || !$dimension) {
                        Log::warning('Produto ID ' . $product->id . ' não tem peso ou dimensão definidos. Usando valores padrão.');
                        // Usar valores padrão para discos de vinil
                        $productWeight = 0.3; // 300g
                        $productWidth = 31.5; // cm
                        $productHeight = 31.5; // cm
                        $productLength = 0.5; // cm
                    } else {
                        // Converter para as unidades esperadas pelo Melhor Envio (cm e kg)
                        $productWeight = $weight->value / 1000; // Convertendo de g para kg
                        $productWidth = $dimension->width; // cm
                        $productHeight = $dimension->height; // cm
                        $productLength = $dimension->depth; // cm
                        
                        Log::info('Produto ID ' . $product->id . ' - Peso: ' . $productWeight . 'kg, Dimensões: ' . 
                            $productWidth . 'x' . $productHeight . 'x' . $productLength . 'cm');
                    }
                    
                    $products[] = [
                        'id' => $product->id,
                        'width' => $productWidth,
                        'height' => $productHeight,
                        'length' => $productLength,
                        'weight' => $productWeight,
                        'insurance_value' => $vinylSec->price,
                        'quantity' => $item->quantity
                    ];
                } else {
                    // Para outros tipos de produtos, usar valores padrão
                    Log::warning('Produto ID ' . $product->id . ' não é do tipo VinylSec. Usando valores padrão.');
                    
                    $products[] = [
                        'id' => $product->id,
                        'width' => 15,
                        'height' => 15,
                        'length' => 15,
                        'weight' => 0.5,
                        'insurance_value' => $product->price,
                        'quantity' => $item->quantity
                    ];
                }
            }
            
            // Se não houver produtos válidos, retorna opções padrão
            if (empty($products)) {
                return $this->getFallbackOptions();
            }

            // Preparar os dados para a API
            $payload = [
                'from' => [
                    'postal_code' => $this->from['postal_code'],
                    'address' => $this->from['address'],
                    'number' => $this->from['number'],
                    'complement' => $this->from['complement'],
                    'district' => $this->from['district'],
                    'city' => $this->from['city'],
                    'state_abbr' => $this->from['state_abbr'],
                    'country' => $this->from['country']
                ],
                'to' => [
                    'postal_code' => preg_replace('/[^0-9]/', '', $address->zipcode),
                    'address' => $address->street,
                    'number' => $address->number,
                    'complement' => $address->complement,
                    'district' => $address->district,
                    'city' => $address->city,
                    'state_abbr' => $address->state,
                    'country' => $address->country ?? 'BR'
                ],
                'products' => $products,
                'options' => $this->options,
                'services' => $this->services
            ];

            // Fazer a requisição para a API do Melhor Envio
            Log::info('Enviando requisição para o Melhor Envio com payload: ' . json_encode($payload));
            
            $response = Http::withToken($this->token)
                ->post($this->baseUrl . '/api/v2/me/shipment/calculate', $payload);

            if ($response->successful()) {
                $shippingOptions = [];
                $data = $response->json();
                
                Log::info('Resposta do Melhor Envio: ' . json_encode($data));
                
                foreach ($data as $option) {
                    // Verificar se é um resultado válido
                    if (isset($option['id']) && isset($option['price']) && isset($option['name'])) {
                        $shippingOptions[] = [
                            'id' => $option['id'],
                            'name' => $option['name'],
                            'price' => $option['price'],
                            'days' => $option['delivery_time'] ?? 7,
                            'company' => $option['company']['name'] ?? 'Transportadora',
                            'custom_delivery_range' => [  // Adicionando informação de prazo personalizada
                                'min' => $option['delivery_range'] ? $option['delivery_range']['min'] : ($option['delivery_time'] - 1),
                                'max' => $option['delivery_range'] ? $option['delivery_range']['max'] : $option['delivery_time']
                            ],
                            'custom_price' => number_format($option['price'], 2, ',', '.'),
                            'error' => false
                        ];
                    }
                }
                
                // Se não encontrou nenhuma opção, usar o fallback
                if (empty($shippingOptions)) {
                    return $this->getFallbackOptions();
                }
                
                return $shippingOptions;
            } else {
                Log::error('Erro ao calcular frete: ' . $response->body());
                return $this->getFallbackOptions();
            }
        } catch (\Exception $e) {
            Log::error('Exceção ao calcular frete: ' . $e->getMessage());
            return $this->getFallbackOptions();
        }
    }

    /**
     * Retorna opções de frete padrão em caso de falha
     */
    protected function getFallbackOptions()
    {
        return [
            [
                'id' => 1, 
                'name' => 'PAC', 
                'price' => 15.90, 
                'days' => 7, 
                'company' => 'Correios', 
                'custom_delivery_range' => ['min' => 5, 'max' => 7],
                'custom_price' => '15,90',
                'error' => true
            ],
            [
                'id' => 2, 
                'name' => 'SEDEX', 
                'price' => 25.50, 
                'days' => 3, 
                'company' => 'Correios', 
                'custom_delivery_range' => ['min' => 2, 'max' => 3],
                'custom_price' => '25,50',
                'error' => true
            ],
            [
                'id' => 3, 
                'name' => 'Jadlog Package', 
                'price' => 18.75, 
                'days' => 5, 
                'company' => 'Jadlog', 
                'custom_delivery_range' => ['min' => 4, 'max' => 5],
                'custom_price' => '18,75',
                'error' => true
            ],
        ];
    }
}
