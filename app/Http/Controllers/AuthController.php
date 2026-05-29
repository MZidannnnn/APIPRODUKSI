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

    public function detailProduk($id)
    {
        $item = ItemProduksi::with([
            'kategoriUsaha',
            'detailProduk.satuanHarga',
            'fotoProduk'
        ])->findOrFail($id);

        return view('klien/detail-produk', compact('item'));
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
        $role_klien = 3; // Sesuaikan dengan id_role klien di tabel role

        // Buat akun baru (id_kategori dikosongkan)
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
     * Tampilkan form login Klien
     */
    public function showLoginKlien()
    {
        return view('auth.login');
    }

    /**
     * Tampilkan form login Admin dan Super Admin
     */
    public function showLoginAdmin()
    {
        return view('auth.loginadmin');
    }

    /**
     * Handle login Klien
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
     * Handle login Admin dan Super Admin
     */
        public function loginAdmin(Request $request)
    {
        // VALIDASI
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        // CEK USER BERDASARKAN EMAIL
        $user = pengguna::where('email', $request->email)->first();

        // JIKA USER TIDAK ADA
        if (!$user) {
            return back()->with('error', 'Email atau Password salah!');
        }

        // COBA LOGIN
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {

            // Regenerate session agar lebih aman
            $request->session()->regenerate();

            // Ambil data user yang sedang login
            $user = Auth::user();

            // Arahkan Super Admin ke dashboard Super Admin
            if ((int) $user->id_role === 1) {
                return redirect()->route('dashboardSuperAdmin')
                    ->with('success', 'Anda berhasil login sebagai Super Admin');
            }

            // Arahkan Admin ke dashboard Admin 
            if ((int) $user->id_role === 2) {
                return redirect()->route('dashboardAdmin')
                    ->with('success', 'Anda berhasil login sebagai Admin');
            }

            // Jika bukan Super Admin atau Admin, logout otomatis
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->with('error', 'Akses hanya untuk Admin dan Super Admin');
        }

        return back()->with('error', 'Email atau Password salah!');
    }

    /**
 * Handle logout
 */
public function logout(Request $request)
{
    // Simpan role user sebelum logout
    $role = Auth::user()->id_role;

    // Logout
    Auth::logout();

    // Hapus session
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Redirect berdasarkan role
    if (in_array($role, [1, 2])) {
        return redirect('/admin/privasi')
            ->with('success', 'Berhasil logout');
    }

    if ($role == 3) {
        return redirect('/')
            ->with('success', 'Berhasil logout');
    }

    // Default fallback
    return redirect('/');
}

    /**
     * Handle Forget Password
     */
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
