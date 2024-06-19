<?php

namespace App\Http\Controllers\Dashboard\webController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetUpdateController;
use App\Http\Controllers\ApprovalAssetController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return view('dashboard');
})->name('test');


Route::middleware(['auth:sanctum'])->prefix('/dashboard')->group(function () {
    Route::middleware(['role:Admin'])->group(function () {
        Route::get('/assetUpdate/{slug}', [WebController::class, 'edit'])->name('editAsset');
        Route::put('/assetUpdate/{slug}', [WebController::class, 'update'])->name('assetUpdate');

        // Approval Asset Route
        // create asset
        Route::get('/createAsset', [WebController::class, 'create'])->name('createAsset');
        Route::post('/createAsset', [WebController::class, 'store'])->name('addAsset.store');

        //Approval Asset Route
        Route::get('/approve-asset', [ApprovalAssetController::class, 'index'])->name('approvalAsset');
        Route::put('/approve-asset/{slug}', [ApprovalAssetController::class, 'approve'])->name('approveAsset');

        // Delete
        Route::delete('/{slug}', [WebController::class, 'remove'])->name('assetRemove');

        Route::get('/history', [WebController::class, 'history'])->name('history');

    });

    Route::middleware(['role:PIC'])-> group(function(){
        // can edit to get approval
        Route::get('/dashboard/assetUpdatePIC/{slug}', [ApprovalAssetController::class, 'edit'])-> name('editAssetPIC');
        Route::post('/dashboard/assetUpdatePIC/{slug}', [ApprovalAssetController::class, 'store'])-> name('assetApprovalStore');

        Route::get('/historyPIC', [ApprovalAssetController::class, 'history'])->name('historyPIC');

    });

    Route::middleware(['role:Admin,PIC,User'])->group(function () {
        // Dashboard Asset Route
        Route::get('/', [WebController::class, 'index'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});


require __DIR__ . '/auth.php';
