<?php

namespace App\Http\Controllers;

use App\Models\StatusPesanan;
use Illuminate\Http\Request;

class StatusPesananController extends Controller
{
    /**
     * Menampilkan seluruh data divisi dan menampilkan halaman index divisi
     */
    public function index()
    {
        $statusPesanan = StatusPesanan::all();
        return view('StatusPesanan.index', compact('statusPesanan'));
    }

    /**
     * menampilkan view tambah data divisi
     */
    public function create()
    {
        return view('StatusPesanan.create');
    }

    /**
     * fungsi untuk menyimpan data divisi yang baru dibuat ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_status_pesanan' => 'required|string|max:30|unique:status_pesanan,nama_status_pesanan',
        ]);

        StatusPesanan::create($validated);

        return redirect()->route('StatusPesanan.index')
                        ->with('success', 'Status Pesanan berhasil ditambahkan');
    }

    /**
     * menampilkan data divisi berdasarkan id dan menampilkan halaman detail divisi
     */
    public function show(StatusPesanan $statusPesanan)
    {
        return view('StatusPesanan.show', compact('statusPesanan'));
    }

    /**
     * menampilkan form edit data divisi berdasarkan id yang dipilih
     */
    public function edit(StatusPesanan $statusPesanan)
    {
        return view('StatusPesanan.edit', compact('statusPesanan'));
    }

    /**
     * fungsi untuk memperbarui data divisi yang sudah ada di database berdasarkan id yang dipilih
     */
    public function update(Request $request, StatusPesanan $statusPesanan)
    {
        $validated = $request->validate([
            'nama_status_pesanan' => 'required|string|max:30|unique:status_pesanan,nama_status_pesanan,' . $statusPesanan->id_status_pesanan . ',id_status_pesanan',
        ]);

        $statusPesanan->update($validated);

        return redirect()->route('StatusPesanan.index')
                        ->with('success', 'Status Pesanan berhasil diperbarui');
    }

    /**
     * fungsi untuk menghapus data divisi berdasarkan id yang dipilih
     */
    public function destroy(StatusPesanan $statusPesanan)
    {
        $statusPesanan->delete();

        return redirect()->route('StatusPesanan.index')
                        ->with('success', 'Status Pesanan berhasil dihapus');
    }
}
