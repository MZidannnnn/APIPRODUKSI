<?php

namespace App\Http\Controllers;

use App\Models\pengguna;
use App\Models\ItemProduksi;
use App\Models\KategoriUsaha;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    /**
     * Tampilkan form register
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Tampilkan form register
     */
    public function showDashboard(Request $request)
    {
        $kategoriUsaha = KategoriUsaha::all();
        $selectedKategoriId = $request->query('kategori');

        $itemProduksi = ItemProduksi::with(['kategoriUsaha', 'detailProduk.satuanHarga'])
            ->when($selectedKategoriId, function ($query) use ($selectedKategoriId) {
                $query->where('id_kategori', $selectedKategoriId);
            })
            ->get();

        return view('pelanggan.dashboard', compact(
            'kategoriUsaha',
            'itemProduksi',
            'selectedKategoriId'
        ));
    }

    /**
     * Handle registrasi klien
     */
    public function register(Request $request)
    {
        // Validasi
        $validated = $request->validate([
            'nama_pengguna' => ['required', 'string', 'max:100', 'unique:pengguna'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:pengguna'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

        ]);

        // Role untuk klien = 1 (dari tabel role)
        $role_klien = 3; // Sesuaikan dengan id_role klien di tabel role

        // Buat akun baru (id_divisi dikosongkan)
        $pengguna = pengguna::create([
            'id_role' => $role_klien,
            'id_kategori' => null, // Kosong untuk klien
            'nama_pengguna' => $validated['nama_pengguna'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        event(new Registered($pengguna));

        Auth::login($pengguna);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil!');
    }

    /**
     * Tampilkan form login
     */
    public function showLoginPelanggan()
    {
        return view('auth.login');
    }

    /**
     * Tampilkan form login
     */
    public function showLoginAdmin()
    {
        return view('auth.loginadmin');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nama_pengguna' => ['required'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'nama_pengguna' => 'Username atau password salah.',
            ])->onlyInput('nama_pengguna');
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if ((int) $user->id_role === 3) {
            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()->withErrors([
            'nama_pengguna' => 'Role tidak diizinkan.',
        ])->onlyInput('nama_pengguna');
    }


    /**
     * Handle login
     */
    public function loginAdmin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'email atau password salah.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if ((int) $user->id_role === 2) {
            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()->withErrors([
            'email' => 'Role tidak diizinkan.',
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

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        Log::info('Proses lupa password dimulai', ['email' => $request->email]);

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        Log::info('Status kirim reset link', ['status' => $status]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(Request $request)
    {
        Log::info('Proses reset password dimulai', ['email' => $request->email]);

        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = $password;
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        Log::info('Status reset password', ['status' => $status]);

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }
}
