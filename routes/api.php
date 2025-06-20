<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IngredientController;
use App\Http\Middleware\AdminOnly;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('meals/diet-types', [\App\Http\Controllers\MealController::class, 'dietTypes']);
Route::apiResource('meals', \App\Http\Controllers\MealController::class)->only(['index' , 'show']);
Route::get('/ingredients' , [IngredientController::class , 'index']);

Route::middleware(AdminOnly::class)->group(function(){

    Route::apiResource('reviews', \App\Http\Controllers\ReviewController::class);

    Route::get('orders/status-options', [\App\Http\Controllers\OrderController::class, 'statusOptions']);
    Route::get('orders/user-self', [\App\Http\Controllers\OrderController::class, 'userSelf']);
    Route::apiResource('orders', \App\Http\Controllers\OrderController::class);


    Route::apiResource('meals', \App\Http\Controllers\MealController::class)->except(['index' , 'show']);

    Route::get('ingredients/stats', [\App\Http\Controllers\IngredientController::class, 'stats']);
    Route::apiResource('ingredients', \App\Http\Controllers\IngredientController::class)->except(['index' , 'show']);

    Route::get('allergens/stats', [\App\Http\Controllers\AllergenController::class, 'stats']);
    Route::apiResource('allergens', \App\Http\Controllers\AllergenController::class)->except(['index' , 'show']);

    Route::apiResource('users', \App\Http\Controllers\UserController::class);
});


Route::post('/login' , [AuthController::class , 'login']);
Route::post('/register' , [AuthController::class , 'register']);
Route::post('/logout' , [AuthController::class , 'logout']);