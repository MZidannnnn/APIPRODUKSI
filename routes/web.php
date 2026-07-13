<?php

use App\Http\Controllers\AdminPesananStatusController; 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatAdminController;
use App\Http\Controllers\ChatAttachmentController;
use App\Http\Controllers\ChatKlienController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\KlienDashboardController;
use App\Http\Controllers\ItemProduksiController;
use App\Http\Controllers\JenisPembayaranController;
use App\Http\Controllers\KategoriUsahaController;
use App\Http\Controllers\LaporanPenjualanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PersetujuanHargaController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\SatuanHargaController;
use App\Http\Controllers\StatusPesananController;
use App\Http\Controllers\CariProdukController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\KlienProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTE
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'showDashboard'])->middleware('redirectRole')->name('dashboard');

// webhook midtrans
Route::post('/midtrans/notification', [PembayaranController::class, 'notification']);

// route testing
Route::get('/pesanan/list-item', [PesananController::class, 'showList'])->name('pesanan.listItem');

// route detail produk dan pesanan
Route::get('/pesanan/detail/{id}', [PesananController::class, 'showListDetail'])->name('pesanan.detail');

Route::get('/pesanan/{pesanan}/tagihan', [PesananController::class, 'showTagihan'])->name('pesanan.tagihan');

// 1. Route untuk dropdown Live Search (JavaScript)
Route::get('/search/live', [CariProdukController::class, 'liveSearch'])->name('klien.liveSearch');

