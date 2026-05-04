<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;

class PembayaranController extends Controller
{
    public function createTransaction(Request $request)
    {
        $validated = $request->validate([
            'id_pesanan' => 'required|exists:pesanan,id_pesanan',
        ]);

        $pesanan = Pesanan::with('pengguna')->findOrFail($validated['id_pesanan']);

        // konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = 'ORDER-' . $pesanan->id_pesanan . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $pesanan->total_harga,
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

        // simpan pembayaran
        $pembayaran = Pembayaran::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'jumlah_bayar' => $pesanan->total_harga,
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

        // mapping status
        $statusBayar = in_array($transactionStatus, ['settlement', 'capture']) ? 'Lunas' : 'Pending';

        $pembayaran->update([
            'status_bayar' => $statusBayar,
            'payment_type' => $paymentType,
            'transaction_id' => $transactionId,
            'jumlah_bayar' => $grossAmount ?? $pembayaran->jumlah_bayar,
            'payload' => $payload,
        ]);

        // update status pesanan bila lunas
        if ($statusBayar === 'Lunas') {
            $pesanan = $pembayaran->pesanan;
            if ($pesanan) {
                $statusSelesai = \App\Models\StatusPesanan::where('nama_status', 'Lunas')->first();
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
}
