<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);

// Semua route di dalam grup ini hanya bisa diakses setelah login
Route::middleware('auth')->group(function () {
    Route.post('/logout', [LoginController::class, 'destroy'])->name('logout');
    
    // Nanti kita akan isi dengan route untuk fitur-fitur utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('umkm', UmkmController::class);
    Route::get('/peta-persebaran', [MapController::class, 'index'])->name('peta.index');
    // Tambahkan route untuk filter dan export di sini nanti
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
