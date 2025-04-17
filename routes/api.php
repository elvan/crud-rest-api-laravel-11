<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\SwaggerController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// API Documentation routes
Route::get('/docs', [SwaggerController::class, 'index'])->name('api.swagger.index');
Route::get('/docs/json', [SwaggerController::class, 'json'])->name('api.swagger.json');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // User routes - only accessible by admin
    Route::middleware('can:admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });

    // Search routes
    Route::prefix('search')->group(function () {
        Route::get('/', [SearchController::class, 'search']);
        Route::get('/name', [SearchController::class, 'searchByName']);
        Route::get('/nim', [SearchController::class, 'searchByNim']);
        Route::get('/ymd', [SearchController::class, 'searchByYmd']);
    });
});
