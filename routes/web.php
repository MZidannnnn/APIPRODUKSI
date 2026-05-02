<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\ItemProduksiController;
use App\Http\Controllers\JenisPembayaranController;
use App\Http\Controllers\KategoriUsahaController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\SatuanHargaController;
use App\Http\Controllers\StatusPesananController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showDashboard'])->name('dashboard');

// route publik bisa diakses semua orang(belum login)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::get('/login', [AuthController::class, 'showLoginPelanggan'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/login-admin', [AuthController::class, 'showLoginAdmin'])->name('login-admin');
    Route::post('/login-admin', [AuthController::class, 'loginAdmin']);

    
});


Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

// route untuk hak akses admin dan super admin
Route::middleware(['auth', 'checkRole:1,2'])->group(function () {
    Route::resource('divisi', DivisiController::class);
    Route::resource('jenisPembayaran', JenisPembayaranController::class);
    Route::resource('statusPesanan', StatusPesananController::class);
    Route::resource('satuanHarga', SatuanHargaController::class);
    Route::resource('kategoriUsaha', KategoriUsahaController::class);
    Route::resource('itemProduksi', ItemProduksiController::class);
    Route::resource('pengguna', PenggunaController::class);
});


// Route::get('/login-tes', function () {
//     return view('auth.login');
// });

// Route::get('/loginadmin', function () {
//     return view('auth.loginadmin');
// });

// route::get('/dashboard', function () {
//     return view('pelanggan.dashboard');
// });

// Route::get('/test-admin', function () {
//     return view('dashboard', [
//         'role' => 'Admin'
//     ]);
// });

// Route::get('/test-superadmin', function () {
//     return view('dashboard', [
//         'role' => 'Super-Admin'
//     ]);
// });