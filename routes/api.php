<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\UserHealthInfoController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\AdminOnly;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/wishlist' , [WishListController::class , 'index'])->middleware('auth:sanctum');
Route::post('/wishlist' , [WishListController::class , 'store'])->middleware('auth:sanctum');

// Homepage routes
Route::get('/home/meals/matched', [HomePageController::class, 'matchedMeals']);
Route::get('/home/meals/types', [HomePageController::class, 'dietTypesMeals']);

Route::get('meals/diet-types', [\App\Http\Controllers\MealController::class, 'dietTypes']);
Route::get('meals/search', [\App\Http\Controllers\MealController::class, 'search']);
Route::get('meals/recommend', [\App\Http\Controllers\MealController::class, 'recommendedMeals']);
Route::get('meals/popular', [\App\Http\Controllers\MealController::class, 'popular']);
Route::apiResource('meals', \App\Http\Controllers\MealController::class)->only(['index' , 'show']);

// User Health Info routes
Route::post('/user-health-info/create-or-update', [UserHealthInfoController::class, 'createOrUpdate']);
Route::get('/user-health-info/{userId}', [UserHealthInfoController::class, 'show']);

Route::apiResource('reviews', \App\Http\Controllers\ReviewController::class);

Route::middleware(AdminOnly::class)->group(function(){

    Route::get('orders/status-options', [\App\Http\Controllers\OrderController::class, 'statusOptions']);
    Route::get('orders/user-self', [\App\Http\Controllers\OrderController::class, 'userSelf']);
    Route::apiResource('orders', \App\Http\Controllers\OrderController::class);


    Route::apiResource('meals', \App\Http\Controllers\MealController::class)->except(['index' , 'show']);

    Route::get('ingredients/stats', [\App\Http\Controllers\IngredientController::class, 'stats']);
    Route::apiResource('ingredients', \App\Http\Controllers\IngredientController::class);

    Route::get('allergens/stats', [\App\Http\Controllers\AllergenController::class, 'stats']);
    Route::apiResource('allergens', \App\Http\Controllers\AllergenController::class);

    Route::apiResource('users', \App\Http\Controllers\UserController::class);
});


Route::post('/login' , [AuthController::class , 'login']);
Route::post('/register' , [AuthController::class , 'register']);
Route::post('/logout' , [AuthController::class , 'logout'])->middleware('auth:sanctum');
Route::post('auth/self' , [AuthController::class , 'self'])->middleware('auth:sanctum');
