<?php

namespace App\Http\Controllers;

use App\Models\ItemProduksi;
use Illuminate\Http\Request;

class ItemProduksiController extends Controller
{
    /**
     * Menampilkan seluruh data Item Produksi dan menampilkan halaman index Item Produksi
     */
    public function index()
    {
        $itemProduksi = ItemProduksi::all();
        return view('ItemProduksi.index', compact('itemProduksi'));
    }

    /**
     * menampilkan view tambah data Item Produksi
     */
    public function create()
    {
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
            'gambar_item' => 'required|string|max:255',
            'status_aktif' => 'required|in:Aktif,Non-aktif',
        ]);
            

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
            'gambar_item' => 'required|string|max:255',
            'status_aktif' => 'required|in:Aktif,Non-aktif',
        ]);

        $itemProduksi->update($validated);

        return redirect()->route('ItemProduksi.index')
                        ->with('success', 'Item Produksi berhasil diperbarui');
    }

    /**
     * fungsi untuk menghapus data Item Produksi berdasarkan id yang dipilih
     */
    public function destroy(ItemProduksi $itemProduksi)
    {
        $itemProduksi->delete();

        return redirect()->route('ItemProduksi.index')
                        ->with('success', 'Item Produksi berhasil dihapus');
    }
}
