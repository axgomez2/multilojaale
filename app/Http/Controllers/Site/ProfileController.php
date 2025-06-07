<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Construtor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Exibe a página principal do perfil
     */
    public function index()
    {
        $user = Auth::user();
        $addressCount = $user->addresses()->where('is_active', true)->count();
        
        return view('site.profile.index', [
            'user' => $user,
            'addressCount' => $addressCount
        ]);
    }
    
    /**
     * Exibe o formulário para edição de dados pessoais
     */
    public function editPersonalInfo()
    {
        $user = Auth::user();
        
        return view('site.profile.personal-info', [
            'user' => $user
        ]);
    }
    
    /**
     * Atualiza os dados pessoais do usuário
     */
    public function updatePersonalInfo(Request $request)
    {
        $user = Auth::user();
        
        // Regras básicas de validação
        $rules = [
            'name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
        ];
        
        // Validar telefone com regra de unicidade, excetuando o usuário atual
        if ($request->filled('phone')) {
            $rules['phone'] = [
                'string', 
                'max:20',
                function ($attribute, $value, $fail) use ($user) {
                    $exists = \App\Models\User::where('phone', $value)
                                              ->where('id', '!=', $user->id)
                                              ->exists();
                    if ($exists) {
                        $fail('Este telefone já está sendo usado por outro usuário.');
                    }
                }
            ];
        }
        
        // Validar CPF com regra de unicidade, excetuando o usuário atual
        if ($request->filled('cpf')) {
            $rules['cpf'] = [
                'string', 
                'max:14',
                function ($attribute, $value, $fail) use ($user) {
                    // Remove formatação para comparar
                    $cleanCpf = preg_replace('/[^0-9]/', '', $value);
                    
                    // Verifica se outro usuário já usa este CPF
                    $exists = \App\Models\User::where(function($query) use ($value, $cleanCpf) {
                                        $query->where('cpf', $value)
                                              ->orWhere('cpf', $cleanCpf);
                                    })
                                    ->where('id', '!=', $user->id)
                                    ->exists();
                    
                    if ($exists) {
                        $fail('Este CPF já está sendo usado por outro usuário.');
                    }
                }
            ];
        }
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Se passou pela validação, atualiza os dados
        $user->update($validator->validated());
        
        return redirect()->route('site.profile.index')
                         ->with('success', 'Dados pessoais atualizados com sucesso!');
    }
    
    /**
     * Exibe o formulário para alteração de senha
     */
    public function editPassword()
    {
        return view('site.profile.password');
    }
    
    /**
     * Atualiza a senha do usuário
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        
        // Verifica se a senha atual está correta
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors([
                'current_password' => 'A senha atual está incorreta.'
            ]);
        }
        
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        
        return redirect()->route('site.profile.index')
                         ->with('success', 'Senha atualizada com sucesso!');
    }
}
