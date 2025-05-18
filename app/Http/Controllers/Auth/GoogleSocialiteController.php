<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class GoogleSocialiteController extends Controller
{
    /**
     * Redireciona o usuário para a autenticação do Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    
    /**
     * Obtém as informações do usuário após autenticação no Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Verificar se o usuário já existe com este email
            $existingUser = User::where('email', $googleUser->getEmail())->first();
            
            if ($existingUser) {
                // Atualiza as informações do Google se o usuário já existir
                $existingUser->google_id = $googleUser->getId();
                $existingUser->avatar = $googleUser->getAvatar();
                $existingUser->save();
                
                Auth::login($existingUser);
            } else {
                // Criar um novo usuário
                $newUser = User::create([
                    'id' => (string) Str::uuid(),
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role' => 20, // Role padrão de usuário comum
                    'password' => Hash::make(Str::random(16)), // Senha aleatória gerada
                ]);
                
                Auth::login($newUser);
            }
            
            // Redireciona com base no role do usuário
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect()->route('home');
            
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Ocorreu um erro durante a autenticação com o Google. Por favor, tente novamente.');
        }
    }
}
