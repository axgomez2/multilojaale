<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\CategoryController;
use App\Http\Controllers\YouTubeController;

// Rota pública principal (home)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rota para todos os produtos
Route::get('/produtos', [CategoryController::class, 'allProducts'])->name('site.products');

// Rota para produtos por categoria
Route::get('/categoria/{slug}', [CategoryController::class, 'show'])->name('site.category');

// Área administrativa separada - COLOCADA PRIMEIRO PARA TER PRIORIDADE
// Rotas apenas para administradores
Route::middleware([
    'web',  // Adicionando web explicitamente para garantir sessão e CSRF
    'auth',
    'admin',
])->prefix('admin')->group(function () {
    require __DIR__.'/admin.php';
});

// Rotas para usuários autenticados
Route::middleware([
    'auth',
])->group(function () {
    // Mantemos o dashboard para componentes já existentes no sistema
    // que possam usar esta rota
    Route::view('dashboard', 'dashboard')->name('dashboard');
    
    // Rotas do perfil do usuário
    Route::prefix('perfil')->name('site.profile.')->group(function () {
        // Página principal do perfil
        Route::get('/', [\App\Http\Controllers\Site\ProfileController::class, 'index'])->name('index');
        
        // Gerenciamento de dados pessoais
        Route::get('/dados-pessoais', [\App\Http\Controllers\Site\ProfileController::class, 'editPersonalInfo'])->name('personal-info.edit');
        Route::put('/dados-pessoais', [\App\Http\Controllers\Site\ProfileController::class, 'updatePersonalInfo'])->name('personal-info.update');
        
        // Alteração de senha
        Route::get('/alterar-senha', [\App\Http\Controllers\Site\ProfileController::class, 'editPassword'])->name('password.edit');
        Route::put('/alterar-senha', [\App\Http\Controllers\Site\ProfileController::class, 'updatePassword'])->name('password.update');
        
        // Rotas para gerenciamento de endereços
        Route::prefix('enderecos')->name('addresses.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Site\AddressController::class, 'index'])->name('index');
            Route::get('/novo', [\App\Http\Controllers\Site\AddressController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Site\AddressController::class, 'store'])->name('store');
            Route::get('/{id}/editar', [\App\Http\Controllers\Site\AddressController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\Site\AddressController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Site\AddressController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/padrao', [\App\Http\Controllers\Site\AddressController::class, 'setDefault'])->name('set-default');
        });
        
        // Rota para consulta de CEP (pode ser acessada via AJAX)
        Route::post('/consulta-cep', [\App\Http\Controllers\Site\AddressController::class, 'lookupZipcode'])->name('lookup-zipcode');
    });
});

// YouTube API - rota pública para evitar problemas com middlewares
Route::post('/youtube/search', [YouTubeController::class, 'search'])->name('youtube.search');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// IMPORTANTE: Rota para detalhes do disco de vinil - deve ser a ÚTIMA rota
// por ser uma rota coringa que captura qualquer padrão /{param1}/{param2}
Route::get('/{artistSlug}/{titleSlug}', [\App\Http\Controllers\Site\VinylDetailsController::class, 'show'])->name('site.vinyl.show');
