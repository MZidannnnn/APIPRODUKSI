@extends('layouts/app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-fw fa-user-plus mr-2"></i>
    {{ $title }}
</h1>

<div class="card">
    <div class="card-header">
            <!-- Export Buttons Create -->
            <a href="{{ route('viewKelolaAkun', $role) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        
    </div>

    <div class="card-body">
        <form action="{{ route('kelolaAkunStore') }}" method="POST">
            @csrf

            <!-- Hidden Role -->
            <input type="hidden" name="id_role" value="{{ $role }}">

            {{-- Username --}}
            <div class="form-group">
                <label>Username</label>

                <input type="text"
                    name="nama_pengguna"
                    class="form-control @error('nama_pengguna') is-invalid @enderror"
                    value="{{ old('nama_pengguna') }}"
                    placeholder="Masukkan username">

                @error('nama_pengguna')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label>Email</label>

                <input type="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    placeholder="Masukkan email">

                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Kategori Usaha --}}
            @if ($role == 2)
                <div class="form-group">
                    <label>Kategori Usaha</label>

                    <select name="id_kategori"
                        class="form-control @error('id_kategori') is-invalid @enderror">

                        <option value="">-- Pilih Kategori --</option>

                        @foreach ($kategori as $item)
                            <option value="{{ $item->id_kategori }}"
                                {{ old('id_kategori') == $item->id_kategori ? 'selected' : '' }}>

                                {{ $item->nama_kategori }}
                            </option>
                        @endforeach
                    </select>

                    @error('id_kategori')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endif

            {{-- Password --}}
            <div class="form-group">
                <label>Password</label>

                <input type="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Masukkan password">

                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div class="form-group">
                <label>Konfirmasi Password</label>

                <input type="password"
                    name="password_confirmation"
                    class="form-control"
                    placeholder="Konfirmasi password">
            </div>

            {{-- Button --}}
            <div class="d-flex justify-content-end">

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save mr-1"></i>
                    Simpan
                </button>

            </div>

        </form>

    </div>
</div>

@endsection
