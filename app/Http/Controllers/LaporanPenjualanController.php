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

        $query = $this->buildQuery(
            $filters['tanggal_mulai'] ?? null,
            $filters['tanggal_selesai'] ?? null
        );

        $laporanPenjualan = $query->get();

        $totalPenjualan = $query->sum('pb.jumlah_bayar');

        $data = [
            'title'                => 'Laporan Penjualan',
            'menuLaporanPenjualan' => 'active',
            'laporan'              => $laporanPenjualan,
            'filters'              => $filters,
            'totalPenjualan'       => $totalPenjualan,
        ];

        return view('super-admin.laporan-penjualan.index', $data);
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
            ->leftJoin('rincian_pesanan as rp', 'ps.id_pesanan', '=', 'rp.id_pesanan')
            ->leftJoin('detail_produk as dp', 'ps.id_detail_produk', '=', 'dp.id_detail_produk')
            ->leftJoin('item_produksi as ip', 'dp.id_item_produksi', '=', 'ip.id_item_produksi')
            ->leftJoin('status_pesanan as sp', 'ps.id_status_pesanan', '=', 'sp.id_status_pesanan')
            ->select([
                'ps.id_pesanan',
                'ps.kode_resi_pesanan',
                'ps.tanggal_pesan',
                'ps.updated_at as tanggal_selesai',
                'ps.nama_penerima',
                'ip.nama_item',
                'dp.ukuran',
                DB::raw('COALESCE(rp.kuantitas, 1) as kuantitas'),
                'ps.total_harga',
                'sp.nama_status_pesanan as status_pesanan',
                'pb.tipe_pembayaran',
                'pb.jumlah_bayar',
                'pb.payment_type',
                'pb.status_bayar',
            ])
            ->where('pb.status_bayar', 'Lunas')
            ->where('sp.nama_status_pesanan', 'Pesanan Selesai');

        if ($tanggalMulai && $tanggalSelesai) {
            $query->whereBetween('ps.tanggal_pesan', [
                $tanggalMulai,
                $tanggalSelesai
            ]);
        }

        return $query
            ->orderByDesc('ps.tanggal_pesan')
            ->orderByDesc('ps.id_pesanan')
            ->orderByDesc('pb.created_at');
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
        $rows = $this->buildQuery(
            $validated['tanggal_mulai'],
            $validated['tanggal_selesai']
        )->get();

        $totalPenjualan = $rows->sum('jumlah_bayar');

        $pdf = Pdf::loadView('super-admin.laporan-penjualan.exportpdf', [
            'rows' => $rows,
            'filters' => $validated,
            'totalPenjualan' => $totalPenjualan,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-penjualan.pdf');
    }
}
