<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentGateway;
use Illuminate\Support\Str;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Iniciando PaymentGatewaySeeder...');
        
        // Desabilitar verificação de chaves estrangeiras temporariamente
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Obter os gateways existentes
        $existingGateways = PaymentGateway::pluck('id', 'code')->toArray();
        
        $gateways = [
            [
                'name' => 'Mercado Pago',
                'code' => 'mercadopago',
                'active' => true,
                'sandbox_mode' => true,
                'credentials' => [
                    'public_key' => 'SEU_PUBLIC_KEY_AQUI',
                    'access_token' => 'SEU_ACCESS_TOKEN_AQUI',
                ],
                'settings' => [
                    'sandbox' => true,
                    'available_methods' => ['credit_card', 'boleto', 'pix']
                ]
            ],
            [
                'name' => 'PagSeguro',
                'code' => 'pagseguro',
                'active' => false,
                'sandbox_mode' => true,
                'credentials' => [
                    'email' => 'seu-email@exemplo.com',
                    'token' => 'SEU_TOKEN_AQUI',
                ],
                'settings' => [
                    'sandbox' => true,
                    'available_methods' => ['credit_card', 'boleto']
                ]
            ],
            [
                'name' => 'Rede Itaú',
                'code' => 'rede',
                'active' => false,
                'sandbox_mode' => true,
                'credentials' => [
                    'pv' => 'SEU_PV_AQUI',
                    'token' => 'SEU_TOKEN_AQUI',
                ],
                'settings' => [
                    'sandbox' => true,
                    'available_methods' => ['credit_card']
                ]
            ]
        ];
        
        foreach ($gateways as $gatewayData) {
            try {
                $this->command->info("Processando gateway: " . $gatewayData['name']);
                
                // Verifica se o gateway já existe
                $gateway = PaymentGateway::firstOrNew(['code' => $gatewayData['code']]);
                
                // Atualiza os dados básicos
                $gateway->name = $gatewayData['name'];
                $gateway->active = $gatewayData['active'];
                $gateway->sandbox_mode = $gatewayData['sandbox_mode'];
                
                // Define as credenciais (serão automaticamente criptografadas pelo cast do modelo)
                $gateway->credentials = $gatewayData['credentials'];
                
                // Define as configurações (serão automaticamente convertidas para JSON pelo cast do modelo)
                $gateway->settings = $gatewayData['settings'];
                
                // Salva o gateway
                $gateway->save();
                
                $this->command->info('Gateway processado com sucesso: ' . $gatewayData['name']);
                
            } catch (\Exception $e) {
                $this->command->error('Erro ao processar gateway ' . ($gatewayData['name'] ?? 'desconhecido') . ': ' . $e->getMessage());
                $this->command->error('Arquivo: ' . $e->getFile() . ' na linha ' . $e->getLine());
            }
        }
        
        // Remover gateways que não estão mais na lista
        if (!empty($existingGateways)) {
            $this->command->info('Removendo gateways obsoletos...');
            foreach ($existingGateways as $code => $id) {
                try {
                    $gateway = PaymentGateway::find($id);
                    if ($gateway) {
                        $this->command->info('Removendo gateway obsoleto: ' . $gateway->name);
                        $gateway->delete();
                    }
                } catch (\Exception $e) {
                    $this->command->error('Erro ao remover gateway: ' . $e->getMessage());
                }
            }
        }
        
        // Reativar verificação de chaves estrangeiras
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $this->command->info('PaymentGatewaySeeder concluído!');
    }
}
