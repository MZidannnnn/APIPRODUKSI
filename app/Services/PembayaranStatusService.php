<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\StatusPesanan;
use Illuminate\Support\Facades\DB;

class PembayaranStatusService
{
    public function handleMidtransNotification(array $payload): Pembayaran
    {
        return DB::transaction(function () use ($payload) {
            $pembayaran = Pembayaran::query()
                ->where('order_id', $payload['order_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $statusBayar = match ($payload['transaction_status']) {
                'settlement', 'capture' => 'Lunas',
                'expire', 'cancel', 'deny' => 'Kedaluwarsa',
                default => 'Pending',
            };

            $pembayaran->update([
                'status_bayar' => $statusBayar,
                'payment_type' => $payload['payment_type'] ?? $pembayaran->payment_type,
                'transaction_id' => $payload['transaction_id'] ?? $pembayaran->transaction_id,
                'jumlah_bayar' => $payload['gross_amount'] ?? $pembayaran->jumlah_bayar,
                'payload' => $payload,
            ]);

            if ($statusBayar === 'Lunas') {
                $this->syncPesananAfterPaid($pembayaran);
            }

            return $pembayaran;
        });
    }

    public function expirePendingPayments(int $chunkSize = 200): int
    {
        $expiredCount = 0;
        $cutoff = now()->subHours(24);

        Pembayaran::query()
            ->select(['id_pembayaran'])
            ->where('status_bayar', 'Pending')
            ->where('created_at', '<', $cutoff)
            ->orderBy('id_pembayaran')
            ->chunkById($chunkSize, function ($payments) use (&$expiredCount) {
                DB::transaction(function () use ($payments, &$expiredCount) {
                    $ids = $payments->pluck('id_pembayaran')->all();

                    if (empty($ids)) {
                        return;
                    }

                    Pembayaran::whereIn('id_pembayaran', $ids)->update([
                        'status_bayar' => 'Kedaluwarsa',
                    ]);

                    $expiredCount += count($ids);
                });
            });

        return $expiredCount;
    }

    private function syncPesananAfterPaid(Pembayaran $pembayaran): void
    {
        $pesanan = Pesanan::query()
            ->where('id_pesanan', $pembayaran->id_pesanan)
            ->lockForUpdate()
            ->first();

        if (! $pesanan) {
            return;
        }

        $statusId = StatusPesanan::where('nama_status_pesanan', 'Menunggu Diproses')
            ->value('id_status_pesanan');

        if ($statusId) {
            $pesanan->update(['id_status_pesanan' => $statusId]);
        }
    }
}