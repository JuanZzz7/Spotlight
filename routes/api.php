<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShipmentTrackingApiController;

/*
|--------------------------------------------------------------------------
| API Routes - Spotlight Tracking & E-Commerce
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // API Pública de Rastreo en Tiempo Real por número de guía
    Route::get('/tracking/{tracking_number}', [ShipmentTrackingApiController::class, 'trackByNumber'])->name('api.tracking.number');

    // API de Rastreo por Orden (accesible para compradores o comercios)
    Route::get('/orders/{order}/tracking', [ShipmentTrackingApiController::class, 'trackOrder'])->name('api.orders.tracking');

    // API de Simulación de avance en tiempo real (para tests interactivos y demo en vivo)
    Route::post('/shipments/{shipment}/simulate-advance', [ShipmentTrackingApiController::class, 'simulateAdvance'])->name('api.shipments.simulate');
});

// Webhook Oficial Pasarela Wompi Colombia (CSRF Excluido)
Route::post('/payments/wompi/webhook', [\App\Http\Controllers\Api\WompiWebhookController::class, 'handle'])
    ->name('api.payments.wompi.webhook');

