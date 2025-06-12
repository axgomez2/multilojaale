<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\NewShippingController;

// Rotas para Frete e Entrega
Route::prefix('frete')->name('site.shipping.')->middleware(['auth', 'verified'])->group(function () {
    // Página principal do frete com resumo do pedido
    Route::get('/', [NewShippingController::class, 'index'])->name('index');
    
    // Salvar dados básicos do usuário
    Route::post('/dados-usuario', [NewShippingController::class, 'saveUserData'])->name('save-user-data');
    
    // Salvar endereço
    Route::post('/endereco', [NewShippingController::class, 'saveAddress'])->name('save-address');
    
    // Selecionar endereço
    Route::post('/selecionar-endereco', [NewShippingController::class, 'selectAddress'])->name('select-address');
    
    // Selecionar opção de frete
    Route::post('/selecionar-frete', [NewShippingController::class, 'selectShipping'])->name('select-shipping');
});
