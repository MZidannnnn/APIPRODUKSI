<?php

namespace App\Http\Controllers;

use App\Models\pengguna;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    /**
     * Tampilkan form register
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    public function index1()
    {
        return view('dashboard');
    }

    /**
     * Handle registrasi klien
     */
    public function register(Request $request)
    {
        // Validasi
        $validated = $request->validate([
            'nama_pengguna' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:pengguna'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
           
        ]);

        // Role untuk klien = 1 (dari tabel role)
        $role_klien = 1; // Sesuaikan dengan id_role klien di tabel role

        // Buat akun baru (id_divisi dikosongkan)
        $pengguna = pengguna::create([
            'id_role' => $role_klien,
            'id_divisi' => null, // Kosong untuk klien
            'nama_pengguna' => $validated['nama_pengguna'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'Jenis_akun' => 'Pribadi',
        ]);

        event(new Registered($pengguna));

        Auth::login($pengguna);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil!');
    }

    /**
     * Tampilkan form login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }    

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
