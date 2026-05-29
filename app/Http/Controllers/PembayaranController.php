<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\StatusPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

class PembayaranController extends Controller
{

    public function createTransaction(Request $request)
    {
        $validated = $request->validate([
            'id_pesanan' => 'required|exists:pesanan,id_pesanan',
            'tipe_pembayaran' => 'required|in:DP,Full,Pelunasan',
        ]);

        $pesanan = Pesanan::with('pengguna')->findOrFail($validated['id_pesanan']);

        // untuk pesanan harga tawar, pastikan harga sudah disetujui sebelum membuat transaksi pembayaran
        $persetujuan = $pesanan->persetujuanHarga;
        if ($persetujuan && $persetujuan->status_persetujuan !== 'Disetujui') {
            return response()->json(['message' => 'Harga belum disetujui'], 422);
        }

        // konfig midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $nominal = $pesanan->total_harga;


        if ($validated['tipe_pembayaran'] === 'DP') {
            $nominal = round($pesanan->total_harga / 2);
        } elseif ($validated['tipe_pembayaran'] === 'Pelunasan') {
            $nominal = $pesanan->sisaBayar();
        }

        $orderId = 'ORDER-' . $pesanan->id_pesanan . '-' . strtoupper($validated['tipe_pembayaran']) . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $nominal,
            ],
            'customer_details' => [
                'first_name' => $pesanan->nama_penerima,
                'phone' => $pesanan->No_hp_penerima,
                'billing_address' => [
                    'address' => $pesanan->alamat_penerima,
                ],
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        $pembayaran = Pembayaran::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'tipe_pembayaran' => $validated['tipe_pembayaran'],
            'jumlah_bayar' => $nominal,
            'order_id' => $orderId,
            'status_bayar' => 'Pending',
        ]);

        return response()->json([
            'snap_token' => $snapToken,
            'id_pembayaran' => $pembayaran->id_pembayaran,
        ]);
    }

    public function notification(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        $payload = $request->all();
        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? null;
        $transactionId = $payload['transaction_id'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;

        if (!$orderId) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $pembayaran = Pembayaran::where('order_id', $orderId)->first();
        if (!$pembayaran) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $statusBayar = in_array($transactionStatus, ['settlement', 'capture']) ? 'Lunas' : 'Pending';

        $pembayaran->update([
            'status_bayar' => $statusBayar,
            'payment_type' => $paymentType,
            'transaction_id' => $transactionId,
            'jumlah_bayar' => $grossAmount ?? $pembayaran->jumlah_bayar,
            'payload' => $payload,
        ]);

        if ($statusBayar === 'Lunas') {
            $pesanan = $pembayaran->pesanan;
            if ($pesanan) {
                $statusSelesai = StatusPesanan::where('nama_status_pesanan', 'Lunas')->first();
                if ($statusSelesai) {
                    $pesanan->update(['id_status_pesanan' => $statusSelesai->id_status_pesanan]);
                }
            }
        }


        return response()->json(['message' => 'OK']);
    }

    public function showUploadForm(Pembayaran $pembayaran)
    {
        return view('pembayaran.upload_bukti', compact('pembayaran'));
    }

    // proses upload
    public function uploadBukti(Request $request, Pembayaran $pembayaran)
    {
        $validated = $request->validate([
            'bukti_bayar' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $path = $request->file('bukti_bayar')->store('bukti-bayar', 'public');

        $pembayaran->update([
            'bukti_bayar' => $path,
        ]);

        return back()->with('success', 'Bukti bayar berhasil diupload');
    }

    public function TampilRiwayatTransaksi() 
    {
        $admin = Auth::user()->id_kategori;

        $riwayatTransaksi = Pembayaran::with([
            'pesanan:id_pesanan,nama_penerima',
            'pesanan.detailProduk.itemProduksi:id_item_produksi,id_kategori'
        ])
        ->whereHas('pesanan.detailProduk.itemProduksi', function ($query) use ($admin) {
            // Logika filter id_kategori harus berada di dalam fungsi ini
            $query->where('id_kategori', $admin);
        })
        ->get();

        $data = [
            'title' => 'Riwayat Transaksi Klien',
            'menuRiwayatTransaksi' => 'active',
            'riwayatTransaksi' => $riwayatTransaksi,
        ];

        return view('admin/riwayat-transaksi/index', $data);
    }
}
