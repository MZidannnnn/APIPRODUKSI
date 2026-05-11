<?php

namespace App\Http\Controllers;

use App\Models\DetailProduk;
use App\Models\ItemProduksi;
use App\Models\Pengguna;
use App\Models\PersetujuanHarga;
use App\Models\Pesanan;
use App\Models\RincianPesanan;
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
            'kuantitas' => 'required|integer|min:1',
            'barang_disediakan_usah' => 'required|in:Ya,Tidak',
            'file_desain' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'jasa_sablon' => 'nullable|boolean',
        ]);

        $detail = DetailProduk::findOrFail($validated['id_detail_produk']);

        $hargaAwal = $detail->harga_dasar * $validated['kuantitas'];
        if ($validated['barang_disediakan_usah'] === 'Tidak') {
            $hargaAwal -= 10000; // contoh diskon, sesuaikan aturan Anda
        }

        $statusMenunggu = StatusPesanan::where('nama_status_pesanan', 'Menunggu Persetujuan Harga')->first();

        $pesanan = Pesanan::create([
            'id_pengguna' => Auth::id(),
            'id_detail_produk' => $detail->id_detail_produk,
            'id_status_pesanan' => $statusMenunggu ? $statusMenunggu->id_status_pesanan : 1,
            'tanggal_pesan' => now()->toDateString(),
            'nama_penerima' => $validated['nama_penerima'],
            'alamat_penerima' => $validated['alamat_penerima'],
            'No_hp_penerima' => $validated['No_hp_penerima'],
            'total_harga' => $hargaAwal,
        ]);

        $fileDesainPath = null;
        if ($request->hasFile('file_desain')) {
            $fileDesainPath = $request->file('file_desain')->store('desain', 'public');
        }

        RincianPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'id_detail_produk' => $detail->id_detail_produk,
            'kuantitas' => $validated['kuantitas'],
            'subtotal' => $hargaAwal,
            'barang_disediakan_usah' => $validated['barang_disediakan_usah'],
            'file_desain' => $fileDesainPath,
            'opsi' => [
                'jasa_sablon' => (bool) ($validated['jasa_sablon'] ?? false),
            ],
        ]);

        PersetujuanHarga::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'harga_awal' => $hargaAwal,
            'status_persetujuan' => 'Menunggu',
        ]);

        return response()->json([
            'message' => 'Pesanan dibuat, menunggu penawaran admin',
            'id_pesanan' => $pesanan->id_pesanan,
        ]);
    }

    // GET /pesanan/{pesanan}
    public function shows(Pesanan $pesanan)
    {
        $pesanan->load('detailProduk', 'statusPesanan');
        return response()->json($pesanan);
    }

    public function showList()
    {
        $itemProduksi = ItemProduksi::with('kategoriUsaha', 'detailProduk.satuanHarga')->where('status_aktif', 'Aktif')->get();
        return view('test.list_item', compact('itemProduksi'));
    }

    public function showLisDetail($id)
    {
        $itemProduksi = ItemProduksi::with('kategoriUsaha.jenisPembayaran', 'detailProduk.satuanHarga')->findOrFail($id);
        return view('test.detail-produk', compact('itemProduksi'));
    }

    public function showTagihan(Pesanan $pesanan)
    {
        $pesanan->load('statusPesanan', 'pembayaran');
        return view('test.tagihan-dp', compact('pesanan'));
    }
}
