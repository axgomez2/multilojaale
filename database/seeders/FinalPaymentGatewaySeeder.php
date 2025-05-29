<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FinalPaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Iniciando FinalPaymentGatewaySeeder...');
        
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
                $exists = DB::table('payment_gateways')
                    ->where('code', $gatewayData['code'])
                    ->exists();
                
                // Prepara os dados para inserção/atualização
                $data = [
                    'name' => $gatewayData['name'],
                    'active' => $gatewayData['active'],
                    'sandbox_mode' => $gatewayData['sandbox_mode'],
                    'credentials' => json_encode($gatewayData['credentials']), // Converte para JSON diretamente
                    'settings' => json_encode($gatewayData['settings']), // Converte para JSON diretamente
                    'updated_at' => now(),
                ];
                
                if ($exists) {
                    // Atualiza o gateway existente
                    DB::table('payment_gateways')
                        ->where('code', $gatewayData['code'])
                        ->update($data);
                    $this->command->info('Gateway atualizado: ' . $gatewayData['name']);
                } else {
                    // Cria um novo gateway
                    $data['id'] = (string) Str::uuid();
                    $data['code'] = $gatewayData['code'];
                    $data['created_at'] = now();
                    
                    DB::table('payment_gateways')->insert($data);
                    $this->command->info('Gateway criado: ' . $gatewayData['name']);
                }
                
            } catch (\Exception $e) {
                $this->command->error('Erro ao processar gateway ' . ($gatewayData['name'] ?? 'desconhecido'));
                $this->command->error('Mensagem: ' . $e->getMessage());
                $this->command->error('Arquivo: ' . $e->getFile() . ':' . $e->getLine());
                continue;
            }
        }
        
        // Reativa as verificações de chave estrangeira
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $this->command->info('FinalPaymentGatewaySeeder concluído com sucesso!');
    }
}
