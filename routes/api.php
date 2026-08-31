<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas publicas de productos 
Route::get('/products', [ProductController::class, 'index']); 
Route::get('/products/{id}', [ProductController::class, 'show']);

//Rutas publicas de usuarios
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);



// Rutas protegidas de productos 
Route::middleware('auth:sanctum')->group(function () { 
    Route::post('/products', [ProductController::class, 'store']); 
    Route::put('/products/{id}', [ProductController::class, 'update']); 
    Route::delete('/products/{id}', [ProductController::class, 'destroy']); 
    Route::post('/products/{id}/restore', [ProductController::class, 'restore']); 


    // Orders
    Route::get('/orders',[OrderController::class,'index']);
    Route::post('/orders',[OrderController::class,'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    //payments
    Route::post('/orders/{orderId}/payment',[PaymentController::class,'store']);
    Route::get('/orders/{orderId}/payment',[PaymentController::class,'show']);
});