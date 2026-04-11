<?php

namespace App\Http\Controllers;

use App\Models\JenisPembayaran;
use Illuminate\Http\Request;

class JenisPembayaranController extends Controller
{
    /**
     * Menampilkan seluruh data divisi dan menampilkan halaman index divisi
     */
    public function index()
    {
        $jenisPembayaran = JenisPembayaran::all();
        return view('JenisPembayaran.index', compact('jenisPembayaran'));
    }

    /**
     * menampilkan view tambah data divisi
     */
    public function create()
    {
        return view('JenisPembayaran.create');
    }

    /**
     * fungsi untuk menyimpan data divisi yang baru dibuat ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jenis_pembayaran' => 'required|string|max:30|unique:jenis_pembayaran,nama_jenis_pembayaran',
        ]);

        JenisPembayaran::create($validated);

        return redirect()->route('JenisPembayaran.index')
                        ->with('success', 'Jenis Pembayaran berhasil ditambahkan');
    }

    /**
     * menampilkan data divisi berdasarkan id dan menampilkan halaman detail divisi
     */
    public function show(JenisPembayaran $jenisPembayaran)
    {
        return view('JenisPembayaran.show', compact('jenisPembayaran'));
    }

    /**
     * menampilkan form edit data divisi berdasarkan id yang dipilih
     */
    public function edit(JenisPembayaran $jenisPembayaran)
    {
        return view('JenisPembayaran.edit', compact('jenisPembayaran'));
    }

    /**
     * fungsi untuk memperbarui data divisi yang sudah ada di database berdasarkan id yang dipilih
     */
    public function update(Request $request, JenisPembayaran $jenisPembayaran)
    {
        $validated = $request->validate([
            'nama_jenis_pembayaran' => 'required|string|max:30|unique:jenis_pembayaran,nama_jenis_pembayaran,' . $jenisPembayaran->id_jenis_pembayaran . ',id_jenis_pembayaran',
        ]);

        $jenisPembayaran->update($validated);

        return redirect()->route('JenisPembayaran.index')
                        ->with('success', 'Jenis Pembayaran berhasil diperbarui');
    }

    /**
     * fungsi untuk menghapus data divisi berdasarkan id yang dipilih
     */
    public function destroy(JenisPembayaran $jenisPembayaran)
    {
        $jenisPembayaran->delete();

        return redirect()->route('JenisPembayaran.index')
                        ->with('success', 'Jenis Pembayaran berhasil dihapus');
    }
}
