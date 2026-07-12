<?php

namespace App\Providers;

use App\Models\ItemProduksi;
use App\Models\Pembayaran;
use App\Models\Pengguna;
use App\Models\Pesanan;
use App\Observers\PesananObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        // Cek jika aplikasi berjalan di lingkungan selain lokal (seperti Ngrok)
        if (config('app.env') !== 'local' || isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            URL::forceScheme('https');
        }

        // Role 2 (Admin) mendapat akses penuh ke semua operasi model utama.
        // Role 1 (Owner) hanya boleh mengakses dashboard statistik dan export laporan,
        // sehingga TIDAK diberikan Gate::before bypass global.
        Gate::before(function (Pengguna $user, string $ability, array $arguments = []): ?bool {
            if ((int) $user->id_role !== 2) {
                return null;
            }

            $target = $arguments[0] ?? null;

            $allowed = [
                Pesanan::class     => ['viewAny', 'updateStatus'],
                Pembayaran::class  => ['viewAdminHistory'],
                ItemProduksi::class => ['viewAny', 'view', 'create', 'update', 'delete'],
            ];

            foreach ($allowed as $modelClass => $abilities) {
                $isModelMatch = $target === $modelClass || ($target instanceof $modelClass);

                if ($isModelMatch && in_array($ability, $abilities, true)) {
                    return true;
                }
            }

            return null;
        });

        Pesanan::observe(PesananObserver::class);
    }
}
