<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use App\Services\MelhorEnvio;

class MelhorEnvioController extends Controller
{
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $baseUrl;
    protected $melhorEnvio;

    public function __construct(MelhorEnvio $melhorEnvio)
    {
        $this->clientId = env('MELHOR_ENVIO_CLIENT_ID');
        $this->clientSecret = env('MELHOR_ENVIO_CLIENT_SECRET');
        $this->redirectUri = env('MELHOR_ENVIO_REDIRECT_URI');
        $this->baseUrl = env('MELHOR_ENVIO_URL', 'https://sandbox.melhorenvio.com.br/api/v2/');
        $this->melhorEnvio = $melhorEnvio;
    }

    /**
     * Página de demonstração do cálculo de frete
     */
    public function index()
    {
        $isAuthenticated = $this->melhorEnvio->isAuthenticated();
        
        return view('shipping.index', [
            'isAuthenticated' => $isAuthenticated
        ]);
    }
    
    /**
     * Demonstração simples de cálculo de frete
     */
    public function calculateShipping(Request $request)
    {
        $request->validate([
            'cep_destino' => 'required|string|size:9'  // Formato: 00000-000
        ]);
        
        // CEP de destino
        $toZipCode = $request->cep_destino;
        
        // Exemplo simples com produto padrão
        $products = [
            [
                'id' => 'test-product',
                'quantity' => 1
            ]
        ];
        
        $result = $this->melhorEnvio->calculateShipping($toZipCode, $products);
        
        return view('shipping.result', [
            'result' => $result,
            'cep' => $toZipCode
        ]);
    }

    /**
     * Redireciona o usuário para a página de autorização do Melhor Envio
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect()
    {
        $authUrl = 'https://sandbox.melhorenvio.com.br/oauth/authorize';
        
        if (env('MELHOR_ENVIO_SANDBOX', true) === false) {
            $authUrl = 'https://melhorenvio.com.br/oauth/authorize';
        }

        $queryParams = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'shipping-calculate shipping-cancel shipping-checkout shipping-companies shipping-generate shipping-preview shipping-print shipping-tracking cart-read cart-write users-read users-write notifications-read companies-read ecommerce-shipping orders-read'
        ];

        $url = $authUrl . '?' . http_build_query($queryParams);

        return redirect($url);
    }

    /**
     * Callback para processar o código de autorização retornado pelo Melhor Envio
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request)
    {
        $code = $request->code;

        if (!$code) {
            Log::error('Código de autorização do Melhor Envio não recebido');
            return redirect()->route('admin.settings.shipping')->with('error', 'Autorização com o Melhor Envio falhou. Código não recebido.');
        }

        try {
            $tokenUrl = 'https://sandbox.melhorenvio.com.br/oauth/token';
            
            if (env('MELHOR_ENVIO_SANDBOX', true) === false) {
                $tokenUrl = 'https://melhorenvio.com.br/oauth/token';
            }

            $response = Http::post($tokenUrl, [
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUri,
                'code' => $code
            ]);

            if ($response->successful()) {
                $tokenData = $response->json();

                // Armazenar tokens no cache
                Cache::put('melhorenvio_access_token', $tokenData['access_token'], now()->addSeconds($tokenData['expires_in']));
                Cache::put('melhorenvio_refresh_token', $tokenData['refresh_token'], now()->addMonths(1));
                
                // Opcional: Armazenar em .env ou no banco de dados para uso persistente
                $this->updateEnvToken($tokenData['access_token']);

                return redirect()->route('admin.settings.shipping')->with('success', 'Autorização com o Melhor Envio realizada com sucesso!');
            } else {
                Log::error('Erro ao obter token do Melhor Envio', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return redirect()->route('admin.settings.shipping')->with('error', 'Erro ao obter token do Melhor Envio: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Exceção ao obter token do Melhor Envio: ' . $e->getMessage());
            return redirect()->route('admin.settings.shipping')->with('error', 'Erro ao processar a autorização: ' . $e->getMessage());
        }
    }

    /**
     * Atualiza o token no arquivo .env
     *
     * @param string $token
     * @return bool
     */
    protected function updateEnvToken($token)
    {
        try {
            $envFile = base_path('.env');
            $envContents = file_get_contents($envFile);

            if (strpos($envContents, 'MELHOR_ENVIO_TOKEN=') !== false) {
                $envContents = preg_replace('/MELHOR_ENVIO_TOKEN=.*/', 'MELHOR_ENVIO_TOKEN=' . $token, $envContents);
            } else {
                $envContents .= "\nMELHOR_ENVIO_TOKEN=" . $token;
            }

            file_put_contents($envFile, $envContents);
            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar token no .env: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Renova o token de acesso usando o refresh token
     *
     * @return array|bool
     */
    public function refreshToken()
    {
        try {
            $refreshToken = Cache::get('melhorenvio_refresh_token');

            if (!$refreshToken) {
                Log::error('Refresh token não encontrado no cache.');
                return false;
            }

            $tokenUrl = 'https://sandbox.melhorenvio.com.br/oauth/token';
            
            if (env('MELHOR_ENVIO_SANDBOX', true) === false) {
                $tokenUrl = 'https://melhorenvio.com.br/oauth/token';
            }

            $response = Http::post($tokenUrl, [
                'grant_type' => 'refresh_token',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $refreshToken
            ]);

            if ($response->successful()) {
                $tokenData = $response->json();

                // Atualizar tokens no cache
                Cache::put('melhorenvio_access_token', $tokenData['access_token'], now()->addSeconds($tokenData['expires_in']));
                
                if (isset($tokenData['refresh_token'])) {
                    Cache::put('melhorenvio_refresh_token', $tokenData['refresh_token'], now()->addMonths(1));
                }
                
                // Opcional: Atualizar em .env
                $this->updateEnvToken($tokenData['access_token']);

                return [
                    'success' => true,
                    'token' => $tokenData['access_token']
                ];
            } else {
                Log::error('Erro ao renovar token do Melhor Envio', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exceção ao renovar token do Melhor Envio: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Teste direto da API para depuração
     */
    public function testApi()
    {
        try {
            // Produtos para teste - usando valores simples
            $products = [
                [
                    'id' => 'test-product',
                    'quantity' => 1
                ]
            ];
            
            // CEP de destino para teste
            $destinationZip = '01001000';
            
            // Calcula frete usando o serviço consolidado
            $result = $this->melhorEnvio->calculateShipping($destinationZip, $products);
            
            return view('shipping.test-result', [
                'result' => $result,
                'cep' => $destinationZip
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
