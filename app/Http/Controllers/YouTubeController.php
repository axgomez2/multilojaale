<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YouTubeController extends Controller
{
    public function search(Request $request)
    {
        try {
            // Analisar o corpo da requisição
            $content = $request->getContent();
            $data = json_decode($content, true) ?: [];
            $query = $data['query'] ?? $request->input('query', '');
            
            Log::info('Busca YouTube', ['query' => $query]);
            
            if (empty($query)) {
                return response()->json(['error' => 'Parâmetro de busca não fornecido'], 400);
            }
            
            // Verificar a chave da API
            $apiKey = config('services.youtube.api_key');
            Log::info('YouTube API Key status', ['configured' => !empty($apiKey), 'key_length' => strlen($apiKey ?? '')]);
            
            // Se não tiver API key, mostrar logs detalhados e retornar erro
            if (empty($apiKey)) {
                Log::warning('API key do YouTube não encontrada. Verifique o arquivo .env.');
                return response()->json([
                    'error' => 'API Key do YouTube não configurada. Adicione YOUTUBE_API_KEY no arquivo .env',
                    'demo' => true,
                    'items' => $this->getDemoResults($query, true)
                ], 200);
            }
            
            // Fazer a requisição para a API do YouTube
            $response = Http::timeout(10)->get('https://www.googleapis.com/youtube/v3/search', [
                'part' => 'snippet',
                'q' => $query,
                'type' => 'video',
                'videoCategoryId' => '10', // Categoria Música
                'key' => $apiKey,
                'maxResults' => 5
            ]);
            
            // Se a requisição falhar, log detalhado
            if ($response->failed()) {
                Log::error('Falha na API do YouTube:', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'query' => $query
                ]);
                
                return response()->json([
                    'error' => 'Falha na requisição para a API do YouTube: ' . $response->status(),
                    'demo' => true,
                    'items' => $this->getDemoResults($query, true)
                ], 200);
            }
            
            $data = $response->json();
            
            // Debug logs
            Log::info('Resposta da API do YouTube:', [
                'success' => true,
                'item_count' => isset($data['items']) ? count($data['items']) : 0
            ]);
            
            if (!isset($data['items']) || empty($data['items'])) {
                // Sem resultados
                return response()->json([]); 
            }
            
            return response()->json($data['items']);
            
        } catch (\Exception $e) {
            Log::error('Erro na busca YouTube:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Em caso de erro, retornar dados de demonstração para que a interface funcione
            return response()->json([
                'error' => 'Erro ao processar a busca: ' . $e->getMessage(),
                'demo' => true,
                'items' => $this->getDemoResults($query ?? 'music', true)
            ], 200);
        }
    }
    
    /**
     * Retorna resultados de demonstração para quando a API real não estiver disponível
     * @param string $query A consulta realizada
     * @param bool $returnArray Se verdadeiro, retorna um array ao invés de uma resposta JSON
     * @return array|\Illuminate\Http\JsonResponse
     */
    private function getDemoResults($query, $returnArray = false)
    {
        // Formatar a query para tornar as sugestões mais realistas
        $cleanQuery = trim(htmlspecialchars($query));
        $queryWords = explode(' ', $cleanQuery);
        $artistName = count($queryWords) > 2 ? implode(' ', array_slice($queryWords, 0, 2)) : $cleanQuery;
        $trackName = count($queryWords) > 2 ? implode(' ', array_slice($queryWords, 2)) : 'Music';
        
        // Resultados simulados para fins de demonstração
        $demoResults = [
            [
                'id' => ['videoId' => 'dQw4w9WgXcQ'],
                'snippet' => [
                    'title' => $artistName . ' - ' . $trackName . ' (Official Video)',
                    'description' => 'Vídeo oficial para ' . $trackName . ' de ' . $artistName . '. Os dados reais da API do YouTube não estão disponíveis no momento.',
                    'channelTitle' => $artistName . ' Official',
                    'publishedAt' => date('c'),
                    'thumbnails' => [
                        'default' => ['url' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/default.jpg']
                    ]
                ]
            ],
            [
                'id' => ['videoId' => 'y6120QOlsfU'],
                'snippet' => [
                    'title' => $artistName . ' - ' . $trackName . ' (Lyric Video)',
                    'description' => 'Lyric video para ' . $trackName . ' de ' . $artistName . '. Ative a API do YouTube no arquivo .env para obter resultados reais.',
                    'channelTitle' => $artistName . ' VEVO',
                    'publishedAt' => date('c', strtotime('-1 day')),
                    'thumbnails' => [
                        'default' => ['url' => 'https://img.youtube.com/vi/y6120QOlsfU/default.jpg']
                    ]
                ]
            ],
            [
                'id' => ['videoId' => 'L_jWHffIx5E'],
                'snippet' => [
                    'title' => $artistName . ' performing ' . $trackName . ' Live',
                    'description' => 'Performance ao vivo de ' . $trackName . ' por ' . $artistName . '. Configure a chave YOUTUBE_API_KEY para obter dados reais.',
                    'channelTitle' => 'Live Music Channel',
                    'publishedAt' => date('c', strtotime('-3 days')),
                    'thumbnails' => [
                        'default' => ['url' => 'https://img.youtube.com/vi/L_jWHffIx5E/default.jpg']
                    ]
                ]
            ],
        ];
        
        return $returnArray ? $demoResults : response()->json($demoResults);
    }
}

