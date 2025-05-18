<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\WorkOS\Http\Requests\AuthKitLoginRequest;

class GoogleLoginController extends Controller
{
    /**
     * Redireciona o usuário para a autenticação do Google usando WorkOS
     */
    public function redirectToGoogle(AuthKitLoginRequest $request)
    {
        // Capture a URL de redirecionamento antes de redirecionamento
        // Usamos uma sessão para armazenar temporariamente a URL de WorkOS
        
        session()->flash('redirect_to_workos', true);
        
        // Obtemos o comportamento padrão de redirecionamento sem executá-lo
        $response = $request->redirect();
        
        // Extraimos a URL para qual o usuário seria redirecionado
        $redirectUrl = $response->getTargetUrl();
        
        // Agora renderizamos nossa página intermediária própria com a URL de redirecionamento
        return view('auth.redirect-to-google', [
            'redirectUrl' => $redirectUrl,
        ]);
    }
}
