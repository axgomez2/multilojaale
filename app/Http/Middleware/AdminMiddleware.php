<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Adicionando log detalhado para debug
        \Log::info('Admin Middleware acionado', [
            'uri' => $request->getRequestUri(),
            'method' => $request->method(),
            'authenticated' => Auth::check(),
            'session' => session()->all()
        ]);
        
        // Para ambiente de produção, podemos temporariamente desativar a verificação de admin
        // para diagnóstico - TEMPORARIAMENTE PARA TESTAR
        if (env('APP_ENV') === 'production') {
            \Log::warning('Bypass de admin ativado temporariamente para diagnóstico');
            return $next($request);
        }
        
        if (!Auth::check()) {
            // O usuário não está autenticado
            \Log::warning('Tentativa de acesso admin sem autenticação');
            return redirect()->route('login');
        }
        
        // Vamos logar informações do usuário para debug
        \Log::info('Tentativa de acesso admin', [
            'user_id' => Auth::id(),
            'email' => Auth::user()->email,
            'role' => Auth::user()->role,
            'is_admin' => Auth::user()->isAdmin()
        ]);
        
        if (!Auth::user()->isAdmin()) {
            // O usuário está autenticado mas não é admin
            \Log::warning('Acesso negado - usuário não é admin');
            return redirect()->route('dashboard')->with('error', 'Você não tem permissão para acessar esta área. Role: ' . Auth::user()->role);
        }

        \Log::info('Acesso admin permitido');
        return $next($request);
    }
}
