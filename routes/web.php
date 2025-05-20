<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\HomeController;

// Rota pública principal (home)
Route::get('/', [HomeController::class, 'index'])->name('home');

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
    require __DIR__.'/admin.php';
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
