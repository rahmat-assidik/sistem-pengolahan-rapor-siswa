<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\GuruController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\TahunAjaranController;
use App\Http\Controllers\Api\MapelController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Routes untuk Admin Management
Route::prefix('admin')->group(function () {
    // Siswa Management
    Route::apiResource('siswa', SiswaController::class);
    
    // Guru Management
    Route::apiResource('guru', GuruController::class);
    
    // Kelas Management
    Route::apiResource('kelas', KelasController::class);
    
    // Mata Pelajaran Management
    Route::apiResource('mapel', MapelController::class);
    
    // Tahun Ajaran Management
    Route::apiResource('tahun-ajaran', TahunAjaranController::class);
});
