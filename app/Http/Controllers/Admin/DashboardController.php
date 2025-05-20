<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreInformation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Exibe o dashboard administrativo com métricas e informações relevantes
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Verificar permissão de administrador (redundante com middleware, mas para segurança adicional)
        if (!auth()->user()->isAdmin()) {
            abort(403, "Acesso não autorizado. Apenas administradores podem acessar esta área.");
        }
        
        // Obter informações do usuário e da loja
        $user = auth()->user();
        $store = StoreInformation::getInstance();
        
        // Obter contagens de usuários por tipo
        $userCounts = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'developer' => User::where('role', 'developer')->count(),
            'user' => User::where('role', 'user')->count(),
        ];
        
        // Obter informações do sistema 
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido',
        ];
        
        // Retorna a view com os dados
        return view('admin.dashboard', [
            'user' => $user,
            'store' => $store,
            'userCounts' => $userCounts,
            'systemInfo' => $systemInfo,
        ]);
    }
}
