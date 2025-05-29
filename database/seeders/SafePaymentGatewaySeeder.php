<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentGateway;

class SafePaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Iniciando SafePaymentGatewaySeeder...');
        
        // Verifica se a tabela existe
        if (!DB::getSchemaBuilder()->hasTable('payment_gateways')) {
            $this->command->error('A tabela payment_gateways não existe!');
            return;
        }
        
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
        
        // Desativa temporariamente as verificações de chave estrangeira
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        foreach ($gateways as $gatewayData) {
            try {
                $this->command->info("Processando gateway: " . $gatewayData['name']);
                
                // Verifica se o gateway já existe
                $gateway = PaymentGateway::where('code', $gatewayData['code'])->first();
                
                if ($gateway) {
                    $this->command->info('Atualizando gateway existente: ' . $gatewayData['name']);
                } else {
                    $gateway = new PaymentGateway();
                    $gateway->id = (string) \Illuminate\Support\Str::uuid();
                    $gateway->code = $gatewayData['code'];
                    $this->command->info('Criando novo gateway: ' . $gatewayData['name']);
                }
                
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
                $this->command->error('Erro ao processar gateway ' . ($gatewayData['name'] ?? 'desconhecido'));
                $this->command->error('Mensagem: ' . $e->getMessage());
                $this->command->error('Arquivo: ' . $e->getFile() . ':' . $e->getLine());
                continue;
            }
        }
        
        // Reativa as verificações de chave estrangeira
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $this->command->info('SafePaymentGatewaySeeder concluído!');
    }
}
