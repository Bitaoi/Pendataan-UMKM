<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\ProgramController; // Jangan lupa import ProgramController

// Rute untuk halaman login (bisa diakses publik)
Route::get('/', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);

// Rute API untuk mengambil data kelurahan (bisa diakses publik untuk form)
Route::get('/api/kelurahan/{kecamatan_id}', [UmkmController::class, 'getKelurahanByKecamatan']);

// =====================================================================
// SEMUA RUTE DI BAWAH INI HANYA BISA DIAKSES SETELAH LOGIN
// =====================================================================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Rute utama setelah login
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/peta-persebaran', [MapController::class, 'index'])->name('peta.index');
    
    // Rute untuk CRUD UMKM
    Route::resource('umkm', UmkmController::class);

    // Rute untuk Program Pembinaan (SUDAH DIPINDAHKAN KE DALAM)
    Route::resource('programs', ProgramController::class);
    Route::post('programs/{program}/add-peserta', [ProgramController::class, 'addPeserta'])->name('programs.addPeserta');
    Route::delete('programs/{program}/remove-peserta/{umkm}', [ProgramController::class, 'removePeserta'])->name('programs.removePeserta');
    Route::post('programs/{program}/add-log', [ProgramController::class, 'addLog'])->name('programs.addLog');
}); // <-- Ini adalah penutup yang benar untuk middleware group