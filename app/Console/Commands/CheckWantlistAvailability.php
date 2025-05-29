<?php

namespace App\Console\Commands;

use App\Models\Wantlist;
use App\Notifications\VinylAvailableNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckWantlistAvailability extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-wantlist-availability';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica produtos na wantlist e notifica usuários quando estiverem disponíveis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verificando disponibilidade de produtos na wantlist...');
        
        // Buscar itens da wantlist que precisam ser verificados
        $wantlistItems = Wantlist::getItemsToCheck();
        
        $this->info("Encontrados {$wantlistItems->count()} itens para verificar.");
        
        $notificationsSent = 0;
        
        foreach ($wantlistItems as $item) {
            // Verifica se o vinyl master existe e se está disponível
            if (!$item->vinylMaster) {
                $this->warn("Vinil não encontrado para o item de wantlist ID: {$item->id}");
                continue;
            }
            
            // Verifica se o produto está disponível (tem estoque)
            if ($item->vinylMaster->isAvailable()) {
                $this->info("Vinil disponível: {$item->vinylMaster->title}");
                
                try {
                    // Enviar notificação ao usuário
                    $item->user->notify(new VinylAvailableNotification($item->vinylMaster));
                    
                    // Atualizar o status da notificação
                    $item->update([
                        'notification_sent' => true,
                        'last_notification_at' => now(),
                    ]);
                    
                    $notificationsSent++;
                    $this->info("Notificação enviada para {$item->user->name} sobre {$item->vinylMaster->title}");
                } catch (\Exception $e) {
                    $this->error("Erro ao enviar notificação: {$e->getMessage()}");
                    Log::error("Erro ao enviar notificação de wantlist", [
                        'exception' => $e->getMessage(),
                        'wantlist_id' => $item->id,
                        'user_id' => $item->user_id,
                        'vinyl_id' => $item->vinyl_master_id
                    ]);
                }
            } else {
                $this->line("Vinil ainda indisponível: {$item->vinylMaster->title}");
            }
        }
        
        $this->info("Processo concluído. Notificações enviadas: {$notificationsSent}");
        
        return 0;
    }
}
