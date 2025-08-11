<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\UmkmController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rute untuk menampilkan halaman login
Route::get('/', [LoginController::class, 'create'])->name('login');
// Rute untuk memproses data login
Route::post('/login', [LoginController::class, 'store']);

// Grup rute yang hanya bisa diakses oleh pengguna yang sudah terotentikasi (login)
Route::middleware('auth')->group(function () {
    // Rute untuk logout
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    
    // Rute untuk halaman utama setelah login
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rute untuk Peta Persebaran
    Route::get('/peta-persebaran', [MapController::class, 'index'])->name('peta.index');

    // Route::resource akan secara otomatis membuat semua rute yang diperlukan
    // untuk CRUD (index, create, store, edit, update, destroy) pada UMKM.
    Route::resource('umkm', UmkmController::class);
});

// API Route untuk mendapatkan data Kelurahan.
// Ditempatkan di luar middleware 'auth' agar mudah diakses oleh JavaScript.
Route::get('/api/kelurahan/{kecamatan_id}', [UmkmController::class, 'getKelurahanByKecamatan']);
