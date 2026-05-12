<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\StatusPesanan;
use Illuminate\Http\Request;

class PersetujuanHargaController extends Controller
{
    // Halaman admin untuk ajukan harga
    public function showAdmin(Pesanan $pesanan)
    {
        $pesanan->load(['persetujuanHarga', 'rincianPesanan', 'pengguna']);
        $persetujuan = $pesanan->persetujuanHarga;

        return view('test.penawaran-admin', compact('pesanan', 'persetujuan'));
    }

    // Halaman klien untuk lihat penawaran + setuju/tolak
    public function showStatus(Pesanan $pesanan)
    {
        $pesanan->load(['persetujuanHarga', 'pembayaran', 'statusPesanan']);
        $persetujuan = $pesanan->persetujuanHarga;

        return view('test.status-penawaran', compact('pesanan', 'persetujuan'));
    }

    public function ajukanHarga(Request $request, Pesanan $pesanan)
    {
        $validated = $request->validate([
            'harga_tawaran' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        $pesanan->persetujuanHarga()->update([
            'harga_tawaran' => $validated['harga_tawaran'],
            'catatan' => $validated['catatan'],
            'status_persetujuan' => 'Ditawarkan',
        ]);

        $statusMenunggu = StatusPesanan::where('nama_status_pesanan', 'Menunggu Persetujuan')->first();
        if ($statusMenunggu) {
            $pesanan->update([
                'id_status_pesanan' => $statusMenunggu->id_status_pesanan,
                'total_harga' => $validated['harga_tawaran'],
            ]);
        }

        return back()->with('success', 'Harga diajukan ke klien');
    }

    public function setujuHarga(Pesanan $pesanan)
    {
        $persetujuan = $pesanan->persetujuanHarga;

        $persetujuan->update([
            'status_persetujuan' => 'Disetujui',
            'harga_disetujui' => $persetujuan->harga_tawaran,
            'tanggal_persetujuan' => now(),
        ]);

        $statusMenungguBayar = StatusPesanan::where('nama_status_pesanan', 'Menunggu Pembayaran')->first();
        if ($statusMenungguBayar) {
            $pesanan->update([
                'id_status_pesanan' => $statusMenungguBayar->id_status_pesanan,
                'total_harga' => $persetujuan->harga_tawaran,
            ]);
        }

        return back()->with('success', 'Harga disetujui, silakan bayar');
    }

    public function tolakHarga(Pesanan $pesanan)
    {
        $pesanan->persetujuanHarga()->update([
            'status_persetujuan' => 'Ditolak',
            'tanggal_persetujuan' => now(),
        ]);

        $statusTolak = StatusPesanan::where('nama_status_pesanan', 'Ditolak')->first();
        if ($statusTolak) {
            $pesanan->update(['id_status_pesanan' => $statusTolak->id_status_pesanan]);
        }

        return back()->with('success', 'Harga ditolak');
    }
}
