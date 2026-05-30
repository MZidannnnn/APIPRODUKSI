<?php

namespace App\Providers;

use App\Models\ItemProduksi;
use App\Models\Pembayaran;
use App\Models\pengguna;
use App\Models\Pesanan;
use App\Observers\PesananObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        //
        // Cek jika aplikasi berjalan di lingkungan selain lokal (seperti Ngrok)
        if (config('app.env') !== 'local' || isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            URL::forceScheme('https');
        }

        Gate::before(function (pengguna $user, string $ability, array $arguments = []): ?bool {
            if ((int) $user->id_role !== 1) {
                return null;
            }

            $target = $arguments[0] ?? null;

            $allowed = [
                Pesanan::class => ['viewAny', 'updateStatus'],
                Pembayaran::class => ['viewAdminHistory'],
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
