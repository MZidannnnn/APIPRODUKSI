<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;

class DivisiController extends Controller
{
    /**
     * Menampilkan seluruh data divisi dan menampilkan halaman index divisi
     */
    public function index()
    {
        $divisi = Divisi::all();
        return view('divisi.index', compact('divisi'));
    }

    /**
     * menampilkan view tambah data divisi
     */
    public function create()
    {
        return view('divisi.create');
    }

    /**
     * fungsi untuk menyimpan data divisi yang baru dibuat ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_divisi' => 'required|string|max:30|unique:divisi,nama_divisi',
        ]);

        Divisi::create($validated);

        return redirect()->route('divisi.index')
                        ->with('success', 'Divisi berhasil ditambahkan');
    }

    /**
     * menampilkan data divisi berdasarkan id dan menampilkan halaman detail divisi
     */
    public function show(Divisi $divisi)
    {
        return view('divisi.show', compact('divisi'));
    }

    /**
     * menampilkan form edit data divisi berdasarkan id yang dipilih
     */
    public function edit(Divisi $divisi)
    {
        return view('divisi.edit', compact('divisi'));
    }

    /**
     * fungsi untuk memperbarui data divisi yang sudah ada di database berdasarkan id yang dipilih
     */
    public function update(Request $request, Divisi $divisi)
    {
        $validated = $request->validate([
            'nama_divisi' => 'required|string|max:30|unique:divisi,nama_divisi,' . $divisi->id_divisi . ',id_divisi',
        ]);

        $divisi->update($validated);

        return redirect()->route('divisi.index')
                        ->with('success', 'Divisi berhasil diperbarui');
    }

    /**
     * fungsi untuk menghapus data divisi berdasarkan id yang dipilih
     */
    public function destroy(Divisi $divisi)
    {
        $divisi->delete();

        return redirect()->route('divisi.index')
                        ->with('success', 'Divisi berhasil dihapus');
    }
}
