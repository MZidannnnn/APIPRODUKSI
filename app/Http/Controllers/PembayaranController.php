<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTransactionRequest;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\StatusPesanan;
use App\Services\MidtransSnapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Midtrans\Config;
use Midtrans\Snap;

class PembayaranController extends Controller
{

    public function createTransaction(CreateTransactionRequest $request, MidtransSnapService $midtrans)
    {
        $pesanan = Pesanan::query()
            ->where('id_pesanan', $request->id_pesanan)
            ->where('id_pengguna', $request->user()->id ?? Auth::id())
            ->firstOrFail();

        try {
            $snap = $midtrans->createSnapToken($pesanan, $request->tipe_pembayaran);

            $pembayaran = Pembayaran::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'tipe_pembayaran' => $request->tipe_pembayaran,
                'jumlah_bayar' => $snap['gross_amount'],
                'order_id' => $snap['order_id'],
                'snap_token' => $snap['snap_token'],
                'snap_expires_at' => now()->addHours(24),
                'status_bayar' => 'Pending',
            ]);

            return response()->json([
                'snap_token' => $snap['snap_token'],
                'id_pembayaran' => $pembayaran->id_pembayaran,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Gagal membuat transaksi.'], 500);
        }
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
        return view('test.list-transaksi-admin', compact('riwayatTransaksi'));
    }

    public function retrySnap(Request $request)
    {
        $data = $request->validate([
            'id_pesanan' => ['required', 'integer', 'exists:pesanan,id_pesanan'],
            // 'tipe_pembayaran' => ['nullable', Rule::in(['DP', 'Full', 'Pelunasan'])],
        ]);

        $pesanan = Pesanan::query()
            ->with(['statusPesanan', 'latestPembayaran'])
            ->where('id_pesanan', $data['id_pesanan'])
            ->where('id_pengguna', $request->user()->id_pengguna ?? Auth::id())
            ->firstOrFail();

        if (($pesanan->statusPesanan->nama_status_pesanan ?? '') !== 'Menunggu Pembayaran') {
            return response()->json(['message' => 'Status pesanan tidak valid.'], 422);
        }

        $last = $pesanan->latestPembayaran;

        if (!$last) {
            return response()->json(['message' => 'Belum ada transaksi untuk pesanan ini.'], 404);
        }

        if ($last->status_bayar === 'Lunas') {
            return response()->json(['message' => 'Pesanan sudah lunas.'], 422);
        }
        $expiresAt = $last->snap_expires_at
            ?? $last->created_at?->copy()->addHours(24);

        if (
            $last->status_bayar === 'Pending'
            && $expiresAt
            && $expiresAt->isFuture()
        ) {
            return response()->json([
                'snap_token' => $last->snap_token,
                'id_pembayaran' => $last->id_pembayaran,
            ]);
        }

        $last->update(['status_bayar' => 'Kedaluwarsa']);

        $statusBatal = StatusPesanan::where('nama_status_pesanan', 'Dibatalkan')->first();
        if ($statusBatal) {
            $pesanan->update(['id_status_pesanan' => $statusBatal->id_status_pesanan]);
        }

        return response()->json(['message' => 'Token pembayaran sudah kedaluwarsa.'], 422);
    }
}
