<?php

use Illuminate\Support\Facades\Route;
use Packages\Purchaser\Http\Controllers\Api\PurchaserController;

Route::prefix('v1')->group(function() {

    Route::get('admin/purchaser-boy', [PurchaserController::class, 'index'])->middleware(['auth:sanctum', 'admin']);
    Route::get('admin/purchaser-boy-as-excel', [PurchaserController::class, 'excel']);//->middleware(['auth:sanctum', 'admin']);

    Route::get('store-operations/purchaser-shortage', [PurchaserController::class, 'shortage'])->middleware(['auth:sanctum', 'admin:storekeeper']);
});