<?php

use App\Http\Controllers\Site\CheckoutAddressController;
use App\Http\Controllers\Site\CheckoutPaymentController;
use App\Http\Controllers\Site\MercadoPagoController;
use App\Http\Controllers\Site\PixPaymentController;
// use App\Http\Controllers\Site\NewCheckoutController;
use App\Http\Controllers\Site\OrderController;
use App\Http\Controllers\Site\NewShippingController;
use Illuminate\Support\Facades\Route;

// Rotas para o novo fluxo simplificado: carrinho → shipping → payment
Route::middleware(['web'])->group(function () {
    // Endereços para checkout
    Route::prefix('checkout/addresses')->name('site.checkout.addresses.')->group(function () {
        Route::post('/', [CheckoutAddressController::class, 'store'])->name('store');
        Route::get('/', [CheckoutAddressController::class, 'index'])->name('index');
        Route::get('/{address}', [CheckoutAddressController::class, 'show'])->name('show');
        Route::put('/{address}', [CheckoutAddressController::class, 'update'])->name('update');
        Route::delete('/{address}', [CheckoutAddressController::class, 'destroy'])->name('destroy');
    });
    
    // Shipping - Etapa após o carrinho
    // Route::get('/shipping', [NewShippingController::class, 'index'])->name('site.shipping.index');
    // Route::post('/shipping/calculate', [NewShippingController::class, 'calculateShipping'])->name('site.shipping.calculate');
    // Route::post('/shipping/select', [NewShippingController::class, 'selectShipping'])->name('site.shipping.select');
    // Route::post('/shipping/checkout', [NewShippingController::class, 'proceedToCheckout'])->name('site.shipping.checkout');
    
    // Payment - Etapa final
    Route::post('/checkout/create-order', [CheckoutPaymentController::class, 'createOrder'])->name('site.checkout.create-order');
    Route::get('/payment/{order}', [CheckoutPaymentController::class, 'show'])->name('site.checkout.payment');
    Route::post('/payment/process/{order_id}', [CheckoutPaymentController::class, 'process'])->name('site.payment.process');
    
    // Mercado Pago
    Route::prefix('payment')->name('site.mercadopago.')->group(function () {
        Route::post('/preference/{order}', [MercadoPagoController::class, 'createPreference'])->name('create-preference');
        Route::post('/process/{order}', [CheckoutPaymentController::class, 'process'])->name('process');
        Route::get('/success/{order}', [MercadoPagoController::class, 'success'])->name('success');
        Route::get('/failure/{order}', [MercadoPagoController::class, 'failure'])->name('failure');
        Route::get('/pending/{order}', [MercadoPagoController::class, 'pending'])->name('pending');
        Route::get('/check-status/{order}', [MercadoPagoController::class, 'checkStatus'])->name('check-status');
    });
    
    // PIX Payment Page
    Route::get('/payment/pix/{order}', [PixPaymentController::class, 'show'])->name('site.pix.show');
    
    // Webhook do Mercado Pago
    Route::post('/api/mercadopago/webhook', [MercadoPagoController::class, 'processWebhook'])->name('site.mercadopago.webhook');
    
    // Página de sucesso após pagamento
    // Route::get('/checkout/success/{order_number}', [NewCheckoutController::class, 'success'])->name('site.checkout.success');
    
    // Webhooks para pagamentos
    Route::post('/webhooks/mercadopago', [CheckoutPaymentController::class, 'mercadoPagoWebhook'])
        ->name('webhook.mercadopago')
        ->withoutMiddleware('\App\Http\Middleware\VerifyCsrfToken');
    Route::post('/checkout/payment/webhook', [CheckoutPaymentController::class, 'webhook'])->name('site.checkout.payment.webhook');
    
    // Pedidos
    Route::post('/orders', [OrderController::class, 'store'])->name('site.orders.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('site.account.orders');
    Route::get('/orders/{orderId}', [OrderController::class, 'show'])->name('site.account.order');
    Route::post('/orders/{orderId}/cancel', [OrderController::class, 'cancel'])->name('site.orders.cancel');
    
    // Rotas legadas mantidas temporariamente para compatibilidade
    // Route::get('/checkout/payment/{order_id}', [NewCheckoutController::class, 'payment'])->name('site.checkout.payment');
    // Route::post('/checkout/process-payment', [NewCheckoutController::class, 'processPayment'])->name('site.checkout.process-payment');
    // Route::get('/checkout', [NewCheckoutController::class, 'index'])->name('site.checkout.index');
});
