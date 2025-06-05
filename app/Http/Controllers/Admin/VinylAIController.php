<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VinylAIController extends Controller
{
    /**
     * Gera uma descrição para um vinil usando IA.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateDescription(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string',
                'artists' => 'required|string',
                'year' => 'nullable|string',
                'genre' => 'nullable|string',
                'condition' => 'nullable|string',
                'buyPrice' => 'nullable|numeric',
                'promoPrice' => 'nullable|numeric',
            ]);
            
            // Construir o prompt para a API de IA
            $prompt = "Crie uma descrição detalhada e atraente em português para um disco de vinil com as seguintes informações:\n";
            $prompt .= "Título: {$request->title}\n";
            $prompt .= "Artista(s): {$request->artists}\n";
            
            if ($request->year) {
                $prompt .= "Ano de lançamento: {$request->year}\n";
            }
            
            if ($request->genre) {
                $prompt .= "Gênero: {$request->genre}\n";
            }
            
            if ($request->condition) {
                $prompt .= "Estado de conservação: {$request->condition}\n";
            }
            
            if ($request->buyPrice) {
                $prompt .= "Preço: R$ " . number_format($request->buyPrice, 2, ',', '.') . "\n";
            }
            
            if ($request->promoPrice) {
                $prompt .= "Preço promocional: R$ " . number_format($request->promoPrice, 2, ',', '.') . "\n";
            }
            
            $prompt .= "\nA descrição deve ter entre 2-3 parágrafos, destacar aspectos musicais e históricos do álbum, e incluir um apelo à compra caso tenha informações de preço.";
            
            // Aqui você adicionaria a chamada para a API de IA (OpenAI, Anthropic, etc)
            // Este é um exemplo usando a API da OpenAI, você precisará substituir pela sua implementação
            
            // Simulando uma resposta para demonstração (remova este bloco quando implementar a API real)
            $description = $this->simulateAIResponse($request->title, $request->artists, $request->year, $request->genre);
            
            return response()->json([
                'description' => $description
            ]);
            
            /* Implementação com OpenAI (descomente e adapte conforme necessário)
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => 'Você é um especialista em música e discos de vinil, com conhecimento profundo sobre história da música e álbuns.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.7,
                'max_tokens' => 500
            ]);
            
            if ($response->successful()) {
                $description = $response->json()['choices'][0]['message']['content'];
                return response()->json([
                    'description' => $description
                ]);
            } else {
                Log::error('Erro na API de IA: ' . $response->body());
                return response()->json(['error' => 'Não foi possível gerar a descrição'], 500);
            }
            */
        } catch (\Exception $e) {
            Log::error('Erro ao gerar descrição: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao processar a solicitação'], 500);
        }
    }
    
    /**
     * Traduz um texto para português usando IA.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function translateDescription(Request $request)
    {
        try {
            $request->validate([
                'text' => 'required|string',
            ]);
            
            // Construir o prompt para a API de IA
            $prompt = "Traduza o seguinte texto para português brasileiro, mantendo o tom e estilo do original:\n\n{$request->text}";
            
            // Simulando uma resposta para demonstração
            $translation = $this->simulateTranslation($request->text);
            
            return response()->json([
                'translation' => $translation
            ]);
            
            /* Implementação com OpenAI (descomente e adapte conforme necessário)
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => 'Você é um tradutor profissional especializado em música e cultura.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.3,
                'max_tokens' => 500
            ]);
            
            if ($response->successful()) {
                $translation = $response->json()['choices'][0]['message']['content'];
                return response()->json([
                    'translation' => $translation
                ]);
            } else {
                Log::error('Erro na API de IA para tradução: ' . $response->body());
                return response()->json(['error' => 'Não foi possível traduzir o texto'], 500);
            }
            */
        } catch (\Exception $e) {
            Log::error('Erro ao traduzir texto: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao processar a solicitação'], 500);
        }
    }
    
    /**
     * Simula uma resposta da IA para fins de demonstração.
     * Remove esta função ao implementar a API real.
     */
    private function simulateAIResponse($title, $artist, $year = null, $genre = null)
    {
        $yearText = $year ? " lançado em {$year}" : "";
        $genreText = $genre ? " do gênero {$genre}" : "";
        
        return "O álbum \"{$title}\" de {$artist}{$yearText}{$genreText} é uma verdadeira obra-prima que marcou época. Com seu som característico e arranjos inovadores, este disco traz uma combinação única de melodias envolventes e letras profundas que emocionam o ouvinte.

A qualidade das gravações neste vinil é excepcional, proporcionando uma experiência auditiva autêntica que só o formato analógico consegue entregar. Cada faixa foi cuidadosamente masterizada para preservar a dinâmica original e os detalhes sonoros que fazem deste álbum um clássico imperdível para colecionadores e amantes da música de qualidade.

Esta edição especial em vinil é uma excelente oportunidade para adicionar um item valioso à sua coleção. O estado de conservação garante uma reprodução fiel do áudio original, transportando você diretamente para a época de ouro da música. Uma aquisição essencial para quem valoriza não apenas a música, mas também a experiência completa que só o vinil proporciona.";
    }
    
    /**
     * Simula uma tradução para fins de demonstração.
     * Remove esta função ao implementar a API real.
     */
    private function simulateTranslation($text)
    {
        // Se o texto já parece estar em português, apenas retorna-o com uma pequena modificação
        if (preg_match('/[áàâãéèêíìóòôõúùüçÁÀÂÃÉÈÊÍÌÓÒÔÕÚÙÜÇ]/', $text)) {
            return $text . "\n\n(Texto já está em português)";
        }
        
        // Simula uma tradução básica
        $englishPhrases = [
            'vinyl' => 'vinil',
            'album' => 'álbum',
            'record' => 'disco',
            'music' => 'música',
            'tracks' => 'faixas',
            'released' => 'lançado',
            'featuring' => 'apresentando',
            'artist' => 'artista',
            'collection' => 'coleção',
            'songs' => 'canções',
            'hit' => 'sucesso',
            'sound' => 'som',
            'quality' => 'qualidade',
            'limited edition' => 'edição limitada',
            'rare' => 'raro',
            'classic' => 'clássico'
        ];
        
        $translatedText = $text;
        foreach ($englishPhrases as $en => $pt) {
            $translatedText = str_ireplace($en, $pt, $translatedText);
        }
        
        return $translatedText . "\n\n(Tradução simulada para demonstração)";
    }
}
