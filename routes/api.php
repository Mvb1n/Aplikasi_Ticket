<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\WebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Rute untuk menerima data aset baru
    Route::post('/v1/assets', [ApiController::class, 'storeAsset']);
    Route::post('/v1/incidents', [ApiController::class, 'storeIncident']);

     // Rute baru untuk mengambil daftar semua site
    Route::get('/v1/sites', [ApiController::class, 'getSites']);

    // Rute baru untuk mengambil aset berdasarkan site
    Route::get('/v1/sites/{site}/assets', [ApiController::class, 'getAssetsBySite']);

     // Rute baru untuk meng-update aset berdasarkan nomor seri
    Route::put('/v1/assets/{serial_number}', [ApiController::class, 'updateAsset']);

    // Rute baru untuk menghapus aset berdasarkan nomor seri
    Route::delete('/v1/assets/{serial_number}', [ApiController::class, 'destroyAsset']);

    // Rute baru untuk membatalkan insiden
    Route::post('/v1/incidents/{incident}/cancel', [ApiController::class, 'cancelIncident']);

    // Tambahkan rute API lain di sini nanti
});