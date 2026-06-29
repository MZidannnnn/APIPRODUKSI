<?php

namespace App\Http\Controllers;

use App\Models\SatuanHarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SatuanHargaController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Satuan Harga',
            'menuDataMaster' => 'active',
            'collapseDataMaster' => 'show',
            'satuanHarga' => SatuanHarga::all(),
            'master' => 'satuanHarga'
        ];

        return view('super-admin/data-master/satuan-harga/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Data Satuan Harga',
            'menuDataMaster' => 'active',
            'collapseDataMaster' => 'show',
            'master' => 'satuanHarga',
        ];
    
        return view('super-admin/data-master/satuan-harga/create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_satuan'          => 'required',
        ], [    
            'nama_satuan.required' => 'Nama satuan harga wajib diisi',
        ]);

        SatuanHarga::create([
            'nama_satuan'  => $request->nama_satuan,
        ]);

        return redirect()
            ->route('satuanHarga.index')
            ->with('success', 'Data satuan harga berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = [
            'title'              => 'Edit Data Satuan Harga',
            'menuDataMaster'     => 'active',
            'collapseDataMaster' => 'show',
            'master'             => 'satuanHarga',
            'satuanHarga'      => SatuanHarga::findOrFail($id),
        ];

        return view('super-admin/data-master/satuan-harga/edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_satuan'         => 'required',
        ], [
            'nama_satuan.required' => 'Nama satuan harga wajib diisi',
        ]);

        $satuanHarga = SatuanHarga::findOrFail($id);

        $satuanHarga->update([
            'nama_satuan'       => $request->nama_satuan,
        ]);

        return redirect()
            ->route('satuanHarga.index')
            ->with('success', 'Data satuan harga berhasil diperbarui');
    }

    public function destroy($id)
    {
        $satuanHarga = SatuanHarga::findOrFail($id);

        DB::beginTransaction();

        try {
            $satuanHarga->delete();

            DB::commit();

            return redirect()
                ->route('satuanHarga.index')
                ->with('success', 'Data satuan harga berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('satuanHarga.index')
                ->with('error', 'Gagal menghapus data satuan harga: ' . $e->getMessage());
        }
    }
}

// /**
//      * Menampilkan seluruh data divisi dan menampilkan halaman index divisi
//      */
//     public function index() 
//     {
//         $satuanHarga = SatuanHarga::all();
//         return view('SatuanHarga.index', compact('satuanHarga'));
//     }

//     /**
//      * menampilkan view tambah data divisi
//      */
//     public function create()
//     {
//         return view('SatuanHarga.create');
//     }

//     /**
//      * fungsi untuk menyimpan data divisi yang baru dibuat ke database
//      */
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'nama_satuan' => 'required|string|max:30|unique:satuan_harga,nama_satuan',
//         ]);

//         SatuanHarga::create($validated);

//         return redirect()->route('SatuanHarga.index')
//                         ->with('success', 'Satuan Harga berhasil ditambahkan');
//     }

//     /**
//      * menampilkan data divisi berdasarkan id dan menampilkan halaman detail divisi
//      */
//     public function show(SatuanHarga $satuanHarga)
//     {
//         return view('SatuanHarga.show', compact('satuanHarga'));
//     }

//     /**
//      * menampilkan form edit data divisi berdasarkan id yang dipilih
//      */
//     public function edit(SatuanHarga $satuanHarga)
//     {
//         return view('SatuanHarga.edit', compact('satuanHarga'));
//     }

//     /**
//      * fungsi untuk memperbarui data divisi yang sudah ada di database berdasarkan id yang dipilih
//      */
//     public function update(Request $request, SatuanHarga $satuanHarga)
//     {
//         $validated = $request->validate([
//             'nama_satuan' => 'required|string|max:30|unique:satuan_harga,nama_satuan,' . $satuanHarga->id_satuan . ',id_satuan',
//         ]);

//         $satuanHarga->update($validated);

//         return redirect()->route('SatuanHarga.index')
//                         ->with('success', 'Satuan Harga berhasil diperbarui');
//     }

//     /**
//      * fungsi untuk menghapus data divisi berdasarkan id yang dipilih
//      */
//     public function destroy(SatuanHarga $satuanHarga)
//     {
//         $satuanHarga->delete();

//         return redirect()->route('SatuanHarga.index')
//                         ->with('success', 'Satuan Harga berhasil dihapus');
//     }