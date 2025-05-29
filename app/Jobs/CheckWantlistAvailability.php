<?php

namespace App\Jobs;

use App\Models\Wantlist;
use App\Models\VinylMaster;
use App\Notifications\VinylAvailableNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckWantlistAvailability implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * O tempo em segundos para bloquear o job de unicidade.
     *
     * @var int
     */
    public $uniqueFor = 3600; // 1 hora

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Iniciando verificação de disponibilidade para itens na Wantlist');
        
        // Buscar todos os itens da Wantlist sem notificação ou com notificação antiga
        $wantlistItems = Wantlist::with(['user', 'vinylMaster.vinylSecs', 'vinylMaster.artists'])
            ->where(function ($query) {
                $query->where('notification_sent', false)
                    ->orWhereNull('last_notification_at')
                    ->orWhere('last_notification_at', '<=', Carbon::now()->subDays(30));
            })
            ->get();
            
        $count = 0;
        
        foreach ($wantlistItems as $item) {
            // Verificar se o vinil agora está disponível
            $vinylMaster = $item->vinylMaster;
            
            if (!$vinylMaster) {
                // Vinil não existe mais, remover da wantlist
                $item->delete();
                continue;
            }
            
            $isAvailable = $vinylMaster->vinylSecs->where('active', true)->count() > 0;
            
            if ($isAvailable) {
                // Vinil está disponível, notificar o usuário
                try {
                    $item->user->notify(new VinylAvailableNotification($vinylMaster));
                    
                    // Atualizar o status da notificação
                    $item->notification_sent = true;
                    $item->last_notification_at = now();
                    $item->save();
                    
                    $count++;
                    
                    Log::info('Notificação enviada para usuário sobre disponibilidade de vinil', [
                        'user_id' => $item->user_id,
                        'vinyl_master_id' => $item->vinyl_master_id,
                        'vinyl_title' => $vinylMaster->title
                    ]);
                } catch (\Exception $e) {
                    Log::error('Erro ao enviar notificação de disponibilidade', [
                        'error' => $e->getMessage(),
                        'user_id' => $item->user_id,
                        'vinyl_master_id' => $item->vinyl_master_id
                    ]);
                }
            }
        }
        
        Log::info("Verificação de Wantlist concluída. {$count} notificações enviadas.");
    }
    
    /**
     * O identificador único para o job.
     */
    public function uniqueId(): string
    {
        return 'check_wantlist_availability';
    }
}
