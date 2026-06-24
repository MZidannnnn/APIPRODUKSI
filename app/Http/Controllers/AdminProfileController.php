<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AdminProfileController extends Controller
{ 
    public function edit()
    {
        $user = Auth::user()->load('kategori');

        $data = [
            'title' => 'Profil Admin',
            'user' => $user,
        ];

        return view('admin.profile', $data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'password_lama' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password_lama.required' => 'Password lama wajib diisi',
            'password_lama.current_password' => 'Password lama tidak sesuai',
            'password.required' => 'Password baru wajib diisi',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        $user = Auth::user();

        $user->update([
            'password' => $request->password,
        ]);

        return redirect()
            ->route('admin.profile.edit')
            ->with('success', 'Password profil berhasil diperbarui');
    }
}