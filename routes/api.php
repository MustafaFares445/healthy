<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\AdminOnly;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(AdminOnly::class)->group(function(){

    Route::apiResource('reviews"', \App\Http\Controllers\ReviewController::class);

    Route::apiResource('orders', \App\Http\Controllers\OrderController::class);
    Route::get('orders/status-options', [\App\Http\Controllers\OrderController::class, 'statusOptions']);

    Route::apiResource('meals', \App\Http\Controllers\MealController::class);
    Route::get('meals/diet-types', [\App\Http\Controllers\MealController::class, 'dietTypes']);


    Route::apiResource('ingredients', \App\Http\Controllers\IngredientController::class);
    Route::get('ingredients/stats', [\App\Http\Controllers\IngredientController::class, 'stats']);


    Route::apiResource('allergens', \App\Http\Controllers\AllergenController::class);
    Route::get('allergens/stats', [\App\Http\Controllers\AllergenController::class, 'stats']);

    Route::apiResource('users"', \App\Http\Controllers\UserController::class);
    Route::apiResource('user-health-infos"', \App\Http\Controllers\UserHealthInfoController::class);

    Route::post('/login' , [AuthController::class , 'login']);
    Route::post('/register' , [AuthController::class , 'register']);
    Route::post('/logout' , [AuthController::class , 'logout']);
});

