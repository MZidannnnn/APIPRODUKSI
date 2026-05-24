<?php

namespace App\Http\Controllers;

use App\Models\DetailProduk;
use App\Models\ItemProduksi;
use App\Models\KategoriUsaha; 
use App\Models\SatuanHarga;
use App\Models\FotoProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ItemProduksiController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Produk Jasa',
            'menuDataProduk' => 'active',
            'itemProduksi' => ItemProduksi::with(['kategoriUsaha', 'satuanHarga', 'detailProduk', 'fotoProduk'])->latest()->get(),
        ];

        return view('admin/data-produk-jasa/index', $data);
    }

    public function show($id)
    {
        $itemProduksi = ItemProduksi::with(['detailProduk', 'fotoProduk', 'kategoriUsaha', 'satuanHarga'])->findOrFail($id);

        $data = [
            'title'        => 'Detail Produk Jasa',
            'itemProduksi' => $itemProduksi,
        ];

        return view('admin/data-produk-jasa/show', $data);
    }

    /**
     * Menampilkan halaman tambah data produk & jasa
     */
    public function create()
    {
        $data = [
            'title'         => 'Tambah Data Produk Jasa',
            'kategoriUsaha' => KategoriUsaha::all(),
            'satuanHarga'   => SatuanHarga::all(),
        ];

        return view('admin/data-produk-jasa/create', $data);
    }

    /**
     * Menyimpan data produk & jasa
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_kategori'      => 'required|exists:kategori_usaha,id_kategori',
            'id_satuan'        => 'required|exists:satuan_harga,id_satuan',
            'nama_item'        => 'required|string|max:100',
            'deskripsi_item'   => 'nullable|string',
            'status_aktif'     => 'required|in:Aktif,Non-aktif',

            'ukuran'           => 'nullable|array|min:1',
            'ukuran.*'         => 'nullable|string|max:50',
            'harga_dasar'      => 'required|array|min:1',
            'harga_dasar.*'    => 'required|regex:/^[0-9.]+$/',

            'foto_produk'      => 'required|array|min:1',
            'foto_produk.*'    => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'id_kategori.required'  => 'Kategori usaha wajib dipilih',
            'id_kategori.exists'    => 'Kategori usaha tidak valid',
            'id_satuan.required'    => 'Satuan harga wajib dipilih',
            'id_satuan.exists'      => 'Satuan harga tidak valid',
            'nama_item.required'    => 'Nama produk wajib diisi',
            'nama_item.max'         => 'Nama produk maksimal 100 karakter',
            'status_aktif.required' => 'Status produk wajib dipilih.',
            'status_aktif.in'       => 'Status produk tidak valid.',

            'ukuran.*.max'           => 'Ukuran maksimal 50 karakter',
            'harga_dasar.required'   => 'Harga dasar wajib diisi',
            'harga_dasar.*.required' => 'Harga dasar wajib diisi',
            'harga_dasar.*.regex'    => 'Harga dasar harus berupa angka',
            'harga_dasar.*.min'      => 'Harga dasar tidak boleh kurang dari 0',

            'foto_produk.required'  => 'Foto produk wajib diupload',
            'foto_produk.*.image'   => 'File harus berupa gambar',
            'foto_produk.*.mimes'   => 'Format gambar harus jpg, jpeg, png, atau webp',
            'foto_produk.*.max'     => 'Ukuran gambar maksimal 2 MB',
        ]);

        DB::beginTransaction();

        try {
            $itemProduksi = ItemProduksi::create([
                'id_kategori'    => $request->id_kategori,
                'id_satuan'      => $request->id_satuan,
                'nama_item'      => $request->nama_item,
                'deskripsi_item' => $request->deskripsi_item,
                'status_aktif'   => $request->status_aktif,
            ]);

            foreach ($request->harga_dasar as $index => $hargaDasar) {
                DetailProduk::create([
                    'id_item_produksi' => $itemProduksi->id_item_produksi,
                    'ukuran'           => $request->ukuran[$index] ?? null,
                    'harga_dasar'      => str_replace('.', '', $hargaDasar), // hapus format titik ribuan
                ]);
            }

            if ($request->hasFile('foto_produk')) {
                foreach ($request->file('foto_produk') as $foto) {
                    $namaFile = time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();

                    $foto->move(public_path('uploads/produk'), $namaFile);

                    FotoProduk::create([
                        'id_item_produksi' => $itemProduksi->id_item_produksi,
                        'nama_foto'        => 'uploads/produk/' . $namaFile,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('itemProduksi.index')
                ->with('success', 'Data Produk Jasa Berhasil Ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data');
        }
    }

    /**
     * Menampilkan halaman edit data produk & jasa
     */
    public function edit($id)
    {
        $data = [
            'title' => 'Edit Data Produk Jasa',
            'itemProduksi' => ItemProduksi::with(['detailProduk', 'fotoProduk', 'kategoriUsaha', 'satuanHarga',])->findOrFail($id),
            'kategoriUsaha' => KategoriUsaha::all(),
            'satuanHarga' => SatuanHarga::all(),
        ];

        return view('admin/data-produk-jasa/edit', $data);
    }

    /**
     * Mengupdate data produk & jasa
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_kategori'      => 'required|exists:kategori_usaha,id_kategori',
            'id_satuan'        => 'required|exists:satuan_harga,id_satuan',
            'nama_item'        => 'required|string|max:100',
            'deskripsi_item'   => 'nullable|string',
            'status_aktif'     => 'required|in:Aktif,Non-aktif',

            'ukuran'           => 'nullable|array',
            'ukuran.*'         => 'nullable|string|max:50',

            'harga_dasar'      => 'required|array|min:1',
            'harga_dasar.*'    => 'required|regex:/^[0-9.]+$/',

            'foto_produk'      => 'nullable|array',
            'foto_produk.*'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'id_kategori.required'    => 'Kategori usaha wajib dipilih',
            'id_kategori.exists'      => 'Kategori usaha tidak valid',
            'id_satuan.required'      => 'Satuan harga wajib dipilih',
            'id_satuan.exists'        => 'Satuan harga tidak valid',
            'nama_item.required'      => 'Nama produk wajib diisi',
            'nama_item.max'           => 'Nama produk maksimal 100 karakter',
            'status_aktif.required'   => 'Status produk wajib dipilih',
            'status_aktif.in'         => 'Status produk tidak valid',

            'ukuran.*.max'            => 'Ukuran maksimal 50 karakter',
            'harga_dasar.required'    => 'Harga dasar wajib diisi',
            'harga_dasar.*.required'  => 'Harga dasar wajib diisi',
            'harga_dasar.*.regex'     => 'Harga dasar harus berupa angka',

            'foto_produk.*.image'     => 'File harus berupa gambar',
            'foto_produk.*.mimes'     => 'Format gambar harus jpg, jpeg, png, atau webp',
            'foto_produk.*.max'       => 'Ukuran gambar maksimal 2 MB',
        ]);

        DB::beginTransaction();

        try {
            $itemProduksi = ItemProduksi::findOrFail($id);

            $itemProduksi->update([
                'id_kategori'    => $request->id_kategori,
                'id_satuan'      => $request->id_satuan,
                'nama_item'      => $request->nama_item,
                'deskripsi_item' => $request->deskripsi_item,
                'status_aktif'   => $request->status_aktif,
            ]);

            // hapus detail lama
            DetailProduk::where('id_item_produksi', $itemProduksi->id_item_produksi)->delete();

            // simpan detail baru
            foreach ($request->harga_dasar as $index => $hargaDasar) {
                DetailProduk::create([
                    'id_item_produksi' => $itemProduksi->id_item_produksi,
                    'ukuran'           => $request->ukuran[$index] ?? null,
                    'harga_dasar'      => str_replace('.', '', $hargaDasar),
                ]);
            }

            // hapus foto lama jika ada yang dipilih untuk dihapus
            if ($request->has('hapus_foto')) {
                $fotoLama = FotoProduk::whereIn('id_foto_produk', $request->hapus_foto)
                    ->where('id_item_produksi', $itemProduksi->id_item_produksi)
                    ->get();

                foreach ($fotoLama as $foto) {
                    if (file_exists(public_path($foto->nama_foto))) {
                        unlink(public_path($foto->nama_foto));
                    }

                    $foto->delete();
                }
            }
            // tambah foto baru jika ada
            if ($request->hasFile('foto_produk')) {
                foreach ($request->file('foto_produk') as $foto) {
                    $namaFile = time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();

                    $foto->move(public_path('uploads/produk'), $namaFile);

                    FotoProduk::create([
                        'id_item_produksi' => $itemProduksi->id_item_produksi,
                        'nama_foto'        => 'uploads/produk/' . $namaFile,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('itemProduksi.index')
                ->with('success', 'Data Produk Jasa berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data');
        }
    }

    /**
     * Menghapus data produk & jasa
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // 1. Cari data item produksi yang mau dihapus beserta relasi fotonya
            $itemProduksi = ItemProduksi::with('fotoProduk')->findOrFail($id);

            // 2. Hapus terlebih dahulu file foto fisik yang ada di folder public
            foreach ($itemProduksi->fotoProduk as $foto) {
                $pathFotoFisik = public_path($foto->nama_foto);
                
                // Cek apakah file fisiknya beneran ada, jika ada langsung hapus dari storage
                if (file_exists($pathFotoFisik)) {
                    unlink($pathFotoFisik);
                }
            }

            // 3. Hapus data utama dari tabel 'item_produksi'
            // Karena CASCADE, baris di tabel 'detail_produk' dan 'foto_produk' otomatis ikut terhapus di DB
            $itemProduksi->delete();

            DB::commit();

            // 4. Kembalikan ke halaman index dengan membawa alert sukses
            return redirect()->route('itemProduksi.index')
                            ->with('success', 'Data Produk Jasa Berhasil Dihapus');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('itemProduksi.index')
                            ->with('error', 'Gagal Menghapus Data: ' . $e->getMessage());
        }
    }
}

// /**
//      * Menampilkan seluruh data Item Produksi dan menampilkan halaman index Item Produksi
//      */
//     public function index()
//     {
//         $itemProduksi = ItemProduksi::with('kategoriUsaha', 'detailProduk')->get();
//         return view('ItemProduksi.index', compact('itemProduksi'));
//     }

