<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'checkRole' => \App\Http\Middleware\CheckRole::class,
            'redirectRole' => \App\Http\Middleware\RedirectByRole::class,
        ]);

        $middleware->redirectUsersTo(function () {
            if (!Auth::check()) {
                return '/login';
            }

            $role = Auth::user()->id_role;

            if ($role == 1) {
                return '/super-admin/dashboard';
            }

            if ($role == 2) {
                return '/admin/dashboard';
            }

            if ($role == 3) {
                return '/dashboard';
            }

            return '/login';
        });

        $middleware->validateCsrfTokens(except: [
            'midtrans/notification',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
