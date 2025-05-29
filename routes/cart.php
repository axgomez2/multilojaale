<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\CartController;

// Rotas para Carrinho de Compras
Route::prefix('carrinho')->name('site.cart.')->middleware(['auth', 'verified'])->group(function () {
    // Página principal do carrinho
    Route::get('/', [CartController::class, 'index'])->name('index');
    
    // Adicionar item ao carrinho
    Route::post('/adicionar', [CartController::class, 'store'])->name('add');
    
    // Atualizar quantidade de um item no carrinho
    Route::put('/atualizar/{id}', [CartController::class, 'update'])->name('update');
    
    // Remover item do carrinho
    Route::delete('/remover/{id}', [CartController::class, 'destroy'])->name('remove');
    
    // Limpar o carrinho completamente
    Route::delete('/limpar', [CartController::class, 'clear'])->name('clear');
    
    // Adicionar todos os itens da wishlist ao carrinho
    Route::post('/adicionar-da-wishlist', [CartController::class, 'addFromWishlist'])->name('add-from-wishlist');
    
    // Rotas para funcionalidade "Salvar para depois"
    Route::get('/salvos', [CartController::class, 'savedItems'])->name('saved-items');
    Route::put('/salvar-para-depois/{id}', [CartController::class, 'saveForLater'])->name('save-for-later');
    Route::put('/mover-para-carrinho/{id}', [CartController::class, 'moveToCart'])->name('move-to-cart');
    
    // Rotas para cálculo de frete
    Route::post('/calcular-frete', [CartController::class, 'calculateShipping'])->name('calculate-shipping');
    Route::get('/opcoes-frete', [CartController::class, 'getShippingOptions'])->name('shipping-options');
    Route::post('/selecionar-frete', [CartController::class, 'selectShipping'])->name('select-shipping');
    
    // Rota para aplicação de cupom de desconto
    Route::post('/aplicar-cupom', [CartController::class, 'applyCoupon'])->name('apply-coupon');
});
