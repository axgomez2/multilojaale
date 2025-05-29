<?php

use App\Http\Controllers\Site\CheckoutAddressController;
use App\Http\Controllers\Site\CheckoutController;
use App\Http\Controllers\Site\CheckoutPaymentController;
use App\Http\Controllers\Site\NewCheckoutController;
use App\Http\Controllers\Site\OrderController;
use Illuminate\Support\Facades\Route;

// Rotas de Checkout
Route::middleware(['web'])->group(function () {
    // Checkout principal
    
    Route::get('/checkout/confirmation/{orderId}', [CheckoutController::class, 'confirmation'])->name('site.checkout.confirmation');
    
    // Endereços no Checkout
    
    Route::post('/checkout/addresses', [CheckoutAddressController::class, 'store'])->name('site.checkout.addresses.store');
  
    Route::get('/checkout/addresses/lookup-zipcode', [CheckoutAddressController::class, 'lookupZipcode'])->name('site.checkout.addresses.lookup-zipcode');
    
    // Pagamentos no Checkout
    
    
    // Pedidos
    Route::post('/orders', [OrderController::class, 'store'])->name('site.orders.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('site.account.orders');
    Route::get('/orders/{orderId}', [OrderController::class, 'show'])->name('site.account.order');
    Route::post('/orders/{orderId}/cancel', [OrderController::class, 'cancel'])->name('site.orders.cancel');
    
    // Novo Checkout - Fluxo Simplificado
    Route::get('/checkout', [NewCheckoutController::class, 'index'])->name('site.checkout.index');
    Route::get('/newcheckout', [NewCheckoutController::class, 'index'])->name('site.newcheckout.index'); // Mantém compatibilidade
    
    Route::post('/checkout/process-payment', [NewCheckoutController::class, 'processPayment'])->name('site.checkout.process-payment');
    Route::post('/newcheckout/process-payment', [NewCheckoutController::class, 'processPayment'])->name('site.newcheckout.process-payment'); // Mantém compatibilidade
    Route::get('/checkout/success/{order_number}', [NewCheckoutController::class, 'success'])->name('site.checkout.success');
    Route::post('/checkout/payment/webhook', [CheckoutPaymentController::class, 'webhook'])->name('site.checkout.payment.webhook');
});
