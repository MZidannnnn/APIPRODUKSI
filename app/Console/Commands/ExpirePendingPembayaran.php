<?php

namespace App\Console\Commands;

use App\Services\PembayaranStatusService;
use Illuminate\Console\Command;

class ExpirePendingPembayaran extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:expire-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark pending Midtrans payments older than 24 hours as Kedaluwarsa';

    /**
     * Execute the console command.
     */
    public function handle(PembayaranStatusService $service): int
    {
        $count = $service->expirePendingPayments();

        $this->info("Berhasil memperbarui {$count} pembayaran menjadi Kedaluwarsa.");

        return self::SUCCESS;
    }
}
