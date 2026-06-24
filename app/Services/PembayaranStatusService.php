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

            /*
            |--------------------------------------------------------------------------
            | STATUS BAYAR
            |--------------------------------------------------------------------------
            | Ini status untuk tabel pembayaran, BUKAN status pesanan.
            |
            | Pending     = transaksi dibuat, tapi belum dibayar
            | Lunas       = pembayaran berhasil
            | Kedaluwarsa = pembayaran gagal / expired / dibatalkan dari Midtrans
            */
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

            /*
            |--------------------------------------------------------------------------
            | SINKRON STATUS PESANAN
            |--------------------------------------------------------------------------
            | Kalau pembayaran berhasil, status pesanan ikut berubah.
            */
            if ($statusBayar === 'Lunas') {
                $this->syncPesananAfterPaid($pembayaran);
            }

            /*
            |--------------------------------------------------------------------------
            | SINKRON PESANAN KADALUARSA
            |--------------------------------------------------------------------------
            | Kalau pembayaran expired dan pesanan belum pernah dibayar,
            | maka status pesanan berubah menjadi Pesanan Kadaluarsa.
            */
            if ($statusBayar === 'Kedaluwarsa') {
                $this->syncPesananAfterExpired($pembayaran);
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

                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE STATUS BAYAR JADI KEDALUWARSA
                    |--------------------------------------------------------------------------
                    | Ini hanya mengubah tabel pembayaran.
                    */
                    Pembayaran::whereIn('id_pembayaran', $ids)->update([
                        'status_bayar' => 'Kedaluwarsa',
                    ]);

                    $statusKadaluarsaId = StatusPesanan::where('nama_status_pesanan', 'Pesanan Kadaluarsa')
                        ->value('id_status_pesanan');

                    if ($statusKadaluarsaId) {
                        /*
                        |--------------------------------------------------------------------------
                        | UPDATE STATUS PESANAN JADI PESANAN KADALUARSA
                        |--------------------------------------------------------------------------
                        | Ini mengubah tabel pesanan.
                        */
                        Pesanan::whereHas('pembayaran', function ($q) use ($ids) {
                                $q->whereIn('id_pembayaran', $ids);
                            })
                            ->whereHas('statusPesanan', function ($q) {
                                $q->where('nama_status_pesanan', 'Belum Bayar');
                            })
                            ->whereDoesntHave('pembayaran', function ($q) {
                                $q->where('status_bayar', 'Lunas');
                            })
                            ->update([
                                'id_status_pesanan' => $statusKadaluarsaId,
                            ]);
                    }

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

        /*
        |--------------------------------------------------------------------------
        | JIKA PEMBAYARAN PELUNASAN BERHASIL
        |--------------------------------------------------------------------------
        | Status pesanan berubah menjadi Pesanan Selesai.
        */
        if ($pembayaran->tipe_pembayaran === 'Pelunasan') {
            $statusId = StatusPesanan::where('nama_status_pesanan', 'Pesanan Selesai')
                ->value('id_status_pesanan');

            if ($statusId) {
                $pesanan->update([
                    'id_status_pesanan' => $statusId,
                ]);
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | JIKA PEMBAYARAN DP / FULL BERHASIL
        |--------------------------------------------------------------------------
        | Status pesanan berubah dari Belum Bayar menjadi Pesanan Diproses.
        */
        $statusId = StatusPesanan::where('nama_status_pesanan', 'Pesanan Diproses')
            ->value('id_status_pesanan');

        if ($statusId) {
            $pesanan->update([
                'id_status_pesanan' => $statusId,
            ]);
        }
    }

    private function syncPesananAfterExpired(Pembayaran $pembayaran): void
    {
        $pesanan = Pesanan::query()
            ->with('statusPesanan')
            ->where('id_pesanan', $pembayaran->id_pesanan)
            ->lockForUpdate()
            ->first();

        if (! $pesanan) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | HANYA PESANAN BELUM BAYAR YANG BOLEH JADI KADALUARSA
        |--------------------------------------------------------------------------
        | Kalau sudah diproses / selesai, jangan diubah.
        */
        if (($pesanan->statusPesanan->nama_status_pesanan ?? null) !== 'Belum Bayar') {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CEK APAKAH ADA PEMBAYARAN YANG SUDAH LUNAS
        |--------------------------------------------------------------------------
        | Kalau sudah pernah lunas, jangan ubah jadi kadaluarsa.
        */
        $hasPaid = $pesanan->pembayaran()
            ->where('status_bayar', 'Lunas')
            ->exists();

        if ($hasPaid) {
            return;
        }

        $statusId = StatusPesanan::where('nama_status_pesanan', 'Pesanan Kadaluarsa')
            ->value('id_status_pesanan');

        if ($statusId) {
            $pesanan->update([
                'id_status_pesanan' => $statusId,
            ]);
        }
    }
}