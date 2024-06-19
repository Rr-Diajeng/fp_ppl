<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\AssetUpdateController;
use App\Http\Controllers\Api\AuthenticationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware(['role:Admin'])->group(function () {
        // Route buat Asset
        Route::post('/assets', [AssetController::class, 'store']);
        Route::post('/assets/{asset:slug}', [AssetController::class, 'update']);
        Route::delete('/assets/{asset:slug}', [AssetController::class, 'destroy']);

        // Route buat Asset Update
        Route::patch('/assetsUpdate/{slug}', [AssetUpdateController::class, 'update']);
    });

    Route::middleware(['role:PIC'])->group(function () {
        Route::post('/assetsUpdate', [AssetUpdateController::class, 'updateDataByPIC']);
        Route::get('/assetsHistory', [AssetUpdateController::class, 'indexByPIC']);
    });

    Route::middleware(['role:Admin,PIC,User'])->group(function () {
        Route::get('/assets', [AssetController::class, 'index']);
        Route::get('/assets/{asset:slug}', [AssetController::class, 'show'])->name('assets.show');
    });

    Route::get('/logout', [AuthenticationController::class, 'logout']);
});

Route::get('/assetsUpdate/list', [AssetUpdateController::class, 'index']);

Route::post('/login', [AuthenticationController::class, 'login']);
