<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\ItemProduksiController;
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
    Route::resource('divisi', DivisiController::class);
    Route::resource('jenisPembayaran', JenisPembayaranController::class);
    Route::resource('statusPesanan', StatusPesananController::class);
    Route::resource('satuanHarga', SatuanHargaController::class);
    Route::resource('kategoriUsaha', KategoriUsahaController::class);
    Route::resource('itemProduksi', ItemProduksiController::class);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Route::get('/dashboard', [AuthController::class, 'index1'])->name('dashboard'); ini route tes
});



Route::get('/', function () {
    return view('welcome');
});

Route::get('/login-tes', function () {
    return view('auth.login');
});

Route::get('/loginadmin', function () {
    return view('auth.loginadmin');
});

route::get('/dashboard', function () {
    return view('pelanggan.dashboard');
});

Route::get('/test-admin', function () {
    return view('dashboard', [
        'role' => 'Admin'
    ]);
});

Route::get('/test-superadmin', function () {
    return view('dashboard', [
        'role' => 'Super-Admin'
    ]);
});