//     /**
//      * menampilkan view tambah data Item Produksi
//      */
//     public function create()
//     {
//         $kategoriUsaha = KategoriUsaha::all();
//         $satuanHarga = SatuanHarga::all();
//         return view('ItemProduksi.create', compact('kategoriUsaha', 'satuanHarga'));
//     }

//     /**
//      * fungsi untuk menyimpan data Item Produksi yang baru dibuat ke database
//      */
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'id_kategori' => 'required|exists:kategori_usaha,id_kategori',
//             'nama_item' => 'required|string|max:100',
//             'deskripsi_item' => 'nullable|string',
//             'gambar_item' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
//             'status_aktif' => 'required|in:Aktif,Non-aktif',

//             // detail produk
//             'id_satuan' => 'required|exists:satuan_harga,id_satuan',
//             'ukuran' => 'required|string|max:50',
//             'harga_dasar' => 'required|numeric|min:0',
//         ]);

//         // Handle upload gambar
//         if ($request->hasFile('gambar_item')) {
//             $file = $request->file('gambar_item');
//             $filename = time() . '_' . $file->getClientOriginalName();
//             $file->storeAs('item_produksi', $filename, 'public');
//             $validated['gambar_item'] = 'item_produksi/' . $filename;
//         }

//         // save item produksi
//         $itemProduksi = ItemProduksi::create([
//             'id_kategori' => $validated['id_kategori'],
//             'nama_item' => $validated['nama_item'],
//             'deskripsi_item' => $validated['deskripsi_item'],
//             'gambar_item' => $validated['gambar_item'] ?? null,
//             'status_aktif' => $validated['status_aktif'],
//         ]);

