<?php

namespace App\Http\Controllers;

use App\Models\DetailProduk;
use App\Models\ItemProduksi;
use App\Models\KategoriUsaha;
use App\Models\SatuanHarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemProduksiController extends Controller
{
    /**
     * Menampilkan seluruh data Item Produksi dan menampilkan halaman index Item Produksi
     */
    public function index()
    {
        $itemProduksi = ItemProduksi::with('kategoriUsaha', 'detailProduk')->get();
        return view('ItemProduksi.index', compact('itemProduksi'));
    }

    /**
     * menampilkan view tambah data Item Produksi
     */
    public function create()
    {
        $kategoriUsaha = KategoriUsaha::all();
        $satuanHarga = SatuanHarga::all();
        return view('ItemProduksi.create', compact('kategoriUsaha', 'satuanHarga'));
    }

    /**
     * fungsi untuk menyimpan data Item Produksi yang baru dibuat ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kategori' => 'required|exists:kategori_usaha,id_kategori',
            'nama_item' => 'required|string|max:100',
            'deskripsi_item' => 'nullable|string',
            'gambar_item' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status_aktif' => 'required|in:Aktif,Non-aktif',

            // detail produk
            'id_satuan' => 'required|exists:satuan_harga,id_satuan',
            'ukuran' => 'required|string|max:50',
            'harga_dasar' => 'required|numeric|min:0',
        ]);

        // Handle upload gambar
        if ($request->hasFile('gambar_item')) {
            $file = $request->file('gambar_item');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('item_produksi', $filename, 'public');
            $validated['gambar_item'] = 'item_produksi/' . $filename;
        }

        // save item produksi
        $itemProduksi = ItemProduksi::create([
            'id_kategori' => $validated['id_kategori'],
            'nama_item' => $validated['nama_item'],
            'deskripsi_item' => $validated['deskripsi_item'],
            'gambar_item' => $validated['gambar_item'] ?? null,
            'status_aktif' => $validated['status_aktif'],
        ]);

        // Save DetailProduk
        DetailProduk::create([
            'id_item_produksi' => $itemProduksi->id_item_produksi,
            'id_satuan' => $validated['id_satuan'],
            'ukuran' => $validated['ukuran'],
            'harga_dasar' => $validated['harga_dasar'],
        ]);

        return redirect()->route('ItemProduksi.index')
            ->with('success', 'Item Produksi berhasil ditambahkan');
    }

    /**
     * menampilkan data Item Produksi berdasarkan id dan menampilkan halaman detail Item Produksi
     */
    public function show(ItemProduksi $itemProduksi)
    {
        $itemProduksi->load('kategoriUsaha', 'detailProduk.satuanHarga');
        return view('ItemProduksi.show', compact('itemProduksi'));
    }

    /**
     * menampilkan form edit data Item Produksi berdasarkan id yang dipilih
     */
    public function edit(ItemProduksi $itemProduksi)
    {
        $itemProduksi->load('detailProduk');
        $kategoriUsaha = KategoriUsaha::all();
        $satuanHarga = SatuanHarga::all();
        return view('ItemProduksi.edit', compact('itemProduksi', 'kategoriUsaha', 'satuanHarga'));
    }

    /**
     * fungsi untuk memperbarui data Item Produksi yang sudah ada di database berdasarkan id yang dipilih
     */
    public function update(Request $request, ItemProduksi $itemProduksi)
    {
        $validated = $request->validate([
            'id_kategori' => 'required|exists:kategori_usaha,id_kategori',
            'nama_item' => 'required|string|max:100',
            'deskripsi_item' => 'nullable|string',
            'gambar_item' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status_aktif' => 'required|in:Aktif,Non-aktif',

            // detail produk
            'id_satuan' => 'required|exists:satuan_harga,id_satuan',
            'ukuran' => 'required|string|max:50',
            'harga_dasar' => 'required|numeric|min:0',
        ]);

        // Untuk update method
        if ($request->hasFile('gambar_item')) {
            // Hapus gambar lama jika ada
            if ($itemProduksi->gambar_item && Storage::disk('public')->exists($itemProduksi->gambar_item)) {
                Storage::disk('public')->delete($itemProduksi->gambar_item);
            }

            $file = $request->file('gambar_item');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('item_produksi', $filename, 'public');
            $validated['gambar_item'] = 'item_produksi/' . $filename;
        }

        // Update ItemProduksi
        $itemProduksi->update([
            'id_kategori' => $validated['id_kategori'],
            'nama_item' => $validated['nama_item'],
            'deskripsi_item' => $validated['deskripsi_item'],
            'status_aktif' => $validated['status_aktif'],
        ]);

        if (isset($validated['gambar_item'])) {
            $itemProduksi->gambar_item = $validated['gambar_item'];
            $itemProduksi->save();
        }

        // Update DetailProduk
        $detailProduk = $itemProduksi->detailProduk;
        if ($detailProduk) {
            $detailProduk->update([
                'id_satuan' => $validated['id_satuan'],
                'ukuran' => $validated['ukuran'],
                'harga_dasar' => $validated['harga_dasar'],
            ]);
        }

        return redirect()->route('ItemProduksi.index')
            ->with('success', 'Item Produksi berhasil diperbarui');
    }

    /**
     * fungsi untuk menghapus data Item Produksi berdasarkan id yang dipilih
     */
    public function destroy(ItemProduksi $itemProduksi)
    {
        // Hapus gambar jika ada
        if ($itemProduksi->gambar_item && Storage::disk('public')->exists($itemProduksi->gambar_item)) {
            Storage::disk('public')->delete($itemProduksi->gambar_item);
        }

        // Hapus DetailProduk terkait jika ada
        if ($itemProduksi->detailProduk) {
            $itemProduksi->detailProduk->delete();
        }

        $itemProduksi->delete();

        return redirect()->route('ItemProduksi.index')
            ->with('success', 'Item Produksi berhasil dihapus');
    }
}
