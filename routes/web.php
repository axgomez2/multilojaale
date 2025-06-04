<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\CategoryController;
use App\Http\Controllers\YouTubeController;

// Rota pública principal (home)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rotas de verificação de email
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/perfil')->with('status', 'Email verificado com sucesso!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'Link de verificação enviado!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Rota para todos os produtos
Route::get('/discos', [CategoryController::class, 'allProducts'])->name('site.products');

// Rota para produtos por categoria
Route::get('/categoria/{slug}', [CategoryController::class, 'show'])->name('site.category');

// Rota para produtos por artista
Route::get('/artista/{slug}', [CategoryController::class, 'byArtist'])->name('site.artist');

// Rota para produtos por gravadora
Route::get('/gravadora/{slug}', [CategoryController::class, 'byLabel'])->name('site.label');

// Rotas do Melhor Envio (Controller unificado)
Route::prefix('melhorenvio')->group(function () {
    // Página de demonstração
    Route::get('/', [\App\Http\Controllers\MelhorEnvioController::class, 'index'])->name('melhorenvio.index');
    
    // Cálculo de frete
    Route::post('/calcular', [\App\Http\Controllers\MelhorEnvioController::class, 'calculateShipping'])->name('melhorenvio.calculate');
    
    // Teste da API para debug
    Route::get('/teste', [\App\Http\Controllers\MelhorEnvioController::class, 'testApi'])->name('melhorenvio.test');
    
    // Iniciar o fluxo de autorização
    Route::get('/authorize', [\App\Http\Controllers\MelhorEnvioController::class, 'redirect'])->name('melhorenvio.authorize');
    
    // Callback para receber o código de autorização
    Route::get('/callback', [\App\Http\Controllers\MelhorEnvioController::class, 'callback'])->name('melhorenvio.callback');
});

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
    // A rota dashboard foi removida por não ser utilizada no projeto
    
    // Rotas do perfil do usuário
    Route::prefix('perfil')->name('site.profile.')->middleware('verified')->group(function () {
        // Página principal do perfil
        Route::get('/', [\App\Http\Controllers\Site\ProfileController::class, 'index'])->name('index');
        
        // Gerenciamento de dados pessoais
        Route::get('/dados-pessoais', [\App\Http\Controllers\Site\ProfileController::class, 'editPersonalInfo'])->name('personal-info.edit');
        Route::put('/dados-pessoais', [\App\Http\Controllers\Site\ProfileController::class, 'updatePersonalInfo'])->name('personal-info.update');
        
        // Alteração de senha
        Route::get('/alterar-senha', [\App\Http\Controllers\Site\ProfileController::class, 'editPassword'])->name('password.edit');
        Route::put('/alterar-senha', [\App\Http\Controllers\Site\ProfileController::class, 'updatePassword'])->name('password.update');
        
        // Gerenciamento do perfil
        Route::get('/detalhes', [\App\Http\Controllers\Site\ProfileController::class, 'show'])->name('show');
        Route::get('/editar', [\App\Http\Controllers\Site\ProfileController::class, 'edit'])->name('edit');
        Route::put('/atualizar', [\App\Http\Controllers\Site\ProfileController::class, 'update'])->name('update');
        
        // Gerenciamento de endereços
        Route::get('enderecos', [\App\Http\Controllers\Site\AddressController::class, 'index'])->name('addresses.index');
        Route::get('enderecos/criar', [\App\Http\Controllers\Site\AddressController::class, 'create'])->name('addresses.create');
        Route::post('enderecos', [\App\Http\Controllers\Site\AddressController::class, 'store'])->name('addresses.store');
        Route::get('enderecos/{address}', [\App\Http\Controllers\Site\AddressController::class, 'edit'])->name('addresses.edit');
        Route::put('enderecos/{address}', [\App\Http\Controllers\Site\AddressController::class, 'update'])->name('addresses.update');
        Route::delete('enderecos/{address}', [\App\Http\Controllers\Site\AddressController::class, 'destroy'])->name('addresses.destroy');
        Route::post('enderecos/default/{address}', [\App\Http\Controllers\Site\AddressController::class, 'setDefault'])->name('addresses.set-default');
        
        // Rotas de pedidos do cliente
        Route::get('pedidos', [\App\Http\Controllers\Site\OrdersController::class, 'index'])->name('orders.index');
        Route::get('pedidos/{orderNumber}', [\App\Http\Controllers\Site\OrdersController::class, 'show'])->name('orders.show');
        
        // Rota para consulta de CEP (pode ser acessada via AJAX)
        Route::post('/consulta-cep', [\App\Http\Controllers\Site\AddressController::class, 'lookupZipcode'])->name('lookup-zipcode');
        
        // Rota para adicionar endereço via modal AJAX
        Route::post('/endereco-modal', [\App\Http\Controllers\Site\AddressController::class, 'storeModal'])->name('address-modal.store');
    });
    

});

// YouTube API - rota pública para evitar problemas com middlewares
Route::post('/youtube/search', [YouTubeController::class, 'search'])->name('youtube.search');

// Rotas para Wishlist (Lista de Desejos) usando Livewire
Route::prefix('wishlist')->name('site.wishlist.')->middleware(['auth', 'verified'])->group(function () {
    // Página principal - usando componente Livewire diretamente
    Route::get('/', function() {
        return view('livewire.pages.wishlist');
    })->name('index');
    
    // Manter as rotas de API para compatibilidade com sistemas externos
    Route::post('/check', [\App\Http\Controllers\Site\WishlistController::class, 'check'])->name('check');
    Route::post('/toggle/{id}', [\App\Http\Controllers\Site\WishlistController::class, 'toggle'])->name('toggle');
});

// Rotas para Wantlist (Lista de Interesse) usando Livewire
Route::prefix('wantlist')->name('site.wantlist.')->middleware(['auth', 'verified'])->group(function () {
    // Página principal - usando componente Livewire diretamente
    Route::get('/', function() {
        return view('livewire.pages.wantlist');
    })->name('index');
    
    // Manter as rotas de API para compatibilidade com sistemas externos
    Route::post('/check', [\App\Http\Controllers\Site\WantlistController::class, 'check'])->name('check');
    Route::post('/toggle/{id}', [\App\Http\Controllers\Site\WantlistController::class, 'toggle'])->name('toggle');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/cart.php';
require __DIR__.'/checkout.php';

// IMPORTANTE: Rota para detalhes do disco de vinil - deve ser a ÚTIMA rota
// por ser uma rota coringa que captura qualquer padrão /{param1}/{param2}
Route::get('/disco/{artistSlug}/{titleSlug}', [\App\Http\Controllers\Site\VinylDetailsController::class, 'show'])->name('site.vinyl.show');
