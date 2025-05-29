<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DiscogsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class DebugController extends Controller
{
    protected $discogsService;
    
    public function __construct(DiscogsService $discogsService)
    {
        $this->discogsService = $discogsService;
    }
    
    /**
     * Página de depuração para a API do Discogs
     */
    public function discogs(Request $request)
    {
        $data = [];
        
        // Verificar se temos um ID para consultar
        if ($request->has('release_id') && !empty($request->release_id)) {
            $releaseId = $request->release_id;
            
            // Limpar o log existente para facilitar a leitura dos resultados
            if (File::exists(storage_path('logs/laravel.log'))) {
                File::put(storage_path('logs/laravel.log'), '');
            }
            
            // Buscar dados do release
            $releaseData = $this->discogsService->getRelease($releaseId);
            
            // Verificar se temos logs recentes da operação
            $logs = $this->getRecentLogs();
            
            // Retornar os dados para a view
            $data = [
                'releaseData' => $releaseData,
                'marketData' => $releaseData['raw_market_data'] ?? [],
                'brazilListings' => $releaseData['brazil_listings'] ?? [],
                'logs' => $logs
            ];
        }
        
        return view('admin.debug-discogs', $data);
    }
    
    /**
     * Obtém os logs recentes formatados
     */
    private function getRecentLogs()
    {
        $logs = [];
        
        if (File::exists(storage_path('logs/laravel.log'))) {
            $logContent = File::get(storage_path('logs/laravel.log'));
            $lines = explode("\n", $logContent);
            
            foreach ($lines as $line) {
                if (empty(trim($line))) continue;
                
                // Tenta extrair informações do log
                if (preg_match('/\[(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})\].*?(INFO|ERROR|WARNING|DEBUG|CRITICAL):\s(.*?)(?:{(.*)}|$)/i', $line, $matches)) {
                    $time = $matches[1] ?? '';
                    $level = strtolower($matches[2] ?? 'info');
                    $message = $matches[3] ?? '';
                    $contextJson = $matches[4] ?? '{}';
                    
                    try {
                        $context = json_decode($contextJson, true) ?? [];
                    } catch (\Exception $e) {
                        $context = ['raw' => $contextJson];
                    }
                    
                    $logs[] = [
                        'time' => $time,
                        'level' => $level,
                        'message' => trim($message),
                        'context' => $context
                    ];
                } else {
                    // Se não conseguir fazer o parse, apenas adicionar a linha bruta
                    $logs[] = [
                        'time' => 'Unknown',
                        'level' => 'info',
                        'message' => trim($line),
                        'context' => []
                    ];
                }
            }
            
            // Limitar para os últimos 20 logs
            $logs = array_slice($logs, -20);
        }
        
        return $logs;
    }
}
