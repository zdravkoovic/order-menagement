<?php

use App\API\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::apiResource('orders', OrderController::class);
Route::post('orders/{id}/item', [OrderController::class, 'storeOrderItem']);
Route::post('orders/{id}/item/remove', [OrderController::class, 'destroyOrderItem']);