<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

// Halaman utama atau halaman login
Route::get('/', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);

// Semua route di dalam grup ini hanya bisa diakses setelah login
Route::middleware('auth')->group(function () {
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Nanti kita akan isi dengan route untuk fitur-fitur utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('umkm', UmkmController::class);
    Route::get('/peta-persebaran', [MapController::class, 'index'])->name('peta.index');
    // Tambahkan route untuk filter dan export di sini nanti
});