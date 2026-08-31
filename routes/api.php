<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;

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
});