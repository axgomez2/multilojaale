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

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
