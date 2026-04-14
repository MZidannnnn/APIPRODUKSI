<?php

namespace App\Http\Controllers;

use App\Models\KategoriUsaha;
use Illuminate\Http\Request;

class KategoriUsahaController extends Controller
{
    /**
     * Menampilkan seluruh data divisi dan menampilkan halaman index divisi
     */
    public function index()
    {
        $kategoriUsaha = KategoriUsaha::all();
        return view('KategoriUsaha.index', compact('kategoriUsaha'));
    }

    /**
     * menampilkan view tambah data divisi
     */
    public function create()
    {
        return view('KategoriUsaha.create');
    }

    /**
     * fungsi untuk menyimpan data divisi yang baru dibuat ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_jenis_pembayaran' => 'required|exists:jenis_pembayaran,id_jenis_pembayaran',
            'nama_kategori' => 'required|string|max:100',
            'jenis_harga' => 'required|in:Harga Tetap,Harga Kostum',
            'deskripsi' => 'nullable|string',
        ]);

        KategoriUsaha::create($validated);

        return redirect()->route('KategoriUsaha.index')
                        ->with('success', 'Kategori Usaha berhasil ditambahkan');
    }

    /**
     * menampilkan data divisi berdasarkan id dan menampilkan halaman detail divisi
     */
    public function show(KategoriUsaha $kategoriUsaha)
    {
        return view('KategoriUsaha.show', compact('kategoriUsaha'));
    }

    /**
     * menampilkan form edit data divisi berdasarkan id yang dipilih
     */
    public function edit(KategoriUsaha $kategoriUsaha)
    {
        return view('KategoriUsaha.edit', compact('kategoriUsaha'));
    }

    /**
     * fungsi untuk memperbarui data divisi yang sudah ada di database berdasarkan id yang dipilih
     */
    public function update(Request $request, KategoriUsaha $kategoriUsaha)
    {
        $validated = $request->validate([
            'id_jenis_pembayaran' => 'required|exists:jenis_pembayaran,id_jenis_pembayaran',
            'nama_kategori' => 'required|string|max:100|unique:kategori_usaha,nama_kategori,' . $kategoriUsaha->id_kategori . ',id_kategori',
            'jenis_harga' => 'required|in:Harga Tetap,Harga Kostum',
            'deskripsi' => 'nullable|string',
        ]);

        $kategoriUsaha->update($validated);

        return redirect()->route('KategoriUsaha.index')
                        ->with('success', 'Kategori Usaha berhasil diperbarui');
    }

    /**
     * fungsi untuk menghapus data divisi berdasarkan id yang dipilih
     */
    public function destroy(KategoriUsaha $kategoriUsaha)
    {
        $kategoriUsaha->delete();

        return redirect()->route('KategoriUsaha.index')
                        ->with('success', 'Kategori Usaha berhasil dihapus');
    }
}