//         // Save DetailProduk
//         DetailProduk::create([
//             'id_item_produksi' => $itemProduksi->id_item_produksi,
//             'id_satuan' => $validated['id_satuan'],
//             'ukuran' => $validated['ukuran'],
//             'harga_dasar' => $validated['harga_dasar'],
//         ]);

//         return redirect()->route('ItemProduksi.index')
//             ->with('success', 'Item Produksi berhasil ditambahkan');
//     }

//     /**
//      * menampilkan data Item Produksi berdasarkan id dan menampilkan halaman detail Item Produksi
//      */
//     public function show(ItemProduksi $itemProduksi)
//     {
//         $itemProduksi->load('kategoriUsaha', 'detailProduk.satuanHarga');
//         return view('ItemProduksi.show', compact('itemProduksi'));
//     }

//     /**
//      * menampilkan form edit data Item Produksi berdasarkan id yang dipilih
//      */
//     public function edit(ItemProduksi $itemProduksi)
//     {
//         $itemProduksi->load('detailProduk');
//         $kategoriUsaha = KategoriUsaha::all();
//         $satuanHarga = SatuanHarga::all();
//         return view('ItemProduksi.edit', compact('itemProduksi', 'kategoriUsaha', 'satuanHarga'));
//     }

//     /**
//      * fungsi untuk memperbarui data Item Produksi yang sudah ada di database berdasarkan id yang dipilih
//      */
//     public function update(Request $request, ItemProduksi $itemProduksi)
//     {
//         $validated = $request->validate([
//             'id_kategori' => 'required|exists:kategori_usaha,id_kategori',
//             'nama_item' => 'required|string|max:100',
//             'deskripsi_item' => 'nullable|string',
//             'gambar_item' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
//             'status_aktif' => 'required|in:Aktif,Non-aktif',

