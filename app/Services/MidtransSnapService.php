<?php

namespace App\Services;

use App\Models\Pesanan;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransSnapService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createSnapToken(Pesanan $pesanan, string $tipePembayaran): array
    {
        $grossAmount = $this->resolveNominal($pesanan, $tipePembayaran);

        $params = [
            'transaction_details' => [
                'order_id' => $this->buildOrderId($pesanan->id_pesanan, $tipePembayaran),
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $pesanan->nama_penerima,
                'phone' => $pesanan->No_hp_penerima,
                'billing_address' => [
                    'address' => $pesanan->alamat_penerima,
                ],
            ],
            'expiry' => [
                'start_time' => date("Y-m-d H:i:s O"),
                'unit'       => 'hours',
                'duration'   => 24 // Agar sejalan dengan now()->addHours(24) di Controller
            ]

            // Batasi hanya Mandiri VA:
            // 'enabled_payments' => [
            //     "bca_va",
            //     "bni_va",
            //     "bri_va",
            //     "echannel",
            //     "permata_va",
            //     "cimb_va",
            //     "other_va",
            //     "qris",
            //     "gopay"
            // ],

            // Jika akun Anda butuh parameter bank_transfer:
            // 'bank_transfer' => ['bank' => 'mandiri'],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
        } catch (\Throwable $e) {
            report($e);
            throw new \RuntimeException('Midtrans Snap error');
        }

        return [
            'snap_token' => $snapToken,
            'order_id' => $params['transaction_details']['order_id'],
            'gross_amount' => $grossAmount,
        ];
    }

    private function resolveNominal(Pesanan $pesanan, string $tipePembayaran): int
    {
        if ($tipePembayaran === 'DP') {
            return (int) round($pesanan->total_harga / 2);
        }

        if ($tipePembayaran === 'Pelunasan') {
            return (int) $pesanan->sisaBayar();
        }

        return (int) $pesanan->total_harga;
    }

    private function buildOrderId(int $pesananId, string $tipePembayaran): string
    {
        return 'ORDER-' . $pesananId . '-' . strtoupper($tipePembayaran) . '-' . time();
    }
}
