<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\JenisPembayaranController;
use App\Http\Controllers\KategoriUsahaController;
use App\Http\Controllers\SatuanHargaController;
use App\Http\Controllers\StatusPesananController;
use Illuminate\Support\Facades\Route;

// route publik bisa diakses semua orang(belum login)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// route untuk hak akses admin dan super admin
Route::middleware(['auth', 'checkRole:2,3'])->group(function () {
    Route::resource('Divisi', DivisiController::class);
    Route::resource('JenisPembayaran', JenisPembayaranController::class);
    Route::resource('StatusPesanan', StatusPesananController::class);
    Route::resource('SatuanHarga', SatuanHargaController::class);
    Route::resource('KategoriUsaha', KategoriUsahaController::class);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Route::get('/dashboard', [AuthController::class, 'index1'])->name('dashboard'); ini route tes
});



Route::get('/', function () {
    return view('welcome');
});