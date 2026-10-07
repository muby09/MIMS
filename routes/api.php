<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InstallationController;

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('/installations', [InstallationController::class, 'index']);
    Route::post('/installations', [InstallationController::class, 'store']);
    Route::get('/installations/{id}', [InstallationController::class, 'show']);
    Route::post('/installations/bulk', [InstallationController::class, 'bulkUpload']);
});

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});
