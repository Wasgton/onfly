<?php

use App\Http\Controllers\TravelOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::group(['prefix' => 'v1'], static function () {

    Route::post('register', [AuthController::class, 'register'])->name('api.v1.register');
    Route::post('login', [AuthController::class, 'login'])->name('api.v1.login');
    Route::middleware('auth:api')->group(function () {
        Route::post('/travel-orders', [TravelOrderController::class, 'store'])->name('api.v1.travel_orders.store');
        Route::get('/travel-orders/{travelOrder}', [TravelOrderController::class, 'show'])->name('api.v1.travel_orders.show');
    });
});