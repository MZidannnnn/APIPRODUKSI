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
        $request->validate([
            'nama_penerima' => 'required|string|max:100',
            'alamat_penerima' => 'required|string|min:10',
            'No_hp_penerima' => 'required|regex:/^08[0-9]{8,11}$/',
            'jadwal_pemasangan' => 'required|date|after_or_equal:today',
        ],[
            'nama_penerima.required' => 'Nama pemesan wajib diisi.',
            'alamat_penerima.required' => 'Alamat wajib diisi.',
            'No_hp_penerima.required' => 'Nomor HP wajib diisi.',
            'No_hp_penerima.regex' => 'Nomor HP tidak valid.',
            'jadwal_pemasangan.required' => 'Silakan pilih jadwal.',
            'jadwal_pemasangan.after_or_equal' => 'Jadwal tidak boleh sebelum hari ini.',
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
            'jadwal_pemasangan' => 'required|date',
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
        $validated = $request->validate([
            'id_detail_produk' => [
                'required',
                'exists:detail_produk,id_detail_produk'
            ],

            'nama_penerima' => [
                'required',
                'string',
                'max:100'
            ],

            'alamat_penerima' => [
                'required',
                'string',
                'min:10'
            ],

            'No_hp_penerima' => [
                'required',
                'regex:/^08[0-9]{8,11}$/'
            ],

            'kuantitas' => [
                'required',
                'integer',
                'min:1'
            ],

            'jadwal_pemasangan' => [
                'required',
                'date',
                'after_or_equal:today'
            ],
        ], [

            'nama_penerima.required' => 'Nama pemesan wajib diisi.',
            'nama_penerima.max' => 'Nama pemesan maksimal 100 karakter.',

            'alamat_penerima.required' => 'Alamat pemesan wajib diisi.',
            'alamat_penerima.min' => 'Alamat minimal 10 karakter.',

            'No_hp_penerima.required' => 'Nomor HP / WhatsApp wajib diisi.',
            'No_hp_penerima.regex' => 'Nomor HP harus diawali 08 dan terdiri dari 10-13 digit.',

            'kuantitas.required' => 'Jumlah pesanan wajib diisi.',
            'kuantitas.integer' => 'Jumlah pesanan harus berupa angka.',
            'kuantitas.min' => 'Jumlah pesanan minimal 1.',

            'jadwal_pemasangan.required' => 'Silakan pilih jadwal.',
            'jadwal_pemasangan.date' => 'Format tanggal tidak valid.',
            'jadwal_pemasangan.after_or_equal' => 'Jadwal tidak boleh sebelum hari ini.',
        ]);

        $detail = DetailProduk::with('itemProduksi.konfigurasiBiaya')->findOrFail($validated['id_detail_produk']);
        $konfigurasi = $detail->itemProduksi->konfigurasiBiaya;

        // Validasi minimum lead time (Zona Merah)
        if ($konfigurasi && $konfigurasi->is_biaya_waktu_aktif && $konfigurasi->batas_hari_zona_merah !== null) {
            $minHari = $konfigurasi->batas_hari_zona_merah;
            $minDate = now()->addDays($minHari)->startOfDay();
            $jadwal = \Carbon\Carbon::parse($validated['jadwal_pemasangan'])->startOfDay();

            if ($jadwal->lte(now()->addDays($minHari - 1)->startOfDay())) { // lte so if today + batas_hari_zona_merah is tomorrow it is blocked
                return redirect()->back()->withErrors([
                    'jadwal_pemasangan' => 'Tanggal pengerjaan terlalu dekat. Harus lebih dari H-' . $minHari . ' dari hari pemesanan.'
                ])->withInput();
            }
        }

        $status = StatusPesanan::where('nama_status_pesanan', 'Belum Bayar')->first();

        $kuantitas = (int) $validated['kuantitas'];
        $subtotal = round((float) $detail->harga_dasar * $kuantitas, 2);

        $biaya_jarak = 0;
        $biaya_waktu = 0;

        if ($konfigurasi) {
            // Kalkulasi Biaya Jarak
            if ($konfigurasi->is_biaya_jarak_aktif) {
                $workshopCoord = \App\Models\WorkshopCoordinate::first();
                $latWorkshop = $workshopCoord ? $workshopCoord->latitude : '-3.2994'; 
                $lonWorkshop = $workshopCoord ? $workshopCoord->longitude : '114.5933';
                
                $latKlien = $request->input('latitude');
                $lonKlien = $request->input('longitude');

                if ($latKlien && $lonKlien) {
                    $jarak_km = 0;
                    try {
                        $response = \Illuminate\Support\Facades\Http::timeout(5)
                            ->get("http://router.project-osrm.org/route/v1/driving/{$lonWorkshop},{$latWorkshop};{$lonKlien},{$latKlien}?overview=false");
                            
                        if ($response->successful() && $response->json('routes.0.distance')) {
                            $jarakMeter = $response->json('routes.0.distance'); // Asal OSRM (Meter)
                            $jarak_km = $jarakMeter / 1000; // Konversi ke KM
                        } else {
                            $jarak_km = $this->hitungJarakHaversine((float)$latWorkshop, (float)$lonWorkshop, (float)$latKlien, (float)$lonKlien);
                        }
                    } catch (\Exception $e) {
                        $jarak_km = $this->hitungJarakHaversine((float)$latWorkshop, (float)$lonWorkshop, (float)$latKlien, (float)$lonKlien);
                    }

                    if ($jarak_km <= 45) {
                        $biaya_jarak = 0;
                    } else {
                        $biaya_jarak = round($jarak_km * $konfigurasi->tarif_per_km);
                    }
                }
            }

            // Kalkulasi Biaya Waktu (Urgensi)
            if ($konfigurasi->is_biaya_waktu_aktif) {
                $tanggal_pesan = now()->startOfDay();
                $jadwal = \Carbon\Carbon::parse($validated['jadwal_pemasangan'])->startOfDay();
                $selisih_hari = $tanggal_pesan->diffInDays($jadwal, false);

                // Zona Merah (Blokir)
                if ($konfigurasi->batas_hari_zona_merah !== null && $selisih_hari < $konfigurasi->batas_hari_zona_merah) {
                    return response()->json([
                        'message' => 'Tanggal pemasangan terlalu dekat. Silakan pilih hari lain.'
                    ], 422);
                }

                // Zona Kuning (Urgensi)
                if ($konfigurasi->batas_hari_zona_kuning !== null) {
                    $batas_akhir_kuning = $konfigurasi->batas_hari_zona_merah + $konfigurasi->batas_hari_zona_kuning - 1;
                    if ($selisih_hari >= $konfigurasi->batas_hari_zona_merah && $selisih_hari <= $batas_akhir_kuning) {
                        $biaya_waktu = $konfigurasi->biaya_urgensi;
                    }
                }
            }
        }

        $grand_total = $subtotal + $biaya_jarak + $biaya_waktu;

        $pesanan = DB::transaction(function () use ($validated, $detail, $status, $kuantitas, $subtotal, $biaya_jarak, $biaya_waktu, $grand_total, $request) {
            $pesanan = Pesanan::create([
                'id_pengguna' => Auth::id(),
                'id_detail_produk' => $detail->id_detail_produk,
                'id_status_pesanan' => $status ? $status->id_status_pesanan : 1,
                'tanggal_pesan' => now()->toDateString(),
                'nama_penerima' => $validated['nama_penerima'],
                'alamat_penerima' => $validated['alamat_penerima'],
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'alamat_lengkap' => $request->input('alamat_lengkap'),
                'No_hp_penerima' => $validated['No_hp_penerima'],
                'total_harga' => $grand_total,
                'total_biaya_jarak' => $biaya_jarak,
                'total_biaya_waktu' => $biaya_waktu,
                'jadwal_pemasangan' => $validated['jadwal_pemasangan'] ?? null,
            ]);

            RincianPesanan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'id_detail_produk' => $detail->id_detail_produk,
                'kuantitas' => $kuantitas,
                'subtotal' => $subtotal,
            ]);

            // PersetujuanHarga::create([
            //     'id_pesanan' => $pesanan->id_pesanan,
            //     'harga_awal' => $detail->harga_dasar,
            //     'status_persetujuan' => 'Menunggu',
            // ]);

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
            'konfigurasiBiaya'
        ])->findOrFail($id);

        $userId = Auth::id();
        $workshopCoord = \App\Models\WorkshopCoordinate::first();

        return view('klien.detail-produk', compact('itemProduksi', 'userId', 'workshopCoord'));
        //return view('test/detail-produk', compact('itemProduksi', 'userId'));
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
                'Belum Bayar',
                'Pesanan Diproses',
                'Pesanan selesai, silahkan lunasi pembayaran',
                'Pesanan Selesai', 
                'Pesanan Dibatalkan',
                'Dibatalkan',
            ])],
            'tipe' => ['nullable', Rule::in(['Full', 'DP'])],
        ]);

        $userId = Auth::id();

        $statusKadaluarsa = StatusPesanan::where('nama_status_pesanan', 'Pesanan Kadaluarsa')->first();

        if ($statusKadaluarsa) {
            Pesanan::where('id_pengguna', $userId)
                ->whereHas('statusPesanan', function ($q) {
                    $q->where('nama_status_pesanan', 'Belum Bayar');
                })
                ->where('created_at', '<=', now()->subHours(24))
                ->whereDoesntHave('pembayaran', function ($q) {
                    $q->where('status_bayar', 'Lunas');
                })
                ->update([
                    'id_status_pesanan' => $statusKadaluarsa->id_status_pesanan,
                ]);
        }

        $query = Pesanan::query()
            ->where('id_pengguna', $userId)
            ->with([
                'detailProduk.itemProduksi.fotoProduk',
                'detailProduk.itemProduksi.satuanHarga',
                'statusPesanan',
                'rincianPesanan',
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
            $query->whereHas('pembayaran', function ($p) use ($filters) {
                $p->where('tipe_pembayaran', $filters['tipe']);
            });
        }

        if (($filters['status'] ?? null) === 'Dibatalkan') {
            $query->whereHas('statusPesanan', function ($s) {
                $s->whereIn('nama_status_pesanan', [
                    'Pesanan Dibatalkan',
                    'Pesanan Kadaluarsa',
                ]);
            });
        } elseif (!empty($filters['status'])) {
            $query->whereHas('statusPesanan', function ($s) use ($filters) {
                $s->where('nama_status_pesanan', $filters['status']);
            });
        }

        $pesanan = $query->paginate(10)->withQueryString();

        return view('klien.riwayat-pesanan', compact('pesanan', 'filters'));
        //return view('test.klien-riwayat-pesanan', compact('pesanan', 'filters'));
    }
    
    public function detailRiwayat(Pesanan $pesanan)
    {
        abort_unless(
            (int) $pesanan->id_pengguna === (int) auth()->id(),
            403
        );

        $pesanan->load([
            'statusPesanan',
            'detailProduk.itemProduksi.fotoProduk',
            'rincianPesanan.detailProduk.itemProduksi',
            'pembayaran' => fn ($q) => $q->latest('created_at'),
        ]);

        return view('klien.detail-pesanan', compact('pesanan'));
    }

    private function hitungJarakHaversine($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Radius bumi dalam Kilometer
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c; // Mengembalikan hasil jarak (KM)
    }
}