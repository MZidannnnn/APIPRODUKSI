<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemProduksiController;
use App\Http\Controllers\JenisPembayaranController;
use App\Http\Controllers\KategoriUsahaController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\SatuanHargaController;
use App\Http\Controllers\StatusPesananController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTE
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'showDashboard'])->name('dashboard');

// webhook midtrans
Route::post('/midtrans/notification', [PembayaranController::class, 'notification']);


/*
|--------------------------------------------------------------------------
| ROUTE GUEST (BELUM LOGIN)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    // register klien
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // login klien
    Route::get('/login', [AuthController::class, 'showLoginPelanggan'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // landing page admin
    Route::get('/adminprivasi', function () {return view('welcome-admin');})->name('welcomeAdmin');

    // login admin
    Route::get('/login-admin', [AuthController::class, 'showLoginAdmin'])->name('loginAdmin');
    Route::post('/login-admin', [AuthController::class, 'loginAdmin'])->name('loginAdminProses');

    /*
    |--------------------------------------------------------------------------
    | LUPA PASSWORD
    |--------------------------------------------------------------------------
    */

    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])
        ->name('password.request');

    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->name('password.update');

});


/*
|--------------------------------------------------------------------------
| ROUTE SUDAH LOGIN
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


    /*
    |--------------------------------------------------------------------------
    | PELANGGAN
    |--------------------------------------------------------------------------
    */

    Route::middleware('checkRole:3')->group(function () {

        Route::get('/pesanan/checkout', function () {
            return view('pesanan.checkout');
        })->name('pesanan.checkout');

        Route::post('/pesanan/beli', [PesananController::class, 'beliSekarang'])
            ->name('pesanan.beli');

        // transaksi midtrans
        Route::post('/pembayaran/midtrans', [PembayaranController::class, 'createTransaction'])
            ->name('pembayaran.midtrans');

        // upload bukti
        Route::get('/pembayaran/{pembayaran}/upload-bukti', [PembayaranController::class, 'showUploadForm'])
            ->name('pembayaran.upload.form');

        Route::post('/pembayaran/{pembayaran}/upload-bukti', [PembayaranController::class, 'uploadBukti'])
            ->name('pembayaran.upload');

    });


    /*
    |--------------------------------------------------------------------------
    | ADMIN & SUPER ADMIN
    |--------------------------------------------------------------------------
    */

    Route::middleware('checkRole:1,2')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboardAdmin');


        Route::resource('jenisPembayaran', JenisPembayaranController::class);
        Route::resource('statusPesanan', StatusPesananController::class);
        Route::resource('satuanHarga', SatuanHargaController::class);
        Route::resource('kategoriUsaha', KategoriUsahaController::class);
        Route::resource('itemProduksi', ItemProduksiController::class);
        Route::resource('pengguna', PenggunaController::class);

    });

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