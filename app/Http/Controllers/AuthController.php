<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\ItemProduksi;
use App\Models\KategoriUsaha;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function showDashboard(Request $request)
    {
        $kategoriUsaha = KategoriUsaha::all();
        $selectedKategoriId = $request->query('kategori');

        $itemProduksi = ItemProduksi::with(['kategoriUsaha', 'satuanHarga', 'detailProduk', 'fotoProduk'])
            ->when($selectedKategoriId, function ($query) use ($selectedKategoriId) {
                $query->where('id_kategori', $selectedKategoriId);
            })
            ->get();

        return view('klien.dashboard', compact(
            'kategoriUsaha',
            'itemProduksi',
            'selectedKategoriId'
        ));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama_pengguna' => ['required', 'string', 'max:100', 'unique:pengguna,nama_pengguna'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:pengguna,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nama_pengguna.required' => 'Username wajib diisi.',
            'nama_pengguna.string' => 'Username harus berupa teks.',
            'nama_pengguna.max' => 'Username maksimal 100 karakter.',
            'nama_pengguna.unique' => 'Username sudah digunakan.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 150 karakter.',
            'email.unique' => 'Email sudah digunakan.',

            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $pengguna = Pengguna::create([
            'id_role' => 3,
            'id_kategori' => null,
            'nama_pengguna' => $validated['nama_pengguna'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        event(new Registered($pengguna));

        Auth::login($pengguna);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil!');
    }

    public function showLoginKlien()
    {
        return view('auth.login');
    }

    public function showLoginAdmin()
    {
        return view('auth.loginadmin');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nama_pengguna' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'nama_pengguna.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cek username terlebih dahulu
        $user = Pengguna::where('nama_pengguna', $request->nama_pengguna)->first();

        if (!$user) {
            return back()
                ->withErrors([
                    'nama_pengguna' => 'Username tidak ditemukan.',
                ])
                ->onlyInput('nama_pengguna');
        }

        // Cek password
        if (!Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors([
                    'password' => 'Password yang Anda masukkan salah.',
                ])
                ->onlyInput('nama_pengguna');
        }

        // Login
        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ((int) $user->id_role === 3) {
            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()
            ->withErrors([
                'nama_pengguna' => 'Akun ini tidak diizinkan login melalui halaman klien.',
            ])
            ->onlyInput('nama_pengguna');
    }

    public function loginAdmin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $user = Pengguna::where('email', $request->email)->first();

        if (!$user) {
            return back()
                ->withErrors([
                    'email' => 'Email atau password salah.',
                ])
                ->onlyInput('email');
        }

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ((int) $user->id_role === 1) {
                return redirect()->route('dashboardOwner')
                    ->with('success', 'Anda berhasil login sebagai Owner');
            }

            if ((int) $user->id_role === 2) {
                return redirect()->route('dashboardAdmin')
                    ->with('success', 'Anda berhasil login sebagai Admin');
            }

            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors([
                    'email' => 'Akses hanya untuk Admin dan Owner.',
                ])
                ->onlyInput('email');
        }

        return back()
            ->withErrors([
                'email' => 'Email atau password salah.',
            ])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $role = Auth::user()->id_role;

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if (in_array($role, [1, 2])) {
            return redirect('/admin/privasi')->with('success', 'Berhasil logout');
        }

        if ($role == 3) {
            return redirect('/')->with('success', 'Berhasil logout');
        }

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
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        // Cek apakah email terdaftar
        $user = Pengguna::where('email', $request->email)->first();

        if (!$user) {
            return back()
                ->withErrors([
                    'email' => 'Email tidak terdaftar.',
                ])
                ->withInput();
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        Log::info('Status kirim reset link', ['status' => $status]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Link reset password berhasil dikirim ke email Anda.')
            : back()->withErrors([
                'email' => 'Gagal mengirim link reset password.',
            ]);
    }

    public function resetPassword(Request $request)
    {
        Log::info('Proses reset password dimulai', ['email' => $request->email]);

        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'token.required' => 'Token reset password tidak ditemukan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
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
        ? redirect()->route('login')->with(
            'status',
            'Password berhasil diubah. Silakan login menggunakan password baru Anda.'
        )
        : back()->withErrors([
            'email' => 'Gagal mereset password. Silakan coba lagi.'
        ]);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }
}