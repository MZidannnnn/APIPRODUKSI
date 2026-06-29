<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateStatusPesananRequest;
use App\Models\Pesanan;
use App\Models\StatusPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AdminPesananStatusController extends Controller
{
    //
    // public function editStatusPesanan(Pesanan $pesanan)
    // {
    //     Gate::authorize('updateStatus', $pesanan);

    //     $pesanan->load([
    //         'detailProduk.itemProduksi.kategoriUsaha',
    //         'statusPesanan',
    //         'pengguna',
    //     ]);

    //     $statusPesanan = StatusPesanan::select('id_status_pesanan', 'nama_status_pesanan')
    //         ->orderBy('nama_status_pesanan')
    //         ->get();

    //     return view('admin/progres-pesanan/index', compact('pesanan', 'statusPesanan'));
    // }

    // public function updateStatusPesanan(UpdateStatusPesananRequest $request, Pesanan $pesanan)
    // {
    //     $pesanan->update($request->validated());

    //     return redirect()
    //         ->route('admin.editStatusPesanan', $pesanan)
    //         ->with('success', 'Status pesanan berhasil diperbarui');
    // }

    public function tampilAdminPesanan()
    {
        Gate::authorize('viewAny', Pesanan::class);

        $user = Auth::user();
        $isSuperAdmin = (int) $user->id_role === 1;

        $query = Pesanan::query()
            ->select([
                'id_pesanan',
                'kode_resi_pesanan',
                'id_pengguna',
                'id_detail_produk',
                'id_status_pesanan',
                'tanggal_pesan',
                'nama_penerima',
                'No_hp_penerima',
                'total_harga',
            ])
            ->with([
                'pengguna:id_pengguna,nama_pengguna',
                'statusPesanan:id_status_pesanan,nama_status_pesanan',
                'detailProduk:id_detail_produk,id_item_produksi,ukuran,harga_dasar',
                'detailProduk.itemProduksi:id_item_produksi,id_kategori,nama_item',
                'detailProduk.itemProduksi.kategoriUsaha:id_kategori,nama_kategori',
                'pembayaran:id_pembayaran,id_pesanan,bukti_bayar,status_bayar',
            ])
            ->whereHas('statusPesanan', function ($q) {
                $q->whereNotIn('nama_status_pesanan', [
                    'Pesanan Selesai',
                    'Pesanan Dibatalkan',
                    'Pesanan Kadaluarsa'
                ]);
            });

        // Admin biasa hanya lihat kategori sendiri
        if (! $isSuperAdmin) {
            $query->whereHas('detailProduk.itemProduksi', function ($q) use ($user) {
                $q->where('id_kategori', (int) $user->id_kategori);
            });
        }

        $pesanan = $query
            ->latest('kode_resi_pesanan')
            ->get();

        return view('admin.progres-pesanan.index', [
            'title' => 'Progres Pesanan Klien',
            'menuProgresPesanan' => 'active',
            'pesanan' => $pesanan,
        ]);
    }

    public function edit($id)
    {
        $user = Auth::user();
        $isSuperAdmin = (int) $user->id_role === 1;

        $query = Pesanan::with([
            'pengguna',
            'statusPesanan',
            'detailProduk.itemProduksi.kategoriUsaha',
            'rincianPesanan.detailProduk.itemProduksi.satuanHarga',
            'pembayaran' => fn ($q) => $q->latest('created_at'),
        ]);

        // Filter hanya untuk admin biasa
        if (! $isSuperAdmin) {
            $query->whereHas('detailProduk.itemProduksi', function ($q) use ($user) {
                $q->where('id_kategori', (int) $user->id_kategori);
            });
        }

        $pesanan = $query->findOrFail($id);

        $statusPesanan = StatusPesanan::all();

        return view('admin.progres-pesanan.edit', [
            'title' => 'Edit Progres Pesanan',
            'pesanan' => $pesanan,
            'statusPesanan' => $statusPesanan,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_status_pesanan' => 'required|exists:status_pesanan,id_status_pesanan',
        ], [
            'id_status_pesanan.required' => 'Status pesanan wajib dipilih.',
            'id_status_pesanan.exists' => 'Status pesanan tidak valid.',
        ]);

        $user = Auth::user();
        $isSuperAdmin = (int) $user->id_role === 1;

        $query = Pesanan::query();

        // Filter hanya untuk admin biasa
        if (! $isSuperAdmin) {
            $query->whereHas('detailProduk.itemProduksi', function ($q) use ($user) {
                $q->where('id_kategori', (int) $user->id_kategori);
            });
        }

        $pesanan = $query->findOrFail($id);

        $pesanan->update([
            'id_status_pesanan' => $request->id_status_pesanan,
        ]);

        return redirect()
            ->route('admin.tampilPesanan')
            ->with('success', 'Progres pesanan berhasil diperbarui.');
    }
}
