<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\SimpleCartController;

// Rotas de debug para testes
Route::prefix('debug')->group(function () {
    // Rota para testar a adição ao carrinho de forma simplificada
    Route::post('/cart/add', [SimpleCartController::class, 'addToCart'])->name('debug.cart.add')->middleware('auth');
});
