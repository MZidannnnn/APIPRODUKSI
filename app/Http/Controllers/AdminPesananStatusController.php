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
    public function editStatusPesanan(Pesanan $pesanan)
    {
        Gate::authorize('updateStatus', $pesanan);

        $pesanan->load([
            'detailProduk.itemProduksi.kategoriUsaha',
            'statusPesanan',
            'pengguna',
        ]);

        $statusPesanan = StatusPesanan::select('id_status_pesanan', 'nama_status_pesanan')
            ->orderBy('nama_status_pesanan')
            ->get();

        return view('test.view-admin-edit-status-pesanan', compact('pesanan', 'statusPesanan'));
    }

    public function updateStatusPesanan(UpdateStatusPesananRequest $request, Pesanan $pesanan)
    {
        $pesanan->update($request->validated());

        return redirect()
            ->route('admin.editStatusPesanan', $pesanan)
            ->with('success', 'Status pesanan berhasil diperbarui');
    }

    public function tampilAdminPesanan()
    {
        Gate::authorize('viewAny', Pesanan::class);

        $admin = Auth::user();

        $pesanan = Pesanan::query()
            ->select([
                'id_pesanan',
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
            ])
            ->whereHas('detailProduk.itemProduksi', function ($q) use ($admin) {
                $q->where('id_kategori', (int) $admin->id_kategori);
            })
            ->orderByDesc('tanggal_pesan')
            ->paginate(10)
            ->withQueryString();

        return view('test.list-admin-pesanan', compact('pesanan'));
    }
}
