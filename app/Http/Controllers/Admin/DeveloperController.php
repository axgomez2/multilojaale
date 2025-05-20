<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class DeveloperController extends Controller
{
    /**
     * Mostra a página de configurações de identidade visual
     */
    public function showBranding()
    {
        // Verificar permissão de desenvolvedor
        if (!auth()->user()->isDeveloper()) {
            abort(403, "Acesso não autorizado. Apenas desenvolvedores podem acessar esta área.");
        }
        
        // Obter a instância da loja (singleton)
        $store = StoreInformation::getInstance();
        
        return view("admin.developer.branding", [
            "store" => $store
        ]);
    }
    
    /**
     * Salvar configurações de identidade visual
     */
    public function updateBranding(Request $request)
    {
        // Verificar permissão de desenvolvedor
        if (!auth()->user()->isDeveloper()) {
            abort(403, "Acesso não autorizado. Apenas desenvolvedores podem acessar esta área.");
        }
        
        $request->validate([
            "logo" => "nullable|image|mimes:jpeg,png,jpg,svg|max:2048",
            "favicon" => "nullable|image|mimes:ico,png,jpg,svg|max:1024",
        ]);
        
        // Obter instância da loja
        $store = StoreInformation::getInstance();
        
        // Processar o logo
        if ($request->hasFile("logo")) {
            // Remover logo antigo se existir
            if ($store->logo_path && Storage::disk("public")->exists($store->logo_path)) {
                Storage::disk("public")->delete($store->logo_path);
            }
            
            // Processar e salvar o logo com o Intervention Image
            $logo = $request->file("logo");
            $originalExtension = $logo->getClientOriginalExtension();
            
            // Determinar o formato de saída com base na extensão original
            $saveFormat = in_array(strtolower($originalExtension), ['svg']) ? 'svg' : 'png';
            $filename = "logo_" . time() . "." . $saveFormat;
            $path = "store/images/" . $filename;
            
            // Criar instância do ImageManager e processar a imagem
            $manager = new ImageManager(new Driver());
            $image = $manager->read($logo->getRealPath());
            
            // Se for SVG, salvar diretamente
            if ($saveFormat === 'svg') {
                Storage::disk("public")->put($path, file_get_contents($logo->getRealPath()));
            } else {
                // Para outros formatos, redimensionar e salvar como PNG para preservar transparência
                $image = $image->scaleDown(width: 500, height: 200);
                $encodedImage = $image->toPng();
                
                // Salvar no disco
                Storage::disk("public")->put($path, $encodedImage->toString());
            }
            
            $store->logo_path = $path;
        }
        
        // Processar o favicon
        if ($request->hasFile("favicon")) {
            // Remover favicon antigo se existir
            if ($store->favicon_path && Storage::disk("public")->exists($store->favicon_path)) {
                Storage::disk("public")->delete($store->favicon_path);
            }
            
            // Processar e salvar o favicon com o Intervention Image
            $favicon = $request->file("favicon");
            $originalExtension = $favicon->getClientOriginalExtension();
            
            // Determinar o formato de saída com base na extensão original
            $saveFormat = strtolower($originalExtension);
            if ($saveFormat == 'ico') {
                // Se for ICO, manter o formato
                $filename = "favicon_" . time() . ".ico";
                $path = "store/images/" . $filename;
                Storage::disk("public")->put($path, file_get_contents($favicon->getRealPath()));
            } else {
                // Para outros formatos, converter para PNG
                $filename = "favicon_" . time() . ".png";
                $path = "store/images/" . $filename;
                
                // Criar instância do ImageManager e processar a imagem
                $manager = new ImageManager(new Driver());
                $image = $manager->read($favicon->getRealPath());
                $image = $image->scaleDown(width: 64, height: 64);
                $encodedImage = $image->toPng();
                
                // Salvar no disco
                Storage::disk("public")->put($path, $encodedImage->toString());
            }
            
            $store->favicon_path = $path;
        }
        
        $store->save();
        
        return redirect()->route("admin.developer.branding")
            ->with("success", "Identidade visual atualizada com sucesso!");
    }
    
    /**
     * Mostra a página de configurações da loja
     */
    public function showStoreInfo()
    {
        // Verificar permissão de desenvolvedor
        if (!auth()->user()->isDeveloper()) {
            abort(403, "Acesso não autorizado. Apenas desenvolvedores podem acessar esta área.");
        }
        
        // Obter as informações da loja (usando o padrão singleton)
        $store = StoreInformation::getInstance();
        
        return view("admin.developer.store-info", [
            "store" => $store
        ]);
    }
    
    /**
     * Salvar configurações da loja
     */
    public function updateStoreInfo(Request $request)
    {
        // Verificar permissão de desenvolvedor
        if (!auth()->user()->isDeveloper()) {
            abort(403, "Acesso não autorizado. Apenas desenvolvedores podem acessar esta área.");
        }
        
        $request->validate([
            "name" => "required|string|max:255",
            "description" => "nullable|string|max:1000",
            "address" => "nullable|string|max:255",
            "zipcode" => "nullable|string|max:10",
            "neighborhood" => "nullable|string|max:100",
            "state" => "nullable|string|size:2",
            "phone" => "nullable|string|max:20",
            "email" => "nullable|email|max:255",
            "document_type" => ["required", Rule::in(["cpf", "cnpj"])],
            "document" => "nullable|string|max:20",
            "logo" => "nullable|image|mimes:jpeg,png,jpg,svg|max:2048",
            "favicon" => "nullable|image|mimes:ico,png,jpg,svg|max:1024",
        ]);
        
        // Obter instância da loja
        $store = StoreInformation::getInstance();
        
        // Atualizar campos básicos
        $store->name = $request->name;
        $store->description = $request->description;
        $store->document_type = $request->document_type;
        $store->document = $request->document;
        $store->address = $request->address;
        $store->zipcode = $request->zipcode;
        $store->neighborhood = $request->neighborhood;
        $store->state = $request->state;
        $store->phone = $request->phone;
        $store->email = $request->email;
        
        // Processar o logo
        if ($request->hasFile("logo")) {
            // Remover logo antigo se existir
            if ($store->logo_path && Storage::disk("public")->exists($store->logo_path)) {
                Storage::disk("public")->delete($store->logo_path);
            }
            
            // Processar e salvar o logo com o Intervention Image
            $logo = $request->file("logo");
            $originalExtension = $logo->getClientOriginalExtension();
            
            // Determinar o formato de saída com base na extensão original
            $saveFormat = in_array(strtolower($originalExtension), ['svg']) ? 'svg' : 'png';
            $filename = "logo_" . time() . "." . $saveFormat;
            $path = "store/images/" . $filename;
            
            // Criar instância do ImageManager e processar a imagem
            $manager = new ImageManager(new Driver());
            $image = $manager->read($logo->getRealPath());
            
            // Se for SVG, salvar diretamente
            if ($saveFormat === 'svg') {
                Storage::disk("public")->put($path, file_get_contents($logo->getRealPath()));
            } else {
                // Para outros formatos, redimensionar e salvar como PNG para preservar transparência
                $image = $image->scaleDown(width: 500, height: 200);
                $encodedImage = $image->toPng();
                
                // Salvar no disco
                Storage::disk("public")->put($path, $encodedImage->toString());
            }
            
            $store->logo_path = $path;
        }
        
        // Processar o favicon
        if ($request->hasFile("favicon")) {
            // Remover favicon antigo se existir
            if ($store->favicon_path && Storage::disk("public")->exists($store->favicon_path)) {
                Storage::disk("public")->delete($store->favicon_path);
            }
            
            // Processar e salvar o favicon com o Intervention Image
            $favicon = $request->file("favicon");
            $originalExtension = $favicon->getClientOriginalExtension();
            
            // Determinar o formato de saída com base na extensão original
            $saveFormat = strtolower($originalExtension);
            if ($saveFormat == 'ico') {
                // Se for ICO, manter o formato
                $filename = "favicon_" . time() . ".ico";
                $path = "store/images/" . $filename;
                Storage::disk("public")->put($path, file_get_contents($favicon->getRealPath()));
            } else {
                // Para outros formatos, converter para PNG
                $filename = "favicon_" . time() . ".png";
                $path = "store/images/" . $filename;
                
                // Criar instância do ImageManager e processar a imagem
                $manager = new ImageManager(new Driver());
                $image = $manager->read($favicon->getRealPath());
                $image = $image->scaleDown(width: 64, height: 64);
                $encodedImage = $image->toPng();
                
                // Salvar no disco
                Storage::disk("public")->put($path, $encodedImage->toString());
            }
            
            $store->favicon_path = $path;
        }
        
        $store->save();
        
        return redirect()->route("admin.developer.store")
            ->with("success", "Informações da loja atualizadas com sucesso!");
    }
}