// 2. Route untuk halaman grid Hasil Pencarian (ketika di-Enter)
Route::get('/search', [CariProdukController::class, 'search'])->name('klien.search');

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
    Route::get('/login', [AuthController::class, 'showLoginKlien'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // landing page admin
    Route::get('/admin/privasi', function () {
        return view('welcome-admin');
    })->name('welcomeAdmin');

    // login admin/owner
    Route::get('/login/admin', [AuthController::class, 'showLoginAdmin'])->name('loginAdmin');
    Route::post('/login/admin', [AuthController::class, 'loginAdmin'])->name('loginAdminProses');

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

    // fitur lampiran file pada yo chat
    Route::get('/chat/attachments/{lampiran}/preview', [ChatAttachmentController::class, 'preview'])
        ->name('chat.attachments.preview');

    Route::get('/chat/attachments/{lampiran}/download', [ChatAttachmentController::class, 'download'])
        ->name('chat.attachments.download');

    /*
    |--------------------------------------------------------------------------
    | KLIEN (role 3)
    |--------------------------------------------------------------------------
    */

    Route::middleware('checkRole:3')->group(function () {

        Route::get('/pesanan/checkout', function () {return view('pesanan.checkout');})->name('pesanan.checkout');
        Route::post('/pesanan/beli', [PesananController::class, 'beliSekarang'])->name('pesanan.beli');
        
        // Detail Pesanan
        Route::get('/pesanan/{pesanan}/detail', [PesananController::class, 'detailRiwayat'])->name('klien.pesanan.detail');
        
        // fitur batalkan pesanan
        Route::post('/pesanan/{pesanan}/batal', [PembayaranController::class, 'cancelPesanan'])->name('pesanan.batal');

        // transaksi midtrans
        Route::post('/pembayaran/midtrans', [PembayaranController::class, 'createTransaction'])->name('pembayaran.midtrans');

        // upload bukti
        Route::get('/pembayaran/{pembayaran}/upload-bukti', [PembayaranController::class, 'showUploadForm'])->name('pembayaran.upload.form');
        Route::post('/pembayaran/{pembayaran}/upload-bukti', [PembayaranController::class, 'uploadBukti'])->name('pembayaran.upload');

        // fitur chat klien
        Route::get('/chat', [ChatKlienController::class, 'index'])->name('chat.index');
        Route::get('/chat/{id}/messages', [ChatKlienController::class, 'messages'])->name('chat.messages');
        Route::post('/chat/{id}/messages', [ChatKlienController::class, 'send'])->name('chat.send');
        Route::get('/chat/unread-count', [ChatKlienController::class, 'unreadCount'])->name('chat.unread');
        Route::get('/chat/unread-list', [ChatKlienController::class, 'unreadList'])->name('chat.unread.list');
        Route::get('/chat/{id}', [ChatKlienController::class, 'show'])->name('chat.show');
        Route::post('/chat/start/{itemProduksi}', [ChatKlienController::class, 'start'])->name('chat.start');

        // fitur bayar kembali riwayatPesanan
        Route::post('/pembayaran/retry', [PembayaranController::class, 'retrySnap'])->name('pembayaran.retry');
        Route::get('/pesanan/riwayat', [PesananController::class, 'riwayatPesanan'])->name('klien.pesanan.riwayat');
        Route::post('/pembayaran/{pembayaran}/sync-success', [PembayaranController::class, 'syncSuccess'])->name('pembayaran.sync-success');

        // Profil klien
        Route::get('/profile', [KlienProfileController::class, 'index'])->name('klien.profile');
        Route::put('/profile', [KlienProfileController::class, 'update'])->name('klien.profile.update');
    });


    /*
    |--------------------------------------------------------------------------
    | OWNER (role 1) — Hanya akses statistik dan export laporan
    |--------------------------------------------------------------------------
    */

    Route::middleware('checkRole:1')->group(function () {
        // Dashboard statistik owner (satu-satunya halaman yang boleh diakses owner)
        Route::get('/owner/dashboard', [AdminDashboardController::class, 'dashboardOwner'])->name('dashboardOwner');

        // Export laporan (eksklusif untuk owner)
        Route::get('/owner/laporan/penjualan', [LaporanPenjualanController::class, 'index'])->name('laporan.penjualan.index');
        Route::get('/owner/laporan/penjualan/excel', [LaporanPenjualanController::class, 'exportExcel'])->name('laporan.penjualan.excel');
        Route::get('/owner/laporan/penjualan/pdf', [LaporanPenjualanController::class, 'exportPdf'])->name('laporan.penjualan.pdf');
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN (role 2) — Akses penuh ke semua fitur operasional kecuali export
    |--------------------------------------------------------------------------
    */

    Route::middleware('checkRole:2')->group(function () {
        // Dashboard admin
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'dashboardAdmin'])->name('dashboardAdmin');

        // KELOLA AKUN
        Route::get('/kelola-akun/{role}', [PenggunaController::class, 'index'])->name('viewKelolaAkun');
        Route::get('/kelola-akun/create/{role}', [PenggunaController::class, 'create'])->name('kelolaAkunCreate');
        Route::post('/kelola-akun/store', [PenggunaController::class, 'store'])->name('kelolaAkunStore');
        Route::get('/kelola-akun/edit/{id}', [PenggunaController::class, 'edit'])->name('kelolaAkunEdit');
        Route::put('/kelola-akun/update/{id}', [PenggunaController::class, 'update'])->name('kelolaAkunUpdate');
        Route::delete('/kelola-akun/delete/{id}', [PenggunaController::class, 'destroy'])->name('kelolaAkunDelete');

        // Data Master
        Route::get('/data-master', [KategoriUsahaController::class, 'index'])->name('viewDataMaster');

        // Kategori Usaha
        Route::resource('/admin/kategori-usaha', KategoriUsahaController::class)->names('kategoriUsaha'); 

        // Status Pesanan
        Route::resource('/admin/status-pesanan', StatusPesananController::class)->names('statusPesanan');

        // Satuan Harga
        Route::resource('/admin/satuan-harga', SatuanHargaController::class)->names('satuanHarga');

        // Jenis Pembayaran
        Route::resource('/admin/jenis-pembayaran', JenisPembayaranController::class)->names('jenisPembayaran');

        // CRUD item produksi
        Route::resource('/admin/item-produksi', ItemProduksiController::class)->names('admin.itemProduksi');

        // Riwayat transaksi admin
        Route::get('/admin/riwayat-transaksi', [PembayaranController::class, 'TampilRiwayatTransaksi'])->name('admin.riwayat-transaksi.index');
        Route::get('/admin/riwayat-transaksi/{pesanan}', [PembayaranController::class, 'detailRiwayatTransaksi'])->name('admin.riwayat-transaksi.detail');

        // update status pesanan admin
        Route::get('/admin/edit-status-pesanan/{pesanan}', [AdminPesananStatusController::class, 'editStatusPesanan'])->name('admin.editStatusPesanan');
        Route::patch('/admin/update-status-pesanan/{pesanan}', [AdminPesananStatusController::class, 'updateStatusPesanan'])->name('admin.updateStatusPesanan');
        Route::get('/admin/pesanan', [AdminPesananStatusController::class, 'tampilAdminPesanan'])->name('admin.tampilPesanan');
        Route::get('/admin/progres-pesanan/{id}/edit', [AdminPesananStatusController::class, 'edit'])->name('admin.progres-pesanan.edit');
        Route::put('/admin/progres-pesanan/{id}', [AdminPesananStatusController::class, 'update'])->name('admin.progres-pesanan.update');

        // Lihat Bukti Bayar Klien
        Route::get('/admin/riwayat-transaksi/{pesanan}/bukti-bayar', [PembayaranController::class, 'lihatBuktiPesanan'])->name('admin.riwayat-transaksi.bukti-bayar');
        Route::get('/admin/bukti-bayar/{pembayaran}/file', [PembayaranController::class, 'tampilFileBukti'])->name('admin.bukti-bayar.file');

        // fitur chat admin (akses semua percakapan)
        Route::get('/admin/chat', [ChatAdminController::class, 'index'])->name('admin.chat.index');
        Route::get('/admin/chat/{percakapan}', [ChatAdminController::class, 'show'])->name('admin.chat.show')
            ->middleware('can:accessAdmin,percakapan');
        Route::get('/admin/chat/{percakapan}/messages', [ChatAdminController::class, 'messages'])->name('admin.chat.messages')
            ->middleware('can:accessAdmin,percakapan');
        Route::post('/admin/chat/{percakapan}/messages', [ChatAdminController::class, 'send'])->name('admin.chat.send')
            ->middleware('can:accessAdmin,percakapan');

        // Profil admin
        Route::get('/admin/profile', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
        Route::put('/admin/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
        // Pengaturan Koordinat Workshop
        Route::get('/admin/workshop-coordinate', [App\Http\Controllers\WorkshopCoordinateController::class, 'edit'])->name('admin.workshop-coordinate.edit');
        Route::put('/admin/workshop-coordinate', [App\Http\Controllers\WorkshopCoordinateController::class, 'update'])->name('admin.workshop-coordinate.update');
    });
});


Route::get('/riwayat-pesanan', function () {
    return view('klien.riwayat-pesanan');
});

Route::get('/profile/test', function () {
    return view('klien.profile');
});