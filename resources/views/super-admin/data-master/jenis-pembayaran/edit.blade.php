@extends('layouts/app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-fw fa-credit-card mr-2"></i>
    {{ $title }}
</h1>

<div class="card">
    <div class="card-header">
        <a href="{{ route('jenisPembayaran.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="card-body">
        <form action="{{ route('jenisPembayaran.update', $jenisPembayaran->id_jenis_pembayaran) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Nama Jenis Pembayaran --}}
            <div class="form-group">
                <label>Nama Jenis Pembayaran</label>

                <input type="text"
                    name="nama_jenis_pembayaran"
                    class="form-control @error('nama_jenis_pembayaran') is-invalid @enderror"
                    value="{{ old('nama_jenis_pembayaran', $jenisPembayaran->nama_jenis_pembayaran) }}"
                    placeholder="Masukkan nama jenis pembayaran">
                @error('nama_jenis_pembayaran')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Button --}}
            <div class="d-flex justify-content-end">
                <button type="submit"
                    class="btn btn-warning">
                    <i class="fas fa-save mr-1"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection