<?php

namespace App\Http\Controllers;

use App\Models\DetailProduk;
use App\Models\Pengguna;
use App\Models\Pesanan;
use App\Models\StatusPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    /**
     * Menampilkan seluruh data pesanan dan menampilkan halaman index pesanan
     */
    public function index()
    {
        $pesanan = Pesanan::with('pengguna', 'statusPesanan')->get();
        return view('Pesanan.index', compact('pesanan'));
    }

    /**
     * Menampilkan view tambah data pesanan
     */
    public function create()
    {
        $pengguna = Pengguna::all();
        $statusPesanan = StatusPesanan::all();
        return view('Pesanan.create', compact('pengguna', 'statusPesanan'));
    }

    /**
     * Fungsi untuk menyimpan data pesanan yang baru dibuat ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'id_status_pesanan' => 'required|exists:status_pesanan,id_status_pesanan',
            'tanggal_pesan' => 'required|date',
            'nama_penerima' => 'required|string|max:100',
            'alamat_penerima' => 'required|string',
            'No_hp_penerima' => 'required|string|max:20',
            'total_harga' => 'required|numeric|min:0',
        ]);

        Pesanan::create($validated);

        return redirect()->route('pesanan.index')
                        ->with('success', 'Pesanan berhasil ditambahkan');
    }

    /**
     * Menampilkan data pesanan berdasarkan id dan menampilkan halaman detail pesanan
     */
    public function show(Pesanan $pesanan)
    {
        $pesanan->load('pengguna', 'statusPesanan');
        return view('Pesanan.show', compact('pesanan'));
    }

    /**
     * Menampilkan form edit data pesanan berdasarkan id yang dipilih
     */
    public function edit(Pesanan $pesanan)
    {
        $pengguna = Pengguna::all();
        $statusPesanan = StatusPesanan::all();
        return view('Pesanan.edit', compact('pesanan', 'pengguna', 'statusPesanan'));
    }

    /**
     * Fungsi untuk memperbarui data pesanan yang sudah ada di database berdasarkan id yang dipilih
     */
    public function update(Request $request, Pesanan $pesanan)
    {
        $validated = $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'id_status_pesanan' => 'required|exists:status_pesanan,id_status_pesanan',
            'tanggal_pesan' => 'required|date',
            'nama_penerima' => 'required|string|max:100',
            'alamat_penerima' => 'required|string',
            'No_hp_penerima' => 'required|string|max:20',
            'total_harga' => 'required|numeric|min:0',
        ]);

        $pesanan->update($validated);

        return redirect()->route('pesanan.index')
                        ->with('success', 'Pesanan berhasil diperbarui');
    }

    /**
     * Fungsi untuk menghapus data pesanan berdasarkan id yang dipilih
     */
    public function destroy(Pesanan $pesanan)
    {
        $pesanan->delete();

        return redirect()->route('pesanan.index')
                        ->with('success', 'Pesanan berhasil dihapus');
    }

    // POST /pesanan/beli
    public function beliSekarang(Request $request)
    {
        $validated = $request->validate([
            'id_detail_produk' => 'required|exists:detail_produk,id_detail_produk',
            'nama_penerima' => 'required|string|max:100',
            'alamat_penerima' => 'required|string',
            'No_hp_penerima' => 'required|string|max:20',
        ]);

        $detail = DetailProduk::findOrFail($validated['id_detail_produk']);

        // sesuaikan ID status "Menunggu Pembayaran"
        $statusMenunggu = StatusPesanan::where('nama_status_pesanan', 'Menunggu Pembayaran')->first();

        $pesanan = Pesanan::create([
            'id_pengguna' => Auth::id(), // pastikan guard sesuai
            'id_detail_produk' => $detail->id_detail_produk,
            'id_status_pesanan' => $statusMenunggu ? $statusMenunggu->id_status_pesanan : 1,
            'tanggal_pesan' => now()->toDateString(),
            'nama_penerima' => $validated['nama_penerima'],
            'alamat_penerima' => $validated['alamat_penerima'],
            'No_hp_penerima' => $validated['No_hp_penerima'],
            'total_harga' => $detail->harga_dasar,
        ]);

        return response()->json([
            'message' => 'Pesanan dibuat',
            'id_pesanan' => $pesanan->id_pesanan,
        ]);
    }

    // GET /pesanan/{pesanan}
    public function shows(Pesanan $pesanan)
    {
        $pesanan->load('detailProduk', 'statusPesanan');
        return response()->json($pesanan);
    }
}