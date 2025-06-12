<?php

use App\Http\Controllers\Api\MercadoPagoWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Rota pública para receber webhooks do Mercado Pago
Route::post('/mercadopago/webhook', [MercadoPagoWebhookController::class, 'handleWebhook'])
    ->name('api.webhooks.mercadopago');

// Rota para verificar o status do pagamento
Route::get('/payment/check-status/{order}', 'App\Http\Controllers\Api\PaymentStatusController@checkStatus')
    ->name('api.payment.check-status');

// Outras rotas de API podem ser adicionadas aqui
