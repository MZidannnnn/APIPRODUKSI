<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengguna;
use App\Models\ItemProduksi;
use App\Models\KategoriUsaha;
use App\Models\Pesanan;
use App\Models\Pembayaran;
use App\Models\RincianPesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard untuk role Admin (id=2).
     * Admin mendapat akses penuh ke semua data tanpa filter kategori.
     */
    public function dashboardAdmin()
    {
        // Jumlah akun berdasarkan role
        $jumlahAdmin = Pengguna::where('id_role', 2)->count();
        $jumlahKlien = Pengguna::where('id_role', 3)->count();

        // Jumlah produk aktif
        $jumlahProduk = ItemProduksi::where('status_aktif', 'Aktif')->count();

        // Jumlah semua pesanan
        $jumlahPesanan = Pesanan::count();

        // Total penjualan bulan ini dari pembayaran berhasil/lunas
        $totalPenjualanBulanIni = Pembayaran::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('jumlah_bayar');

        // Grafik penjualan 6 bulan terakhir
        $grafikPenjualan = Pembayaran::select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('YEAR(created_at) as tahun'),
                DB::raw('SUM(jumlah_bayar) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        $labelPenjualan = [];
        $dataPenjualan = [];

        for ($i = 5; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subMonths($i);
            $bulan = $tanggal->month;
            $tahun = $tanggal->year;

            $labelPenjualan[] = $tanggal->translatedFormat('M Y');

            $total = $grafikPenjualan
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->first();

            $dataPenjualan[] = $total ? (int) $total->total : 0;
        }

        // Grafik produk per kategori
        $kategoriData = KategoriUsaha::withCount([
            'itemProduksi' => function ($query) {
                $query->where('status_aktif', 'Aktif');
            }
        ])->get();

        $labelKategori = $kategoriData->pluck('nama_kategori')->toArray();
        $dataKategori = $kategoriData->pluck('item_produksi_count')->toArray();

        // Pesanan terbaru (semua, tanpa filter)
        $pesananTerbaru = Pesanan::with(['pengguna', 'statusPesanan'])
            ->latest()
            ->take(5)
            ->get();

        // Produk terbaru
        $produkTerbaru = ItemProduksi::with(['kategoriUsaha', 'fotoProduk'])
            ->latest()
            ->take(5)
            ->get();

        // Pesanan selesai bulan ini (global, semua admin)
        $pesananSelesaiBulanIni = Pesanan::whereHas('statusPesanan', function ($query) {
                $query->where('nama_status_pesanan', 'Pesanan Selesai');
            })
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->count();

        $data = [
            'title'                  => 'Dashboard Admin',
            'menuDashboard'          => 'active',

            'jumlahAdmin'            => $jumlahAdmin,
            'jumlahKlien'            => $jumlahKlien,
            'jumlahProduk'           => $jumlahProduk,
            'jumlahPesanan'          => $jumlahPesanan,
            'totalPenjualanBulanIni' => $totalPenjualanBulanIni,

            'labelPenjualan'         => $labelPenjualan,
            'dataPenjualan'          => $dataPenjualan,

            // Objek koleksi Eloquent — dibutuhkan view untuk @forelse loop
            'kategoriData'           => $kategoriData,

            // Alias yang dibutuhkan view lama
            'totalProduk'            => $jumlahProduk,

            'labelKategori'          => $labelKategori,
            'dataKategori'           => $dataKategori,

            'pesananSelesaiBulanIni' => $pesananSelesaiBulanIni,
            'pesananTerbaru'         => $pesananTerbaru,
            'produkTerbaru'          => $produkTerbaru,
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * Dashboard untuk role Owner (id=1).
     * Owner HANYA bisa melihat halaman statistik penjualan yang sangat komprehensif.
     */
    public function dashboardOwner()
    {
        // Jumlah akun berdasarkan role
        $jumlahAdmin = Pengguna::where('id_role', 2)->count();
        $jumlahKlien = Pengguna::where('id_role', 3)->count();

        // Jumlah produk aktif
        $jumlahProduk = ItemProduksi::where('status_aktif', 'Aktif')->count();

        // Jumlah semua pesanan
        $jumlahPesanan = Pesanan::count();

        // Total penjualan bulan ini dari pembayaran berhasil/lunas
        $totalPenjualanBulanIni = Pembayaran::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('jumlah_bayar');

        // Grafik penjualan 6 bulan terakhir
        $grafikPenjualan = Pembayaran::select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('YEAR(created_at) as tahun'),
                DB::raw('SUM(jumlah_bayar) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        $labelPenjualan = [];
        $dataPenjualan = [];

        for ($i = 5; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subMonths($i);
            $bulan = $tanggal->month;
            $tahun = $tanggal->year;

            $labelPenjualan[] = $tanggal->translatedFormat('M Y');

            $total = $grafikPenjualan
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->first();

            $dataPenjualan[] = $total ? (int) $total->total : 0;
        }

        // Grafik produk per kategori
        $kategoriData = KategoriUsaha::withCount([
            'itemProduksi' => function ($query) {
                $query->where('status_aktif', 'Aktif');
            }
        ])->get();

        $labelKategori = $kategoriData->pluck('nama_kategori')->toArray();
        $dataKategori = $kategoriData->pluck('item_produksi_count')->toArray();

        // Pesanan terbaru (semua, tanpa filter)
        $pesananTerbaru = Pesanan::with(['pengguna', 'statusPesanan'])
            ->latest()
            ->take(5)
            ->get();

        // Produk terbaru
        $produkTerbaru = ItemProduksi::with(['kategoriUsaha', 'fotoProduk'])
            ->latest()
            ->take(5)
            ->get();

        // Pesanan selesai bulan ini (global, semua admin)
        $pesananSelesaiBulanIni = Pesanan::whereHas('statusPesanan', function ($query) {
                $query->where('nama_status_pesanan', 'Pesanan Selesai');
            })
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->count();

        $data = [
            'title'                  => 'Dashboard Owner',
            'menuDashboard'          => 'active',

            'jumlahAdmin'            => $jumlahAdmin,
            'jumlahKlien'            => $jumlahKlien,
            'jumlahProduk'           => $jumlahProduk,
            'jumlahPesanan'          => $jumlahPesanan,
            'totalPenjualanBulanIni' => $totalPenjualanBulanIni,

            'labelPenjualan'         => $labelPenjualan,
            'dataPenjualan'          => $dataPenjualan,

            'kategoriData'           => $kategoriData,
            'totalProduk'            => $jumlahProduk,
            'labelKategori'          => $labelKategori,
            'dataKategori'           => $dataKategori,

            'pesananSelesaiBulanIni' => $pesananSelesaiBulanIni,
            'pesananTerbaru'         => $pesananTerbaru,
            'produkTerbaru'          => $produkTerbaru,
        ];

        return view('admin/dashboard', $data);
    }
}