//             // detail produk
//             'id_satuan' => 'required|exists:satuan_harga,id_satuan',
//             'ukuran' => 'required|string|max:50',
//             'harga_dasar' => 'required|numeric|min:0',
//         ]);

//         // Untuk update method
//         if ($request->hasFile('gambar_item')) {
//             // Hapus gambar lama jika ada
//             if ($itemProduksi->gambar_item && Storage::disk('public')->exists($itemProduksi->gambar_item)) {
//                 Storage::disk('public')->delete($itemProduksi->gambar_item);
//             }

//             $file = $request->file('gambar_item');
//             $filename = time() . '_' . $file->getClientOriginalName();
//             $file->storeAs('item_produksi', $filename, 'public');
//             $validated['gambar_item'] = 'item_produksi/' . $filename;
//         }

//         // Update ItemProduksi
//         $itemProduksi->update([
//             'id_kategori' => $validated['id_kategori'],
//             'nama_item' => $validated['nama_item'],
//             'deskripsi_item' => $validated['deskripsi_item'],
//             'status_aktif' => $validated['status_aktif'],
//         ]);

//         if (isset($validated['gambar_item'])) {
//             $itemProduksi->gambar_item = $validated['gambar_item'];
//             $itemProduksi->save();
//         }

//         // Update DetailProduk
//         $detailProduk = $itemProduksi->detailProduk;
//         if ($detailProduk) {
//             $detailProduk->update([
//                 'id_satuan' => $validated['id_satuan'],
//                 'ukuran' => $validated['ukuran'],
//                 'harga_dasar' => $validated['harga_dasar'],
//             ]);
//         }

//         return redirect()->route('ItemProduksi.index')
//             ->with('success', 'Item Produksi berhasil diperbarui');
//     }

//     /**
//      * fungsi untuk menghapus data Item Produksi berdasarkan id yang dipilih
//      */
//     public function destroy(ItemProduksi $itemProduksi)
//     {
//         // Hapus gambar jika ada
//         if ($itemProduksi->gambar_item && Storage::disk('public')->exists($itemProduksi->gambar_item)) {
//             Storage::disk('public')->delete($itemProduksi->gambar_item);
//         }

//         // Hapus DetailProduk terkait jika ada
//         if ($itemProduksi->detailProduk) {
//             $itemProduksi->detailProduk->delete();
//         }

//         $itemProduksi->delete();

//         return redirect()->route('ItemProduksi.index')
//             ->with('success', 'Item Produksi berhasil dihapus');
//     }
