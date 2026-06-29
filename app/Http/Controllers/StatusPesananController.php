<?php

namespace App\Http\Controllers;

use App\Models\StatusPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class StatusPesananController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Status Pesanan',
            'menuDataMaster' => 'active',
            'collapseDataMaster' => 'show',
            'statusPesanan' => StatusPesanan::get(),
            'master' => 'statusPesanan' 
        ];

        return view('super-admin/data-master/status-pesanan/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Data Status Pesanan',
            'menuDataMaster' => 'active',
            'collapseDataMaster' => 'show',
            'master' => 'statusPesanan',
        ];
    
        return view('super-admin/data-master/status-pesanan/create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_status_pesanan'          => 'required',
        ], [    
            'nama_status_pesanan.required' => 'Nama status pesanan wajib diisi',
        ]);

        StatusPesanan::create([
            'nama_status_pesanan'  => $request->nama_status_pesanan,
        ]);

        return redirect()
            ->route('statusPesanan.index')
            ->with('success', 'Data status pesanan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = [
            'title'              => 'Edit Data Status Pesanan',
            'menuDataMaster'     => 'active',
            'collapseDataMaster' => 'show',
            'master'             => 'statusPesanan',
            'statusPesanan'      => StatusPesanan::findOrFail($id),
        ];

        return view('super-admin/data-master/status-pesanan/edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_status_pesanan'  => 'required',
        ], [
            'nama_status_pesanan.required'  => 'Nama status pesanan wajib diisi',
        ]);
        $statusPesanan = StatusPesanan::findOrFail($id);

        $statusPesanan->update([
            'nama_status_pesanan'  => $request->nama_status_pesanan,
        ]);

        return redirect()
            ->route('statusPesanan.index')
            ->with('success', 'Data status pesanan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $statusPesanan = StatusPesanan::findOrFail($id);

        DB::beginTransaction();

        try {
            $statusPesanan->delete();

            DB::commit();

            return redirect()
                ->route('statusPesanan.index')
                ->with('success', 'Data status pesanan berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('statusPesanan.index')
                ->with('error', 'Gagal menghapus data status pesanan: ' . $e->getMessage());
        }
    }
}

// /**
//      * Menampilkan seluruh data divisi dan menampilkan halaman index divisi
//      */
//     public function index()
//     {
//         $statusPesanan = StatusPesanan::all();
//         return view('StatusPesanan.index', compact('statusPesanan'));
//     }

//     /**
//      * menampilkan view tambah data divisi
//      */
//     public function create()
//     {
//         return view('StatusPesanan.create');
//     }

//     /**
//      * fungsi untuk menyimpan data divisi yang baru dibuat ke database
//      */
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'nama_status_pesanan' => 'required|string|max:30|unique:status_pesanan,nama_status_pesanan',
//         ]);

//         StatusPesanan::create($validated);

//         return redirect()->route('StatusPesanan.index')
//                         ->with('success', 'Status Pesanan berhasil ditambahkan');
//     }

//     /**
//      * menampilkan data divisi berdasarkan id dan menampilkan halaman detail divisi
//      */
//     public function show(StatusPesanan $statusPesanan)
//     {
//         return view('StatusPesanan.show', compact('statusPesanan'));
//     }

//     /**
//      * menampilkan form edit data divisi berdasarkan id yang dipilih
//      */
//     public function edit(StatusPesanan $statusPesanan)
//     {
//         return view('StatusPesanan.edit', compact('statusPesanan'));
//     }

//     /**
//      * fungsi untuk memperbarui data divisi yang sudah ada di database berdasarkan id yang dipilih
//      */
//     public function update(Request $request, StatusPesanan $statusPesanan)
//     {
//         $validated = $request->validate([
//             'nama_status_pesanan' => 'required|string|max:30|unique:status_pesanan,nama_status_pesanan,' . $statusPesanan->id_status_pesanan . ',id_status_pesanan',
//         ]);

//         $statusPesanan->update($validated);

//         return redirect()->route('StatusPesanan.index')
//                         ->with('success', 'Status Pesanan berhasil diperbarui');
//     }

//     /**
//      * fungsi untuk menghapus data divisi berdasarkan id yang dipilih
//      */
//     public function destroy(StatusPesanan $statusPesanan)
//     {
//         $statusPesanan->delete();

//         return redirect()->route('StatusPesanan.index')
//                         ->with('success', 'Status Pesanan berhasil dihapus');
//     }
