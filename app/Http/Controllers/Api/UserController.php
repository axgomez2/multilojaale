<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Atualiza apenas o CPF do usuário autenticado
     * Usado no modal da página de checkout
     */
    public function updateCpf(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Validação básica
            if (!$request->filled('cpf')) {
                return response()->json([
                    'success' => false,
                    'message' => 'CPF é obrigatório'
                ], 422);
            }
            
            $cpf = $request->input('cpf');
            
            // Remove formatação para comparar
            $cleanCpf = preg_replace('/[^0-9]/', '', $cpf);
            
            // Verifica comprimento
            if (strlen($cleanCpf) !== 11) {
                return response()->json([
                    'success' => false,
                    'message' => 'CPF inválido. Deve conter 11 dígitos.'
                ], 422);
            }
            
            // Verifica se outro usuário já usa este CPF
            $exists = User::where(function($query) use ($cpf, $cleanCpf) {
                        $query->where('cpf', $cpf)
                              ->orWhere('cpf', $cleanCpf);
                    })
                    ->where('id', '!=', $user->id)
                    ->exists();
            
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este CPF já está sendo usado por outro usuário.'
                ], 422);
            }
            
            // Atualiza o CPF do usuário
            $user->cpf = $cpf;
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => 'CPF atualizado com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar CPF do usuário: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao atualizar o CPF. Por favor, tente novamente.'
            ], 500);
        }
    }
}
