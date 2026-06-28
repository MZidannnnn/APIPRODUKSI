<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class KlienProfileController extends Controller
{
    public function index()
    {
        return view('klien.profile', [
            'title' => 'Profil Saya',
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama_pengguna' => 'required|string|max:255',
            'email' => ['required','email',Rule::unique('pengguna', 'email')->ignore($user->id_pengguna, 'id_pengguna')],
            'password_lama' => 'nullable|required_with:password',
            'password' => 'nullable|min:6',
        ], 
        [
            'nama_pengguna.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password_lama.required_with' => 'Password lama wajib diisi jika ingin mengubah password.',
            'password.min' => 'Password baru minimal 6 karakter.',
        ]);

        // Jika user ingin ubah password
        if ($request->filled('password')) {
            if (!Hash::check($request->password_lama, $user->password)) {
                return back()
                    ->withErrors([
                        'password_lama' => 'Password lama tidak sesuai.'
                    ])
                    ->withInput();
            }

            $user->password = $request->password;
        }

        // Update data profil
        $user->nama_pengguna = $request->nama_pengguna;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}