<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DeveloperController extends Controller
{
    /**
     * Mostra a página de configurações de identidade visual
     */
    public function showBranding()
    {
        // Verificar permissão de desenvolvedor
        if (!auth()->user()->isDeveloper()) {
            abort(403, 'Acesso não autorizado. Apenas desenvolvedores podem acessar esta área.');
        }
        
        $logo = SiteSetting::get('site_logo');
        $favicon = SiteSetting::get('site_favicon');
        
        return view('admin.developer.branding', [
            'logo' => $logo,
            'favicon' => $favicon
        ]);
    }
    
    /**
     * Salvar configurações de identidade visual
     */
    public function updateBranding(Request $request)
    {
        // Verificar permissão de desenvolvedor
        if (!auth()->user()->isDeveloper()) {
            abort(403, 'Acesso não autorizado. Apenas desenvolvedores podem acessar esta área.');
        }
        
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg,svg|max:1024',
        ]);
        
        // Processar o upload do logo
        if ($request->hasFile('logo')) {
            // Remover logo antigo se existir
            $oldLogo = SiteSetting::get('site_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            
            // Salvar novo logo
            $logoPath = $request->file('logo')->store('site/logo', 'public');
            SiteSetting::set('site_logo', $logoPath, 'branding', 'Caminho para o logotipo do site');
        }
        
        // Processar o upload do favicon
        if ($request->hasFile('favicon')) {
            // Remover favicon antigo se existir
            $oldFavicon = SiteSetting::get('site_favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }
            
            // Salvar novo favicon
            $faviconPath = $request->file('favicon')->store('site/favicon', 'public');
            SiteSetting::set('site_favicon', $faviconPath, 'branding', 'Caminho para o favicon do site');
        }
        
        return redirect()->route('admin.developer.branding')
            ->with('success', 'Identidade visual atualizada com sucesso!');
    }
    
    /**
     * Mostra a página de configurações da loja
     */
    public function showStoreInfo()
    {
        // Verificar permissão de desenvolvedor
        if (!auth()->user()->isDeveloper()) {
            abort(403, 'Acesso não autorizado. Apenas desenvolvedores podem acessar esta área.');
        }
        
        // Recuperar as configurações existentes
        $settings = [
            'store_name' => SiteSetting::get('store_name'),
            'store_description' => SiteSetting::get('store_description'),
            'store_address' => SiteSetting::get('store_address'),
            'store_zipcode' => SiteSetting::get('store_zipcode'),
            'store_neighborhood' => SiteSetting::get('store_neighborhood'),
            'store_state' => SiteSetting::get('store_state'),
            'store_phone' => SiteSetting::get('store_phone'),
            'store_email' => SiteSetting::get('store_email'),
            'store_document_type' => SiteSetting::get('store_document_type', 'cnpj'),
            'store_document' => SiteSetting::get('store_document'),
        ];
        
        return view('admin.developer.store-info', [
            'settings' => $settings
        ]);
    }
    
    /**
     * Salvar configurações da loja
     */
    public function updateStoreInfo(Request $request)
    {
        // Verificar permissão de desenvolvedor
        if (!auth()->user()->isDeveloper()) {
            abort(403, 'Acesso não autorizado. Apenas desenvolvedores podem acessar esta área.');
        }
        
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_description' => 'nullable|string|max:1000',
            'store_address' => 'nullable|string|max:255',
            'store_zipcode' => 'nullable|string|max:10',
            'store_neighborhood' => 'nullable|string|max:100',
            'store_state' => 'nullable|string|size:2',
            'store_phone' => 'nullable|string|max:20',
            'store_email' => 'nullable|email|max:255',
            'store_document_type' => ['required', Rule::in(['cpf', 'cnpj'])],
            'store_document' => 'nullable|string|max:20',
        ]);
        
        // Salvar todas as configurações
        $fields = [
            'store_name',
            'store_description',
            'store_address',
            'store_zipcode',
            'store_neighborhood',
            'store_state',
            'store_phone',
            'store_email',
            'store_document_type',
            'store_document',
        ];
        
        foreach ($fields as $field) {
            SiteSetting::set($field, $request->input($field), 'store_info', 'Informações da loja');
        }
        
        return redirect()->route('admin.developer.store')
            ->with('success', 'Informações da loja atualizadas com sucesso!');
    }
}
