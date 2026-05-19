<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatAdminController;
use App\Http\Controllers\ChatAttachmentController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
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
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTE
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'showDashboard'])->name('dashboard');

// webhook midtrans
Route::post('/midtrans/notification', [PembayaranController::class, 'notification']);

// route testing
Route::get('/pesanan/list-item', [PesananController::class, 'showList'])->name('pesanan.listItem');
Route::get('/pesanan/detail/{id}', [PesananController::class, 'showLisDetail'])->name('pesanan.detail');
Route::get('/pesanan/{pesanan}/tagihan', [PesananController::class, 'showTagihan'])
    ->name('pesanan.tagihan');


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
    Route::get('/adminprivasi', function () {
        return view('welcome-admin');
    })->name('welcomeAdmin');

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

    // fitur lampiran file pada yo chat
    Route::get('/chat/attachments/{lampiran}/preview', [ChatAttachmentController::class, 'preview'])
        ->name('chat.attachments.preview');

    Route::get('/chat/attachments/{lampiran}/download', [ChatAttachmentController::class, 'download'])
        ->name('chat.attachments.download');

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

        // harga kostum
        Route::get('/pesanan/{pesanan}/status-harga', [PersetujuanHargaController::class, 'showStatus'])
            ->name('pesanan.statusHarga');

        Route::post('/pesanan/{pesanan}/setuju-harga', [PersetujuanHargaController::class, 'setujuHarga'])
            ->name('pesanan.setujuHarga');

        Route::post('/pesanan/{pesanan}/tolak-harga', [PersetujuanHargaController::class, 'tolakHarga'])
            ->name('pesanan.tolakHarga');

        // fitur chat klien
        Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/{id}/messages', [ChatController::class, 'messages'])->name('chat.messages');
        Route::post('/chat/{id}/messages', [ChatController::class, 'send'])->name('chat.send');
        Route::get('/chat/unread-count', [ChatController::class, 'unreadCount'])->name('chat.unread');
        Route::get('/chat/{id}', [ChatController::class, 'show'])->name('chat.show');
    });


    /*
    |--------------------------------------------------------------------------
    | SUPER ADMIN
    |--------------------------------------------------------------------------
    */

    Route::middleware('checkRole:1')->group(function () {
        // dashboard super admin
        Route::get('dashboard-super-admin', [DashboardController::class, 'dashboardSuperAdmin'])->name('dashboardSuperAdmin');

        // KELOLA AKUN
        // View data akun berdasarkan role
        Route::get('/data-master', [KategoriUsahaController::class, 'index'])
            ->name('viewDataMaster');

        // Form tambah akun
        Route::get('/kelola-akun/create/{role}', [PenggunaController::class, 'create'])
            ->name('kelolaAkunCreate');

        // Simpan data akun
        Route::post('/kelola-akun/store', [PenggunaController::class, 'store'])
            ->name('kelolaAkunStore');

        // Form edit akun
        Route::get('/kelola-akun/edit/{id}', [PenggunaController::class, 'edit'])
            ->name('kelolaAkunEdit');

        // Update data akun
        Route::put('/kelola-akun/update/{id}', [PenggunaController::class, 'update'])
            ->name('kelolaAkunUpdate');

        // Hapus akun
        Route::delete('/kelola-akun/delete/{id}', [PenggunaController::class, 'destroy'])
            ->name('kelolaAkunDelete');

        // View data akun berdasarkan role
        Route::get('/kelola-akun/{role}', [PenggunaController::class, 'index'])
            ->name('viewKelolaAkun');


        // Data Master
        // Kategori Usaha
        Route::resource('kategoriUsaha', KategoriUsahaController::class);

        // Satus Pesanan
        Route::resource('statusPesanan', StatusPesananController::class);

        //Satuan Harga
        Route::resource('satuanHarga', SatuanHargaController::class);

        // Jenis Pembayaran
        Route::resource('jenisPembayaran', JenisPembayaranController::class);

        //Kelola Akun
        Route::get('/kelola-akun/{role}', [PenggunaController::class, 'index'])->name('viewKelolaAkun');

        // route untuk export pdf dan excel
        Route::get('/super-admin/laporan/penjualan', [LaporanPenjualanController::class, 'index'])
            ->name('laporan.penjualan.index');

        Route::get('/super-admin/laporan/penjualan/excel', [LaporanPenjualanController::class, 'exportExcel'])
            ->name('laporan.penjualan.excel');

        Route::get('/super-admin/laporan/penjualan/pdf', [LaporanPenjualanController::class, 'exportPdf'])
            ->name('laporan.penjualan.pdf');
        // >>
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */

    Route::middleware('checkRole:2')->group(function () {
        // dashboard admin
        Route::get('dashboard-admin', [DashboardController::class, 'dashboardAdmin'])->name('dashboardAdmin');

        // Route::get('/admin/pesanan/{pesanan}/penawaran', [PersetujuanHargaController::class, 'showAdmin'])
        //     ->name('admin.pesanan.penawaran');

        // Route::post('/admin/pesanan/{pesanan}/penawaran', [PersetujuanHargaController::class, 'ajukanHarga'])
        //     ->name('admin.pesanan.ajukanHarga');

        // fitur chat admin
        Route::get('/admin/chat', [ChatAdminController::class, 'index'])->name('admin.chat.index');
        // Route::get('/admin/chat/{id}', [ChatAdminController::class, 'show'])->name('admin.chat.show');
        // Route::get('/admin/chat/{id}/messages', [ChatAdminController::class, 'messages'])->name('admin.chat.messages');
        // Route::post('/admin/chat/{id}/messages', [ChatAdminController::class, 'send'])->name('admin.chat.send');
        Route::get('/admin/chat/{percakapan}', [ChatAdminController::class, 'show'])->name('admin.chat.show')
            ->middleware('can:accessAdmin,percakapan');

        Route::get('/admin/chat/{percakapan}/messages', [ChatAdminController::class, 'messages'])->name('admin.chat.messages')
            ->middleware('can:accessAdmin,percakapan');

        Route::post('/admin/chat/{percakapan}/messages', [ChatAdminController::class, 'send'])->name('admin.chat.send')
            ->middleware('can:accessAdmin,percakapan');
    });
});



//Route::resource('itemProduksi', ItemProduksiController::class);
//Route::resource('pengguna', PenggunaController::class);
// Route::get('/login-tes', function () {
//     return view('auth.login');
// });

Route::get('/login-klien', function () {
    return view('klien.index');
});

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