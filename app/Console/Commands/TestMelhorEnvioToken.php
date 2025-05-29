<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestMelhorEnvioToken extends Command
{
    protected $signature = 'melhorenvio:test-token';
    protected $description = 'Test Melhor Envio API token connection';

    public function handle()
    {
        $this->info('Testing connection to Melhor Envio API...');
        
        try {
            $baseUrl = config('services.melhorenvio.url', env('MELHOR_ENVIO_URL'));
            $token = config('services.melhorenvio.token', env('MELHOR_ENVIO_TOKEN'));
            
            $this->info("Base URL: {$baseUrl}");
            $this->info("Using token: " . (empty($token) ? 'Not set' : 'Set (hidden)'));
            
            $response = Http::withToken($token)
                ->acceptJson()
                ->get($baseUrl . 'me');
                
            if ($response->successful()) {
                $this->info('Connection successful! Status code: ' . $response->status());
                $userData = $response->json();
                
                $this->info('User data:');
                $this->info('- Name: ' . ($userData['firstname'] ?? 'N/A') . ' ' . ($userData['lastname'] ?? ''));
                $this->info('- Email: ' . ($userData['email'] ?? 'N/A'));
                $this->info('- Company: ' . ($userData['company']['name'] ?? 'N/A'));
                
                return Command::SUCCESS;
            } else {
                $this->error('Connection failed! Status code: ' . $response->status());
                $this->error('Response: ' . $response->body());
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
