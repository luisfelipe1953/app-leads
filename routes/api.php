<?php

use App\Http\Controllers\LeadController;
use App\Http\Middleware\ApiKeyMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('leads')->group(function () {
    Route::post('/webhook', [LeadController::class, 'webhook']);

    Route::middleware([ApiKeyMiddleware::class, 'throttle:60,1'])->group(function () {
        Route::get('/stats', [LeadController::class, 'stats']);
        Route::post('/ai/summary', [LeadController::class, 'aiSummary']);

        Route::get('/', [LeadController::class, 'index']);
        Route::post('/', [LeadController::class, 'store']);
        Route::get('/{id}', [LeadController::class, 'show']);
        Route::patch('/{id}', [LeadController::class, 'update']);
        Route::delete('/{id}', [LeadController::class, 'destroy']);
    });
});
