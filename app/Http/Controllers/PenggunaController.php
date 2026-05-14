<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\KategoriUsaha;   
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index($role)
    {
        $user = Pengguna::where('id_role', $role)->get();

        $data = [
            'title' => $title = ($role == 1) ? 'Data Akun Super Admin' : (($role == 2) ? 'Data Akun Admin' : 'Data Akun Klien'),
            'menuKelolaAkun' => 'active',
            'collapseKelolaAkun' => 'show',
            'user' => $user,
            'role' => $role,
        ];

        return view('super-admin/kelola-akun/index', $data);
    }

    public function create($role)
    {
        $data = [
            'title' => $title = ($role == 1) ? 'Tambah Data Akun Super Admin' : (($role == 2) ? 'Tambah Data Akun Admin' : 'Tambah Data Akun Klien'),
            'menuKelolaAkun' => 'active',
            'collapseKelolaAkun' => 'show',
            'role' => $role,
            'kategori' => KategoriUsaha::get()
        ];

        return view('super-admin/kelola-akun/create', $data);
    }

    public function store(Request $request)
    {
        // VALIDASI
        $rules = [
            'nama_pengguna'  => 'required|unique:pengguna,nama_pengguna',
            'email'          => 'required|email|unique:pengguna,email',
            'password'       => 'required|confirmed|min:8',
            'id_role'        => 'required',
        ];

        // id_kategori hanya wajib untuk role admin
        if ($request->id_role == 2) {
            $rules['id_kategori'] = 'required';
        }

        $request->validate($rules, [
            'nama_pengguna.required'     => 'Username wajib diisi',
            'nama_pengguna.unique'       => 'Username sudah digunakan',
            'email.required'             => 'Email wajib diisi',
            'email.email'                => 'Format email tidak valid',
            'email.unique'               => 'Email sudah digunakan',
            'password.required'          => 'Password wajib diisi',
            'password.confirmed'         => 'Konfirmasi password tidak sesuai',
            'password.min'               => 'Password minimal 8 karakter',
            'id_kategori.required'       => 'Kategori wajib dipilih',
        ]);

        // SIMPAN DATA
        Pengguna::create([
            'id_role'       => $request->id_role,
            'id_kategori'   => $request->id_role == 2 ? $request->id_kategori : null,
            'nama_pengguna' => $request->nama_pengguna,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
        ]);

        return redirect()
            ->route('viewKelolaAkun', $request->id_role)
            ->with('success', 'Data akun berhasil ditambahkan');
    }

    public function edit($id)
    {
        $user = Pengguna::findOrFail($id);

        $data = [
            'title' => 'Edit Data Akun',
            'menuKelolaAkun' => 'active',
            'collapseKelolaAkun' => 'show',
            'user' => $user,
            'role' => $user->id_role,
            'kategori' => KategoriUsaha::orderBy('nama_kategori', 'ASC')->get(),
        ];

        return view('super-admin/kelola-akun/edit', $data);
    }

    public function update(Request $request, $id)
    {
        $user = Pengguna::findOrFail($id);

        $rules = [
            'nama_pengguna' => [
                'required',
                Rule::unique('pengguna', 'nama_pengguna')->ignore($id, 'id_pengguna'),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('pengguna', 'email')->ignore($id, 'id_pengguna'),
            ],
        ];

        if ($request->id_role == 2) {
            $rules['id_kategori'] = 'required';
        }

        if ($request->password) {
            $rules['password'] = 'confirmed|min:8';
        }

        $request->validate($rules, [
            'nama_pengguna.required' => 'Username wajib diisi',
            'nama_pengguna.unique' => 'Username sudah digunakan',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'id_kategori.required' => 'Kategori wajib dipilih',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        $user->update([
            'nama_pengguna' => $request->nama_pengguna,
            'email' => $request->email,
            'id_kategori' => $request->id_role == 2 ? $request->id_kategori : null,
        ]);

        if ($request->password) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()
            ->route('viewKelolaAkun', $user->id_role)
            ->with('success', 'Data akun berhasil diperbarui');
    }

    public function destroy($id)
    {
        $user = Pengguna::findOrFail($id);
        $role = $user->id_role;

        $user->delete();

        return redirect()
            ->route('viewKelolaAkun', $role)
            ->with('success', 'Data akun berhasil dihapus');
    }
}

/*public function index()
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
    } */