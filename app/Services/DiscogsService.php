<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscogsService
{
    /**
     * Busca discos no Discogs
     *
     * @param string $query Query de busca
     * @return array Resultados da busca
     * @throws \Exception
     */
    public function search(string $query): array
    {
        try {
            $response = Http::get('https://api.discogs.com/database/search', [
                'q' => $query,
                'type' => 'release',
                'token' => config('services.discogs.token'),
            ]);

            if (!$response->successful()) {
                throw new \Exception('Falha ao buscar dados da API do Discogs: ' . $response->body());
            }

            return $response->json()['results'] ?? [];
        } catch (\Exception $e) {
            Log::error('Erro ao buscar no Discogs: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtém detalhes de um lançamento específico do Discogs
     *
     * @param string $releaseId ID do lançamento no Discogs
     * @return array|null Dados do lançamento ou null se não encontrado
     */
    public function getRelease(string $releaseId): ?array
    {
        try {
            // Log a consulta sendo realizada
            Log::info("Consultando release do Discogs", ['release_id' => $releaseId]);
            
            // Obter informações do release
            $response = Http::get("https://api.discogs.com/releases/{$releaseId}", [
                'token' => config('services.discogs.token'),
            ]);

            if (!$response->successful()) {
                Log::error("Falha ao obter release do Discogs", [
                    'release_id' => $releaseId,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $releaseData = $response->json();
            
            // Log com as informações básicas do release para debug
            Log::info("Dados básicos do release obtidos", [
                'release_id' => $releaseId,
                'title' => $releaseData['title'] ?? 'N/A',
                'year' => $releaseData['year'] ?? 'N/A',
                'num_images' => isset($releaseData['images']) ? count($releaseData['images']) : 0,
            ]);
            
            // Remover os vídeos da resposta (não são necessários para o cadastro)
            if (isset($releaseData['videos'])) {
                unset($releaseData['videos']);
            }

            // Buscar informações de preço (usar BRL para obter valores diretamente em reais)
            Log::info("Consultando estatísticas de mercado do Discogs", ['release_id' => $releaseId]);
            
            $marketResponse = Http::get("https://api.discogs.com/marketplace/stats/{$releaseId}", [
                'token' => config('services.discogs.token'),
                'curr_abbr' => 'BRL' // Solicitar valores em reais
            ]);
            
            // Verificar se obtivemos sucesso na consulta de estatísticas de mercado
            if (!$marketResponse->successful()) {
                Log::error("Falha ao obter estatísticas de mercado do Discogs", [
                    'release_id' => $releaseId,
                    'status' => $marketResponse->status(),
                    'body' => $marketResponse->body()
                ]);
            }
            
            // Registrar a resposta completa para debug com formato mais legível
            $marketData = $marketResponse->json();
            Log::info('Resposta detalhada do Discogs para market stats:', [
                'release_id' => $releaseId,
                'status_code' => $marketResponse->status(),
                'num_for_sale' => $marketData['num_for_sale'] ?? 'não disponível',
                'lowest_price' => $marketData['lowest_price'] ?? 'não disponível',
                'highest_price' => $marketData['highest_price'] ?? 'não disponível',
                'median_price' => $marketData['median_price'] ?? 'não disponível',
                'raw_response' => $marketData
            ]);
            
            // Buscar informações de vendas específicas do Brasil
            $brazilListings = $this->getBrazilListings($releaseId);
            $releaseData['brazil_listings'] = $brazilListings;

            if ($marketResponse->successful()) {
                $marketData = $marketResponse->json();
                
                // Verificar se temos dados de preço válidos
                $hasValidPriceData = isset($marketData['lowest_price']) || 
                                     isset($marketData['median_price']) || 
                                     isset($marketData['highest_price']);
                                     
                Log::info('Validade dos dados de preço:', [
                    'release_id' => $releaseId,
                    'has_valid_data' => $hasValidPriceData ? 'Sim' : 'Não',
                    'tipos_disponiveis' => array_keys($marketData)
                ]);
                
                // Adicionar os valores originais retornados pela API para debug
                $releaseData['raw_market_data'] = $marketData;
                
                // Preço mais baixo - usar exatamente o valor retornado pela API
                $lowestPrice = isset($marketData['lowest_price']) && is_numeric($marketData['lowest_price']) 
                    ? (float)$marketData['lowest_price'] 
                    : 0;
                $releaseData['lowest_price'] = $lowestPrice;
                
                // Preço médio - usar exatamente o valor retornado pela API
                $medianPrice = isset($marketData['median_price']) && is_numeric($marketData['median_price']) 
                    ? (float)$marketData['median_price'] 
                    : 0;
                $releaseData['median_price'] = $medianPrice;
                
                // Preço mais alto - usar exatamente o valor retornado pela API
                $highestPrice = isset($marketData['highest_price']) && is_numeric($marketData['highest_price']) 
                    ? (float)$marketData['highest_price'] 
                    : 0;
                $releaseData['highest_price'] = $highestPrice;
                
                // Número de cópias à venda - usar exatamente o valor retornado pela API
                $forSaleCount = isset($marketData['num_for_sale']) 
                    ? (int)$marketData['num_for_sale'] 
                    : 0;
                $releaseData['num_for_sale'] = $forSaleCount;
                
                // Verificar se temos informações de vendedores brasileiros
                $brazilInfo = $releaseData['brazil_listings'] ?? null;
                $hasBrazilSellers = isset($brazilInfo['has_brazil_sellers']) ? (bool)$brazilInfo['has_brazil_sellers'] : false;
                $brazilLowestPrice = isset($brazilInfo['lowest_price']) ? (float)$brazilInfo['lowest_price'] : 0;
                $brazilMedianPrice = isset($brazilInfo['median_price']) ? (float)$brazilInfo['median_price'] : 0;
                
                // Ajusta com base na raridade (quanto menos cópias à venda, maior o markup)
                $forSaleCount = isset($marketData['num_for_sale']) ? (int)$marketData['num_for_sale'] : 10;
                $rarityFactor = max(1.0, 1.5 - ($forSaleCount / 100)); // De 1.0 a 1.5 dependendo da raridade
                
                // Determinar preço sugerido com prioridade para dados brasileiros
                if ($hasBrazilSellers && $brazilMedianPrice > 0) {
                    // Se temos dados brasileiros válidos, usar o preço mediano brasileiro com markup de raridade
                    $releaseData['suggested_price'] = $brazilMedianPrice * min(1.2, $rarityFactor); // Markup menor para preços brasileiros
                    $releaseData['price_source'] = 'brazil';
                } elseif ($hasBrazilSellers && $brazilLowestPrice > 0) {
                    // Se temos apenas o preço mais baixo, calcular com base nele
                    $releaseData['suggested_price'] = $brazilLowestPrice * min(1.3, $rarityFactor + 0.1);
                    $releaseData['price_source'] = 'brazil_lowest';
                } else {
                    // Se não temos dados brasileiros, usar dados globais
                    $releaseData['suggested_price'] = $medianPrice * $rarityFactor;
                    $releaseData['price_source'] = 'global';
                }
                
                // Garantir que o preço sugerido não seja zero
                if ($releaseData['suggested_price'] <= 0) {
                    $releaseData['suggested_price'] = 15 * $rarityFactor; // Valor padrão mínimo
                    $releaseData['price_source'] = 'default';
                }
                
                Log::info('Preços calculados:', [
                    'global_min' => $releaseData['lowest_price'],
                    'global_median' => $releaseData['median_price'],
                    'global_max' => $releaseData['highest_price'],
                    'brazil_min' => $brazilLowestPrice,
                    'brazil_median' => $brazilMedianPrice,
                    'sugerido' => $releaseData['suggested_price'],
                    'fonte' => $releaseData['price_source'],
                    'fator_raridade' => $rarityFactor
                ]);
            }

            return $releaseData;
        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados do Discogs: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca e faz download de uma imagem do Discogs
     *
     * @param string $imageUrl URL da imagem
     * @return string|null Conteúdo da imagem ou null em caso de erro
     */
    public function fetchImage(string $imageUrl): ?string
    {
        try {
            $response = Http::get($imageUrl);
            
            if ($response->successful()) {
                return $response->body();
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Erro ao buscar imagem do Discogs: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtém detalhes de um artista no Discogs
     *
     * @param string $artistId ID do artista no Discogs
     * @return array|null Dados do artista ou null se não encontrado
     */
    public function getArtistDetails(string $artistId): ?array
    {
        try {
            if (empty($artistId)) {
                return null;
            }
            
            $response = Http::get("https://api.discogs.com/artists/{$artistId}", [
                'token' => config('services.discogs.token'),
            ]);

            if (!$response->successful()) {
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados do artista no Discogs: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtém detalhes de uma gravadora no Discogs
     *
     * @param string $labelId ID da gravadora no Discogs
     * @return array|null Dados da gravadora ou null se não encontrado
     */
    public function getLabelDetails(string $labelId): ?array
    {
        try {
            if (empty($labelId)) {
                return null;
            }
            
            $response = Http::get("https://api.discogs.com/labels/{$labelId}", [
                'token' => config('services.discogs.token'),
            ]);

            if (!$response->successful()) {
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados da gravadora no Discogs: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtém informações sobre discos à venda no Brasil para um lançamento específico
     *
     * @param string $releaseId ID do lançamento no Discogs
     * @return array Informações sobre vendas no Brasil
     */
    public function getBrazilListings(string $releaseId): array
    {
        try {
            // Primeiro tentamos obter diretamente a quantidade à venda na API do Discogs, independente do país
            // Buscar informações gerais do mercado para este lançamento
            $marketResponse = Http::get("https://api.discogs.com/marketplace/stats/{$releaseId}", [
                'token' => config('services.discogs.token'),
                'curr_abbr' => 'BRL' // Solicitar valores em reais
            ]);
            
            // Registrar os dados recebidos para este disco específico
            Log::info('Tentando obter dados de mercado para ID: ' . $releaseId, [
                'resposta' => $marketResponse->successful() ? $marketResponse->json() : 'Falha ao obter resposta'
            ]);
            
            // Agora fazer a busca de anúncios no Brasil usando a API de marketplace
            // Tentando sem o filtro de país primeiro para ver todas as listagens disponíveis
            $listingsResponse = Http::get("https://api.discogs.com/marketplace/listings", [
                'release_id' => $releaseId,
                'token' => config('services.discogs.token'),
                'currency' => 'BRL',
                'sort' => 'price',
                'per_page' => 100
            ]);
            
            if (!$listingsResponse->successful()) {
                Log::error('Falha ao consultar API do Discogs para listagens: ' . $listingsResponse->body());
                return [
                    'count' => 0,
                    'listings' => [],
                    'lowest_price' => 0,
                    'median_price' => 0,
                    'highest_price' => 0,
                    'has_brazil_sellers' => false
                ];
            }
            
            $data = $listingsResponse->json();
            
            // Logando os dados completos para debug
            Log::info('Dados completos de marketplace para o disco ' . $releaseId, [
                'total_listings' => count($data['listings'] ?? []),
                'pagination' => $data['pagination'] ?? [],
            ]);
            
            $listings = $data['listings'] ?? [];
            
            // Filtrar anúncios do Brasil (verificando tanto country quanto location)
            // e logando informações sobre vendedores para debug
            $brazilListings = [];
            $sellerInfo = [];
            
            foreach ($listings as $listing) {
                $isFromBrazil = false;
                $country = $listing['seller']['country'] ?? '';
                $location = $listing['seller']['location'] ?? '';
                
                // Adicionar informação do vendedor para debug
                $sellerInfo[] = [
                    'country' => $country,
                    'location' => $location,
                    'price' => $listing['price']['value'] ?? 0,
                    'condition' => $listing['condition'] ?? '',
                ];
                
                // Verificar se é do Brasil
                if (!empty($country) && 
                    (strtolower($country) === 'brazil' || 
                     strtolower($country) === 'brasil' ||
                     strtolower($country) === 'br')) {
                    $isFromBrazil = true;
                } elseif (!empty($location)) {
                    $locationLower = strtolower($location);
                    if (strpos($locationLower, 'brazil') !== false || 
                        strpos($locationLower, 'brasil') !== false ||
                        strpos($locationLower, ', br') !== false ||
                        strpos($locationLower, 'br,') !== false) {
                        $isFromBrazil = true;
                    }
                }
                
                if ($isFromBrazil) {
                    $brazilListings[] = $listing;
                }
            }
            
            // Logando informações de vendedores para debug
            Log::info('Informações de todos os vendedores para o disco ' . $releaseId, [
                'total_sellers' => count($sellerInfo),
                'sellers_info' => $sellerInfo
            ]);
            
            // Calcular informações úteis
            $count = count($brazilListings);
            $lowestPrice = 0;
            $medianPrice = 0;
            $highestPrice = 0;
            
            if ($count > 0) {
                // Coletar todos os preços dos anúncios brasileiros
                $prices = array_map(function($listing) {
                    $value = $listing['price']['value'] ?? 0;
                    // Converter para número se for string
                    return is_numeric($value) ? (float)$value : 0;
                }, $brazilListings);
                
                // Filtrar preços válidos (acima de zero)
                $prices = array_filter($prices, function($price) {
                    return $price > 0;
                });
                
                if (!empty($prices)) {
                    // Preço mais baixo
                    $lowestPrice = min($prices);
                    
                    // Preço mais alto
                    $highestPrice = max($prices);
                    
                    // Preço mediano (ordenar e pegar o do meio)
                    sort($prices);
                    $middle = floor(count($prices) / 2);
                    $medianPrice = $prices[$middle];
                }
            }
            
            Log::info('Encontrados ' . $count . ' anúncios no Brasil para o disco ID ' . $releaseId, [
                'min' => $lowestPrice,
                'median' => $medianPrice,
                'max' => $highestPrice
            ]);
            
            return [
                'count' => $count,
                'listings' => $brazilListings,
                'lowest_price' => $lowestPrice,
                'median_price' => $medianPrice,
                'highest_price' => $highestPrice,
                'has_brazil_sellers' => ($count > 0)
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao buscar anúncios do Brasil no Discogs: ' . $e->getMessage());
            return [
                'count' => 0,
                'listings' => [],
                'lowest_price' => 0,
                'median_price' => 0,
                'highest_price' => 0,
                'has_brazil_sellers' => false
            ];
        }
    }
}
