<?php

namespace App\Http\Controllers;

use App\Models\DetailProduk;
use App\Models\ItemProduksi;
use App\Models\Pengguna;
use App\Models\Percakapan;
use App\Models\PersetujuanHarga;
use App\Models\Pesanan;
use App\Models\RincianPesanan;
use App\Models\StatusPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PesananController extends Controller
{
    /**
     * Menampilkan seluruh data pesanan dan menampilkan halaman index pesanan
     */
    public function index()
    {
        $pesanan = Pesanan::with('pengguna', 'statusPesanan')->get();
        return view('Pesanan.index', compact('pesanan'));
    }

    /**
     * Menampilkan view tambah data pesanan
     */
    public function create()
    {
        $pengguna = Pengguna::all();
        $statusPesanan = StatusPesanan::all();
        return view('Pesanan.create', compact('pengguna', 'statusPesanan'));
    }

    /**
     * Fungsi untuk menyimpan data pesanan yang baru dibuat ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'id_status_pesanan' => 'required|exists:status_pesanan,id_status_pesanan',
            'tanggal_pesan' => 'required|date',
            'nama_penerima' => 'required|string|max:100',
            'alamat_penerima' => 'required|string',
            'No_hp_penerima' => 'required|string|max:20',
            'total_harga' => 'required|numeric|min:0',
            'jadwal_pemasangan' => 'nullable|date',
        ]);

        Pesanan::create($validated);

        return redirect()->route('pesanan.index')
            ->with('success', 'Pesanan berhasil ditambahkan');
    }

    /**
     * Menampilkan data pesanan berdasarkan id dan menampilkan halaman detail pesanan
     */
    public function show(Pesanan $pesanan)
    {
        $pesanan->load('pengguna', 'statusPesanan');
        return view('Pesanan.show', compact('pesanan'));
    }

    /**
     * Menampilkan form edit data pesanan berdasarkan id yang dipilih
     */
    public function edit(Pesanan $pesanan)
    {
        $pengguna = Pengguna::all();
        $statusPesanan = StatusPesanan::all();
        return view('Pesanan.edit', compact('pesanan', 'pengguna', 'statusPesanan'));
    }

    /**
     * Fungsi untuk memperbarui data pesanan yang sudah ada di database berdasarkan id yang dipilih
     */
    public function update(Request $request, Pesanan $pesanan)
    {
        $validated = $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'id_status_pesanan' => 'required|exists:status_pesanan,id_status_pesanan',
            'tanggal_pesan' => 'required|date',
            'nama_penerima' => 'required|string|max:100',
            'alamat_penerima' => 'required|string',
            'No_hp_penerima' => 'required|string|max:20',
            'total_harga' => 'required|numeric|min:0',
            'jadwal_pemasangan' => 'nullable|date',
        ]);

        $pesanan->update($validated);

        return redirect()->route('pesanan.index')
            ->with('success', 'Pesanan berhasil diperbarui');
    }

    /**
     * Fungsi untuk menghapus data pesanan berdasarkan id yang dipilih
     */
    public function destroy(Pesanan $pesanan)
    {
        $pesanan->delete();

        return redirect()->route('pesanan.index')
            ->with('success', 'Pesanan berhasil dihapus');
    }

    // POST /pesanan/beli
    public function beliSekarang(Request $request)
    {
        $base = $request->validate([
            'id_detail_produk' => 'required|exists:detail_produk,id_detail_produk',
            'nama_penerima' => 'required|string|max:100',
            'alamat_penerima' => 'required|string',
            'No_hp_penerima' => 'required|string|max:20',
        ]);

        $detail = DetailProduk::with('itemProduksi.kategoriUsaha')->findOrFail($base['id_detail_produk']);
        $isSablon = strtolower($detail->itemProduksi->kategoriUsaha->nama_kategori ?? '') === 'sablon';

        $validated = $base;

        if ($isSablon) {
            $extra = $request->validate([
                'kuantitas' => 'required|integer|min:1',
                // 'barang_disediakan_usah' => 'required|in:Ya,Tidak',
                'file_desain' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
                // 'jasa_sablon' => 'nullable|boolean',
            ]);

            $validated = array_merge($base, $extra);
        }

        $statusName = 'Menunggu Pembayaran';
        $status = StatusPesanan::where('nama_status_pesanan', $statusName)->first();

        $pesanan = DB::transaction(function () use ($validated, $detail, $status, $isSablon, $request) {
            $pesanan = Pesanan::create([
                'id_pengguna' => Auth::id(),
                'id_detail_produk' => $detail->id_detail_produk,
                'id_status_pesanan' => $status ? $status->id_status_pesanan : 1,
                'tanggal_pesan' => now()->toDateString(),
                'nama_penerima' => $validated['nama_penerima'],
                'alamat_penerima' => $validated['alamat_penerima'],
                'No_hp_penerima' => $validated['No_hp_penerima'],
                'total_harga' => $detail->harga_dasar,
                'jadwal_pemasangan' => $validated['jadwal_pemasangan'] ?? null,
            ]);

            if ($isSablon) {
                $fileDesainPath = null;
                if ($request->hasFile('file_desain')) {
                    $fileDesainPath = $request->file('file_desain')->store('desain', 'public');
                }

                RincianPesanan::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_detail_produk' => $detail->id_detail_produk,
                    'kuantitas' => $validated['kuantitas'],
                    'subtotal' => $detail->harga_dasar,
                    // 'barang_disediakan_usah' => $validated['barang_disediakan_usah'],
                    'file_desain' => $fileDesainPath,
                    // 'opsi' => [
                    //     'jasa_sablon' => (bool) ($validated['jasa_sablon'] ?? false),
                    // ],
                ]);

                // PersetujuanHarga::create([
                //     'id_pesanan' => $pesanan->id_pesanan,
                //     'harga_awal' => $detail->harga_dasar,
                //     'status_persetujuan' => 'Menunggu',
                // ]);
            }
            return $pesanan;
        });

        return response()->json([
            'message' => 'Pesanan dibuat',
            'id_pesanan' => $pesanan->id_pesanan,
        ]);
    }

    // GET /pesanan/{pesanan}
    public function shows(Pesanan $pesanan)
    {
        $pesanan->load('detailProduk', 'statusPesanan');
        return response()->json($pesanan);
    }

    public function showList()
    {
        $itemProduksi = ItemProduksi::with('kategoriUsaha', 'detailProduk.satuanHarga')->where('status_aktif', 'Aktif')->get();
        return view('test.list_item', compact('itemProduksi'));
    }

    public function showListDetail($id)
    {
        $itemProduksi = ItemProduksi::with([ 
            'kategoriUsaha.jenisPembayaran',
            'satuanHarga',
            'detailProduk',
            'fotoProduk',
        ])->findOrFail($id);

        $userId = Auth::id();

        return view('test/detail-produk', compact('itemProduksi', 'userId')); //yang benar klien/detail-produk
    }

    public function showTagihan(Pesanan $pesanan)
    {
        $pesanan->load('statusPesanan', 'pembayaran');
        return view('test.tagihan-dp', compact('pesanan'));
    }

    public function riwayatPesanan(Request $request)
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in([
                'Menunggu Pembayaran',
                'Menunggu Diproses',
                'Diproses',
                'Selesai',
                'Kedaluwarsa',
            ])],
            'tipe' => ['nullable', Rule::in(['Full', 'DP'])],
        ]);

        $userId = Auth::id();

        $query = Pesanan::query()
            ->where('id_pengguna', $userId)
            ->with([
                'detailProduk.itemProduksi',
                'statusPesanan',
                'latestPembayaran' => function ($q) {
                    $q->select([
                        'pembayaran.id_pembayaran',
                        'pembayaran.id_pesanan',
                        'pembayaran.tipe_pembayaran',
                        'pembayaran.jumlah_bayar',
                        'pembayaran.status_bayar',
                        'pembayaran.created_at',
                    ]);
                },
                'pembayaran' => function ($q) {
                    $q->select([
                        'pembayaran.id_pembayaran',
                        'pembayaran.id_pesanan',
                        'pembayaran.tipe_pembayaran',
                        'pembayaran.jumlah_bayar',
                        'pembayaran.status_bayar',
                        'pembayaran.created_at',
                    ])->latest('created_at');
                },
            ])
            ->latest('tanggal_pesan');

        if (!empty($filters['tipe'])) {
            $query->whereHas('pembayaran', fn($p) => $p->where('tipe_pembayaran', $filters['tipe']));
        }

        if (($filters['status'] ?? null) === 'Kedaluwarsa') {
            $query->where(function ($q) {
                $q->whereHas('statusPesanan', fn($s) => $s->where('nama_status_pesanan', 'Dibatalkan'))
                    ->orWhere(function ($q2) {
                        $q2->whereHas('statusPesanan', fn($s) => $s->where('nama_status_pesanan', 'Menunggu Pembayaran'))
                            ->whereHas('pembayaran', fn($p) => $p->where('status_bayar', 'Pending')
                                ->where('created_at', '<', now()->subHours(24)));
                    });
            });
        } elseif (!empty($filters['status'])) {
            $query->whereHas('statusPesanan', fn($s) => $s->where('nama_status_pesanan', $filters['status']));
        }

        $pesanan = $query->paginate(10)->withQueryString();

        return view('test.klien-riwayat-pesanan', compact('pesanan', 'filters'));
    }
}
