<?php

use Illuminate\Support\Facades\Route;
use Packages\Store\Http\Controllers\Api\StoreController;
use Packages\Store\Http\Controllers\Api\StoreGiftOperationController;
use Packages\Store\Http\Controllers\Api\StoreOperationController;
use Packages\Store\Http\Controllers\Api\StorePlaceController;

Route::prefix('v1')->group(function() {

    # Stores
    Route::get('stores', [StoreController::class, 'index'])->middleware(['auth:sanctum', 'admin:storekeeper']);
    Route::get('stores/{id}', [StoreController::class, 'show'])->middleware(['auth:sanctum', 'admin:storekeeper']);
    Route::get('stores/{id}/contents', [StoreController::class, 'contents'])->middleware(['auth:sanctum', 'admin:storekeeper']);
    Route::post('stores', [StoreController::class, 'store'])->middleware(['auth:sanctum', 'admin:storekeeper']);
    Route::post('stores/{id}', [StoreController::class, 'update'])->middleware(['auth:sanctum', 'admin:storekeeper']);
    Route::delete('stores/{id}', [StoreController::class, 'delete'])->middleware(['auth:sanctum', 'admin:storekeeper']);

    # Places
    Route::get('store-places', [StorePlaceController::class, 'index'])->middleware(['auth:sanctum', 'admin:storekeeper']);
    Route::get('store-places/{id}', [StorePlaceController::class, 'show'])->middleware(['auth:sanctum', 'admin:storekeeper']);
    Route::post('store-places', [StorePlaceController::class, 'store'])->middleware(['auth:sanctum', 'admin:storekeeper']);
    Route::post('store-places/{id}', [StorePlaceController::class, 'update'])->middleware(['auth:sanctum', 'admin:storekeeper']);
    Route::delete('store-places/{id}', [StorePlaceController::class, 'delete'])->middleware(['auth:sanctum', 'admin:storekeeper']);

    # Store operations
    Route::get('store-operations', [StoreOperationController::class, 'index'])->middleware(['auth:sanctum', 'admin:storekeeper']);
    Route::get('store-operations/{id}', [StoreOperationController::class, 'show'])->middleware(['auth:sanctum', 'admin:storekeeper']);

    Route::post('store-operation/put', [StoreOperationController::class, 'put'])->middleware(['auth:sanctum', 'admin:storekeeper']);
    Route::post('store-operation/take', [StoreOperationController::class, 'take'])->middleware(['auth:sanctum', 'admin:storekeeper']);
    Route::post('store-operation/correction', [StoreOperationController::class, 'correction'])->middleware(['auth:sanctum', 'admin:storekeeper']);

    Route::get('store-operations/product/{id}', [StoreOperationController::class, 'productOperations'])->middleware(['auth:sanctum', 'admin:storekeeper']);
    Route::get('store-operations/product-residue/{id}', [StoreOperationController::class, 'productResidue'])->middleware(['auth:sanctum', 'admin:storekeeper']);

    Route::post('store-operation/clear-system-place', [StoreOperationController::class, 'clearSystemPlace'])->middleware(['auth:sanctum', 'admin:storekeeper']);

    # Store gift operations
    Route::post('store-gift-operation/put', [StoreGiftOperationController::class, 'put'])->middleware(['auth:sanctum', 'admin:storekeeper']);
    Route::post('store-gift-operation/take', [StoreGiftOperationController::class, 'take'])->middleware(['auth:sanctum', 'admin:storekeeper']);
    Route::post('store-gift-operation/correction', [StoreGiftOperationController::class, 'correction'])->middleware(['auth:sanctum', 'admin:storekeeper']);

    Route::get('store-gift-operations/gift-residue/{id}', [StoreGiftOperationController::class, 'giftResidue'])->middleware(['auth:sanctum', 'admin:storekeeper']);

    Route::post('store-gift-operation/clear-system-place', [StoreGiftOperationController::class, 'clearSystemPlace'])->middleware(['auth:sanctum', 'admin:storekeeper']);
});