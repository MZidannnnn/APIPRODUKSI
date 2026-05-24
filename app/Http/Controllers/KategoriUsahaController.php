<?php

namespace App\Http\Controllers;

use App\Models\KategoriUsaha;
use App\Models\JenisPembayaran;
use Illuminate\Http\Request;

class KategoriUsahaController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Kategori Usaha',
            'menuDataMaster' => 'active',
            'collapseDataMaster' => 'show',
            'kategori' => KategoriUsaha::get(),
            'master' => 'kategoriUsaha'
        ];

        return view('super-admin/data-master/kategori-usaha/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Data Kategori Usaha',
            'menuDataMaster' => 'active',
            'collapseDataMaster' => 'show',
            'master' => 'kategoriUsaha',
            'jenisPembayaran' => JenisPembayaran::all()
        ];
    
        return view('super-admin/data-master/kategori-usaha/create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori'         => 'required|max:100',
            'id_jenis_pembayaran'   => 'required',
            'jenis_harga'           => 'required|in:Harga Tetap,Harga Kostum',
            'deskripsi'             => 'nullable',
        ], [
            'nama_kategori.required'         => 'Nama kategori usaha wajib diisi',
            'nama_kategori.max'              => 'Nama kategori usaha maksimal 100 karakter',
            'id_jenis_pembayaran.required'   => 'Jenis pembayaran wajib dipilih',
            'jenis_harga.required'           => 'Jenis harga wajib dipilih',
            'jenis_harga.in'                 => 'Jenis harga tidak valid', 
        ]);

        KategoriUsaha::create([
            'id_jenis_pembayaran' => $request->id_jenis_pembayaran,
            'nama_kategori'       => $request->nama_kategori,
            'jenis_harga'         => $request->jenis_harga,
            'deskripsi'           => $request->deskripsi,
        ]);

        return redirect()
            ->route('kategoriUsaha.index')
            ->with('success', 'Data kategori usaha berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = [
            'title'              => 'Edit Data Kategori Usaha',
            'menuDataMaster'     => 'active',
            'collapseDataMaster' => 'show',
            'master'             => 'kategoriUsaha',
            'kategori'           => KategoriUsaha::findOrFail($id),
            'jenisPembayaran'    => JenisPembayaran::orderBy('nama_jenis_pembayaran', 'ASC')->get(),
        ];

        return view('super-admin/data-master/kategori-usaha/edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori'         => 'required|max:100',
            'id_jenis_pembayaran'   => 'required',
            'jenis_harga'           => 'required|in:Harga Tetap,Harga Kostum',
            'deskripsi'             => 'nullable',
        ], [
            'nama_kategori.required'         => 'Nama kategori usaha wajib diisi',
            'nama_kategori.max'              => 'Nama kategori usaha maksimal 100 karakter',
            'id_jenis_pembayaran.required'   => 'Jenis pembayaran wajib dipilih',
            'jenis_harga.required'           => 'Jenis harga wajib dipilih',
            'jenis_harga.in'                 => 'Jenis harga tidak valid',
        ]);

        $kategori = KategoriUsaha::findOrFail($id);

        $kategori->update([
            'id_jenis_pembayaran' => $request->id_jenis_pembayaran,
            'nama_kategori'       => $request->nama_kategori,
            'jenis_harga'         => $request->jenis_harga,
            'deskripsi'           => $request->deskripsi,
        ]);

        return redirect()
            ->route('kategoriUsaha.index')
            ->with('success', 'Data kategori usaha berhasil diperbarui');
    }

    public function destroy($id)
    {
        $kategori = KategoriUsaha::findOrFail($id);
        $kategori->delete();
        return redirect()
            ->route('kategoriUsaha.index')
            ->with('success', 'Data kategori usaha berhasil dihapus');
    }
}

// /**
//      * Menampilkan seluruh data divisi dan menampilkan halaman index divisi
//      */
//     public function index()
//     {
//         $kategoriUsaha = KategoriUsaha::all();
//         return view('KategoriUsaha.index', compact('kategoriUsaha'));
//     }

//     /**
//      * menampilkan view tambah data divisi
//      */
//     public function create()
//     {
//         return view('KategoriUsaha.create');
//     }

//     /**
//      * fungsi untuk menyimpan data divisi yang baru dibuat ke database
//      */
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'id_jenis_pembayaran' => 'required|exists:jenis_pembayaran,id_jenis_pembayaran',
//             'nama_kategori' => 'required|string|max:100',
//             'jenis_harga' => 'required|in:Harga Tetap,Harga Kostum',
//             'deskripsi' => 'nullable|string',
//         ]);

//         KategoriUsaha::create($validated);

//         return redirect()->route('KategoriUsaha.index')
//                         ->with('success', 'Kategori Usaha berhasil ditambahkan');
//     }

//     /**
//      * menampilkan data divisi berdasarkan id dan menampilkan halaman detail divisi
//      */
//     public function show(KategoriUsaha $kategoriUsaha)
//     {
//         return view('KategoriUsaha.show', compact('kategoriUsaha'));
//     }

//     /**
//      * menampilkan form edit data divisi berdasarkan id yang dipilih
//      */
//     public function edit(KategoriUsaha $kategoriUsaha)
//     {
//         return view('KategoriUsaha.edit', compact('kategoriUsaha'));
//     }

//     /**
//      * fungsi untuk memperbarui data divisi yang sudah ada di database berdasarkan id yang dipilih
//      */
//     public function update(Request $request, KategoriUsaha $kategoriUsaha)
//     {
//         $validated = $request->validate([
//             'id_jenis_pembayaran' => 'required|exists:jenis_pembayaran,id_jenis_pembayaran',
//             'nama_kategori' => 'required|string|max:100|unique:kategori_usaha,nama_kategori,' . $kategoriUsaha->id_kategori . ',id_kategori',
//             'jenis_harga' => 'required|in:Harga Tetap,Harga Kostum',
//             'deskripsi' => 'nullable|string',
//         ]);

//         $kategoriUsaha->update($validated);

//         return redirect()->route('KategoriUsaha.index')
//                         ->with('success', 'Kategori Usaha berhasil diperbarui');
//     }

//     /**
//      * fungsi untuk menghapus data divisi berdasarkan id yang dipilih
//      */
//     public function destroy(KategoriUsaha $kategoriUsaha)
//     {
//         $kategoriUsaha->delete();

//         return redirect()->route('KategoriUsaha.index')
//                         ->with('success', 'Kategori Usaha berhasil dihapus');
//     }