<?php

namespace App\Http\Controllers;

use App\Models\SatuanHarga;
use Illuminate\Http\Request;

class SatuanHargaController extends Controller
{
    /**
     * Menampilkan seluruh data divisi dan menampilkan halaman index divisi
     */
    public function index()
    {
        $satuanHarga = SatuanHarga::all();
        return view('SatuanHarga.index', compact('satuanHarga'));
    }

    /**
     * menampilkan view tambah data divisi
     */
    public function create()
    {
        return view('SatuanHarga.create');
    }

    /**
     * fungsi untuk menyimpan data divisi yang baru dibuat ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:30|unique:satuan_harga,nama_satuan',
        ]);

        SatuanHarga::create($validated);

        return redirect()->route('SatuanHarga.index')
                        ->with('success', 'Satuan Harga berhasil ditambahkan');
    }

    /**
     * menampilkan data divisi berdasarkan id dan menampilkan halaman detail divisi
     */
    public function show(SatuanHarga $satuanHarga)
    {
        return view('SatuanHarga.show', compact('satuanHarga'));
    }

    /**
     * menampilkan form edit data divisi berdasarkan id yang dipilih
     */
    public function edit(SatuanHarga $satuanHarga)
    {
        return view('SatuanHarga.edit', compact('satuanHarga'));
    }

    /**
     * fungsi untuk memperbarui data divisi yang sudah ada di database berdasarkan id yang dipilih
     */
    public function update(Request $request, SatuanHarga $satuanHarga)
    {
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:30|unique:satuan_harga,nama_satuan,' . $satuanHarga->id_satuan . ',id_satuan',
        ]);

        $satuanHarga->update($validated);

        return redirect()->route('SatuanHarga.index')
                        ->with('success', 'Satuan Harga berhasil diperbarui');
    }

    /**
     * fungsi untuk menghapus data divisi berdasarkan id yang dipilih
     */
    public function destroy(SatuanHarga $satuanHarga)
    {
        $satuanHarga->delete();

        return redirect()->route('SatuanHarga.index')
                        ->with('success', 'Satuan Harga berhasil dihapus');
    }
}
