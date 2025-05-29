<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentSettingsController extends Controller
{
    /**
     * Exibir a lista de gateways de pagamento disponíveis.
     */
    public function index()
    {
        $gateways = \App\Models\PaymentGateway::all();
        
        return view('admin.payment.index', compact('gateways'));
    }
    
    /**
     * Exibir o formulário para editar um gateway de pagamento.
     */
    public function edit($id)
    {
        $gateway = \App\Models\PaymentGateway::findOrFail($id);
        
        return view('admin.payment.edit', compact('gateway'));
    }
    
    /**
     * Atualizar as configurações de um gateway de pagamento.
     */
    public function update(Request $request, $id)
    {
        $gateway = \App\Models\PaymentGateway::findOrFail($id);
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'active' => 'boolean',
            'sandbox_mode' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Se este gateway está sendo ativado, desativar todos os outros
        if ($request->input('active')) {
            \App\Models\PaymentGateway::where('id', '!=', $id)
                ->update(['active' => false]);
        }
        
        // Obter as credenciais atuais para manter os campos não preenchidos
        $currentCredentials = $gateway->credentials ?? [];
        $newCredentials = $this->buildCredentials($request, $gateway->code);
        
        // Mesclar as credenciais atuais com as novas
        $credentials = array_merge($currentCredentials, $newCredentials);
        
        // Atualizar o gateway diretamente para garantir que a criptografia funcione
        $gateway->update([
            'name' => $request->input('name'),
            'active' => $request->input('active', false),
            'sandbox_mode' => $request->input('sandbox_mode', false),
            'credentials' => $credentials,
            'settings' => array_merge($gateway->settings ?? [], $request->input('settings', []))
        ]);
        
        return redirect()->route('admin.payment.index')
            ->with('success', 'Gateway de pagamento atualizado com sucesso');
    }
    
    /**
     * Construir o array de credenciais com base no código do gateway.
     */
    private function buildCredentials(Request $request, string $code)
    {
        $credentials = [];
        
        switch ($code) {
            case 'mercadopago':
                if ($request->filled('mp_public_key')) {
                    $credentials['public_key'] = $request->input('mp_public_key');
                }
                if ($request->filled('mp_access_token')) {
                    $credentials['access_token'] = $request->input('mp_access_token');
                }
                break;
                
            case 'pagseguro':
                if ($request->filled('ps_email')) {
                    $credentials['email'] = $request->input('ps_email');
                }
                if ($request->filled('ps_token')) {
                    $credentials['token'] = $request->input('ps_token');
                }
                break;
                
            case 'rede':
                if ($request->filled('rede_pv')) {
                    $credentials['pv'] = $request->input('rede_pv');
                }
                if ($request->filled('rede_token')) {
                    $credentials['token'] = $request->input('rede_token');
                }
                break;
        }
        
        return $credentials;
    }
}
