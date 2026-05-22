<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPenjualanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPenjualanController extends Controller
{
    public function index(Request $request)
{
    $filters = $request->only([
        'tanggal_mulai',
        'tanggal_selesai'
    ]);

    $laporanPenjualan = $this->buildQuery(
        $filters['tanggal_mulai'] ?? null,
        $filters['tanggal_selesai'] ?? null
    )
    ->where('pb.status_bayar', 'Lunas')
    ->paginate(50) // Hanya tampilkan 50 baris per halaman web
    ->withQueryString(); // Menjaga filter tanggal tetap aktif saat pindah halaman

    $data = [
        'title'                => 'Laporan Penjualan',
        'menuLaporanPenjualan' => 'active',
        'laporan'              => $laporanPenjualan,
        'filters'              => $filters,
    ];

    return view('super-admin/laporan-penjualan/index', $data);
}

    private function validateTanggal(Request $request): array
    { 
        return $request->validate([
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
        ]);
    }

    private function buildQuery(?string $tanggalMulai = null, ?string $tanggalSelesai = null)
    {
        $query = DB::table('pembayaran as pb')
            ->join('pesanan as ps', 'pb.id_pesanan', '=', 'ps.id_pesanan')
            // Menggunakan leftJoin agar data tetap tampil jika relasi ada yang kosong
            ->leftJoin('rincian_pesanan as rp', 'ps.id_pesanan', '=', 'rp.id_pesanan')
            ->leftJoin('detail_produk as dp', 'ps.id_detail_produk', '=', 'dp.id_detail_produk')
            ->leftJoin('item_produksi as ip', 'dp.id_item_produksi', '=', 'ip.id_item_produksi')
            ->leftJoin('status_pesanan as sp', 'ps.id_status_pesanan', '=', 'sp.id_status_pesanan')
            ->select([
                'ps.id_pesanan',
                'ps.tanggal_pesan',
                'ps.nama_penerima',
                'ip.nama_item',
                'dp.ukuran',
                DB::raw('COALESCE(rp.kuantitas, 1) as kuantitas'),
                'ps.total_harga',
                'sp.nama_status_pesanan as status_pesanan',
                'pb.created_at as tanggal_bayar',
                'pb.tipe_pembayaran',
                'pb.jumlah_bayar',
                'pb.payment_type',
                'pb.status_bayar',
            ]);

        // Jika parameter tanggal diisi, baru tambahkan filter whereBetween
        if ($tanggalMulai && $tanggalSelesai) {
            $query->whereBetween(DB::raw('DATE(pb.created_at)'), [$tanggalMulai, $tanggalSelesai]);
        }

        return $query
            ->orderBy('ps.tanggal_pesan')
            ->orderBy('ps.id_pesanan')
            ->orderBy('pb.created_at');
    }


    public function exportExcel(Request $request)
    {
        $validated = $this->validateTanggal($request);

        return Excel::download(
            new LaporanPenjualanExport($validated['tanggal_mulai'], $validated['tanggal_selesai']),
            'laporan-penjualan.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $validated = $this->validateTanggal($request);
        $rows = $this->buildQuery($validated['tanggal_mulai'], $validated['tanggal_selesai'])->get();

        $pdf = Pdf::loadView('test.penjualan-pdf', [
            'rows' => $rows,
            'filters' => $validated,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-penjualan.pdf');
    }
}
