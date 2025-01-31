<?php

use App\Http\Controllers\TravelOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::group(['prefix' => 'v1'], static function () {

    Route::post('register', [AuthController::class, 'register'])->name('api.v1.register');
    Route::post('login', [AuthController::class, 'login'])->name('api.v1.login');
    Route::middleware('auth:api')->group(function () {
        Route::group(['prefix' => 'travel-orders'], static function () {
            Route::post('/', [TravelOrderController::class, 'store'])->name('api.v1.travel_orders.store');
            Route::get('/{travelOrder}', [TravelOrderController::class, 'show'])->name('api.v1.travel_orders.show');
            Route::get('/', [TravelOrderController::class, 'getTravelOrders'])->name('api.v1.travel_orders.get-all');
            Route::put('/approve/{travelOrder}', [TravelOrderController::class, 'approve'])->name('api.v1.travel_orders.approve');
            Route::put('/cancel/{travelOrder}', [TravelOrderController::class, 'cancel'])->name('api.v1.travel_orders.cancel');
        });
        
    });
});