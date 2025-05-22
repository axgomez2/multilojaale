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
        if (!Auth::check()) {
            // O usuário não está autenticado
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
            return redirect()->route('dashboard')->with('error', 'Você não tem permissão para acessar esta área. Role: ' . Auth::user()->role);
        }

        return $next($request);
    }
}
