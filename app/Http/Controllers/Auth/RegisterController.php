<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Mostra o formulário de registro.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Registra um novo usuário.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users'],
            'cpf' => ['nullable', 'string', 'max:14', 'unique:users'],
            'birth_date' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'id' => (string) Str::uuid(), // Gera um UUID
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 20, // Usuário padrão
            'phone' => $request->phone,
            'cpf' => $request->cpf,
            'birth_date' => $request->birth_date,
        ]);

        Auth::login($user);
        
        // Enviar email de verificação
        $user->sendEmailVerificationNotification();
        
        // Verificação explícita da role para evitar dependências de métodos
        if ($user->role == 66 || $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        // Redirecionar para a página de verificação de email
        return redirect()->route('verification.notice');
    }
}
