<?php

use Illuminate\Support\Facades\Route;

// Rota pública principal (home)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rotas para usuários autenticados
Route::middleware([
    'auth',
])->group(function () {
    // Mantemos o dashboard para componentes já existentes no sistema
    // que possam usar esta rota
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

// Área administrativa separada
// Rotas apenas para administradores
Route::middleware([
    'auth',
    'admin',
])->prefix('admin')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('admin.dashboard');
    // Futuras rotas administrativas irão aqui
});

// Área do desenvolvedor
// Rotas apenas para desenvolvedores
Route::middleware([
    'auth',
    'admin',
    'developer',
])->prefix('admin/developer')->group(function () {
    // Identidade Visual (Logo e Favicon)
    Route::get('/branding', [\App\Http\Controllers\Admin\DeveloperController::class, 'showBranding'])->name('admin.developer.branding');
    Route::post('/branding', [\App\Http\Controllers\Admin\DeveloperController::class, 'updateBranding'])->name('admin.developer.branding.update');
    
    // Informações da Loja
    Route::get('/store', [\App\Http\Controllers\Admin\DeveloperController::class, 'showStoreInfo'])->name('admin.developer.store');
    Route::post('/store', [\App\Http\Controllers\Admin\DeveloperController::class, 'updateStoreInfo'])->name('admin.developer.store.update');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
