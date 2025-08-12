<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\UmkmController;

// Rute untuk login
Route::get('/', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);

// Grup rute yang memerlukan otentikasi
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/peta-persebaran', [MapController::class, 'index'])->name('peta.index');
    Route::resource('umkm', UmkmController::class);
});

// =====================================================================
// PASTIKAN RUTE API INI BERADA DI LUAR MIDDLEWARE AUTH
// =====================================================================
Route::get('/api/kelurahan/{kecamatan_id}', [UmkmController::class, 'getKelurahanByKecamatan']);
