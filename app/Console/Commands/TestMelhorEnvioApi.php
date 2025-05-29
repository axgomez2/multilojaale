<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MelhorEnvio;
use Illuminate\Support\Facades\Http;

class TestMelhorEnvioApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'melhorenvio:test {--debug : Mostrar informações detalhadas de debug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a conexão com a API do Melhor Envio';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testando conexão com a API do Melhor Envio...');
        
        $shippingService = new MelhorEnvio();
        
        // Mostrar informações de configuração se o modo debug estiver ativado
        if ($this->option('debug')) {
            $this->info('URL da API: ' . config('services.melhorenvio.url'));
            $this->info('CEP da loja: ' . config('services.melhorenvio.store_zip'));
            
            $token = config('services.melhorenvio.token');
            if (!empty($token)) {
                $tokenLength = strlen($token);
                $this->info("Token configurado: Sim (tamanho: {$tokenLength} caracteres)");
                $this->info("Primeiros 20 caracteres do token: " . substr($token, 0, 20) . '...');
            } else {
                $this->error('Token não configurado!');
            }
        }
        
        // Testar a conexão buscando as empresas disponíveis
        $response = $shippingService->testConnection();
        
        if ($response['success']) {
            $this->info("\n✅ Conexão estabelecida com sucesso!");
            $this->info('Empresas de entrega disponíveis:');
            
            foreach ($response['companies'] as $company) {
                $this->line(" - {$company['name']}");
            }
            
            // Testar um cálculo de frete simples
            $this->info("\nTestando cálculo de frete simples...");
            
            $testItems = [
                [
                    'id' => 1,
                    'quantity' => 1
                ]
            ];
            
            $calculationResult = $shippingService->calculateShipping('01001000', $testItems);
            
            if ($calculationResult['success']) {
                $this->info('✅ Cálculo de frete realizado com sucesso!');
                $this->info('Opções de entrega encontradas: ' . count($calculationResult['options']));
            } else {
                $this->error('❌ Falha no cálculo de frete: ' . ($calculationResult['message'] ?? 'Erro desconhecido'));
                
                if ($this->option('debug')) {
                    $this->line("\nDica: Verifique se o token da API está correto e tem permissões adequadas.");
                    $this->line('Se o token foi gerado recentemente, pode ser necessário aguardar alguns minutos para que seja ativado.');
                }
            }
        } else {
            $this->error('❌ Falha na conexão: ' . ($response['message'] ?? 'Erro desconhecido'));
            
            if ($this->option('debug')) {
                $this->line("\nDica: Verifique as seguintes possibilidades:");
                $this->line('1. O token da API está correto?');
                $this->line('2. O formato do token está correto? Deve começar com "Bearer ".');
                $this->line('3. A URL da API está correta?');
                $this->line('4. Você tem conexão com a internet?');
            }
        }
    }
}
