<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\CategoryController;

// Rota pública principal (home)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rota para todos os produtos
Route::get('/produtos', [CategoryController::class, 'allProducts'])->name('site.products');

// Rota para produtos por categoria
Route::get('/categoria/{slug}', [CategoryController::class, 'show'])->name('site.category');

// Rota para detalhes do disco de vinil
Route::get('/{artistSlug}/{titleSlug}', [\App\Http\Controllers\Site\VinylDetailsController::class, 'show'])->name('site.vinyl.show');

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

// Rota de debug para verificar o acesso à área admin
Route::get('/teste-admin', function() {
    return 'Teste de acesso - você pode ver esta mensagem!';
});

// Área administrativa separada
// Rotas apenas para administradores
Route::middleware([
    'auth',
    'admin',
])->prefix('admin')->group(function () {
    // Rota básica de teste para admin sem chamar o arquivo externo
    Route::get('/teste', function() {
        return 'Se você pode ver esta mensagem, você tem acesso admin!';
    });
    
    require __DIR__.'/admin.php';
});

// YouTube API - rota pública para evitar problemas com middlewares
use App\Http\Controllers\YouTubeController;
Route::post('/youtube/search', [YouTubeController::class, 'search'])->name('youtube.search');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
