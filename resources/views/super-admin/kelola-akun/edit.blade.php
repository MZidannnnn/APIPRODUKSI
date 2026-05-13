@extends('layouts/app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-fw fa-user-edit mr-2"></i>
    {{ $title }}
</h1>

<div class="card">
    <div class="card-header">
        <a href="{{ route('viewKelolaAkun', $role) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="card-body">
        <form action="{{ route('kelolaAkunUpdate', $user->id_pengguna) }}" method="POST">
            @csrf
            @method('PUT')

            <input type="hidden" name="id_role" value="{{ $role }}">

            <div class="form-group">
                <label>Username</label>

                <input type="text"
                    name="nama_pengguna"
                    class="form-control @error('nama_pengguna') is-invalid @enderror"
                    value="{{ old('nama_pengguna', $user->nama_pengguna) }}"
                    placeholder="Masukkan username">

                @error('nama_pengguna')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Email</label>

                <input type="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $user->email) }}"
                    placeholder="Masukkan email">

                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @if ($role == 2)
                <div class="form-group">
                    <label>Kategori Usaha</label>

                    <select name="id_kategori"
                        class="form-control @error('id_kategori') is-invalid @enderror">

                        <option value="">-- Pilih Kategori --</option>

                        @foreach ($kategori as $item)
                            <option value="{{ $item->id_kategori }}"
                                {{ old('id_kategori', $user->id_kategori) == $item->id_kategori ? 'selected' : '' }}>
                                {{ $item->nama_kategori }}
                            </option>
                        @endforeach
                    </select>

                    @error('id_kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            <div class="form-group">
                <label>Password Baru</label>

                <input type="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Kosongkan jika tidak ingin mengubah password">

                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Konfirmasi Password Baru</label>

                <input type="password"
                    name="password_confirmation"
                    class="form-control"
                    placeholder="Konfirmasi password baru">
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save mr-1"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection