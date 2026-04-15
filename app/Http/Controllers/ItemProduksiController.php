<?php

namespace App\Http\Controllers;

use App\Models\ItemProduksi;
use App\Models\KategoriUsaha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemProduksiController extends Controller
{
    /**
     * Menampilkan seluruh data Item Produksi dan menampilkan halaman index Item Produksi
     */
    public function index()
    {
        $itemProduksi = ItemProduksi::with('kategoriUsaha')->get();
        return view('ItemProduksi.index', compact('itemProduksi'));
    }

    /**
     * menampilkan view tambah data Item Produksi
     */
    public function create()
    {
        $kategoriUsaha = KategoriUsaha::all();
        return view('ItemProduksi.create');
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
        ]);

        // Handle upload gambar
        if ($request->hasFile('gambar_item')) {
            $file = $request->file('gambar_item');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('item_produksi', $filename, 'public');
            $validated['gambar_item'] = 'item_produksi/' . $filename;
        }

        ItemProduksi::create($validated);

        return redirect()->route('ItemProduksi.index')
            ->with('success', 'Item Produksi berhasil ditambahkan');
    }

    /**
     * menampilkan data Item Produksi berdasarkan id dan menampilkan halaman detail Item Produksi
     */
    public function show(ItemProduksi $itemProduksi)
    {
        return view('ItemProduksi.show', compact('itemProduksi'));
    }

    /**
     * menampilkan form edit data Item Produksi berdasarkan id yang dipilih
     */
    public function edit(ItemProduksi $itemProduksi)
    {
        $kategoriUsaha = KategoriUsaha::all();
        return view('ItemProduksi.edit', compact('itemProduksi'));
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

        $itemProduksi->update($validated);

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

        $itemProduksi->delete();

        return redirect()->route('ItemProduksi.index')
            ->with('success', 'Item Produksi berhasil dihapus');
    }
}
