<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\StatusPesanan;
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
            'tahap_pembayaran' => 'required|in:DP,Pelunasan',
        ]);

        $pesanan = Pesanan::with('pengguna')->findOrFail($validated['id_pesanan']);
        $tahap = $validated['tahap_pembayaran'];

        if ($tahap === 'DP') {
            $dpExists = $pesanan->pembayaran()
                ->where('tahap_pembayaran', 'DP')
                ->whereIn('status_bayar', ['Pending', 'Lunas'])
                ->exists();

            if ($dpExists) {
                return response()->json(['message' => 'DP sudah dibuat'], 422);
            }

            $grossAmount = (int) round($pesanan->total_harga * 0.5);
        } else {
            $dpPending = $pesanan->pembayaran()
                ->where('tahap_pembayaran', 'DP')
                ->where('status_bayar', 'Pending')
                ->exists();

            if ($dpPending) {
                return response()->json(['message' => 'DP masih pending'], 422);
            }

            $pelunasanExists = $pesanan->pembayaran()
                ->where('tahap_pembayaran', 'Pelunasan')
                ->whereIn('status_bayar', ['Pending', 'Lunas'])
                ->exists();

            if ($pelunasanExists) {
                return response()->json(['message' => 'Pelunasan sudah dibuat'], 422);
            }

            $grossAmount = (int) max(0, $pesanan->sisaBayar());
            if ($grossAmount <= 0) {
                return response()->json(['message' => 'Sisa bayar sudah 0'], 422);
            }
        }

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $suffix = $tahap === 'DP' ? 'DP' : 'PEL';
        $orderId = 'ORDER-' . $pesanan->id_pesanan . '-' . $suffix . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
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
            'tahap_pembayaran' => $tahap,
            'jumlah_bayar' => $grossAmount,
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
            'paid_at' => $statusBayar === 'Lunas' ? now() : null,
        ]);

        if ($statusBayar === 'Lunas') {
            $pesanan = $pembayaran->pesanan;
            if ($pesanan) {
                $targetStatus = $pembayaran->tahap_pembayaran === 'DP' ? 'Diproses' : 'Selesai';
                $status = StatusPesanan::where('nama_status_pesanan', $targetStatus)->first();

                if ($status) {
                    $pesanan->update(['id_status_pesanan' => $status->id_status_pesanan]);
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
