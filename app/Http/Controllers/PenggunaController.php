<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\KategoriUsaha;
use App\Models\pengguna;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index()
    {
        $query = pengguna::query();

        // Admin hanya melihat klien (role 3)
        if ((int) Auth::user()->id_role === 2) {
            $query->where('id_role', 3);
        }

        $pengguna = $query->paginate(10);

        return view('pengguna.index', compact('pengguna'));
    }

    public function create()
    {
        $kategori = KategoriUsaha::all();

        // List role sesuai hak akses
        $roles = (int) Auth::user()->id_role === 2
            ? [3 => 'Klien']
            : [1 => 'Super Admin', 2 => 'Admin', 3 => 'Klien'];

        return view('pengguna.create', compact('kategori', 'roles'));
    }

    public function store(Request $request)
    {
        $allowedRoles = (int) Auth::user()->id_role === 2 ? [3] : [1, 2, 3];

        $validated = $request->validate([
            'nama_pengguna' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:pengguna,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'id_role' => ['required', 'integer', 'in:' . implode(',', $allowedRoles)],
            'id_kategori' => ['nullable', 'integer', 'exists:kategori_usaha,id_kategori'],
        ]);

        pengguna::create($validated);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna ditambahkan.');
    }

    public function edit(pengguna $pengguna)
    {
        // Admin tidak boleh edit role 1/2
        if ((int) Auth::user()->id_role === 2 && (int) $pengguna->id_role !== 3) {
            abort(403);
        }

        $kategori = KategoriUsaha::all();
        $roles = (int) Auth::user()->id_role === 2
            ? [3 => 'Klien']
            : [1 => 'Super Admin', 2 => 'Admin', 3 => 'Klien'];

        return view('pengguna.edit', compact('pengguna', 'kategori', 'roles'));
    }

    public function update(Request $request, pengguna $pengguna)
    {
        if ((int) Auth::user()->id_role === 2 && (int) $pengguna->id_role !== 3) {
            abort(403);
        }

        $allowedRoles = (int) Auth::user()->id_role === 2 ? [3] : [1, 2, 3];

        $validated = $request->validate([
            'nama_pengguna' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:pengguna,email,' . $pengguna->id_pengguna . ',id_pengguna'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'id_role' => ['required', 'integer', 'in:' . implode(',', $allowedRoles)],
            'id_kategori' => ['nullable', 'integer', 'exists:kategori_usaha,id_kategori'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $pengguna->update($validated);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna diperbarui.');
    }

    public function destroy(pengguna $pengguna)
    {
        if ((int) Auth::user()->id_role === 2 && (int) $pengguna->id_role !== 3) {
            abort(403);
        }

        $pengguna->delete();

        return redirect()->route('pengguna.index')->with('success', 'Pengguna dihapus.');
    }
}
