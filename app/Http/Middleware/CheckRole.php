<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $userRole = Auth::user()->id_role;
        
        // Cek apakah role user ada di daftar role yang diizinkan
        if (in_array($userRole, $roles)) {
            return $next($request);
        }
        
        // Jika tidak, redirect atau abort
        return abort(403, 'Anda tidak memiliki akses ke halaman ini');
    }
}
