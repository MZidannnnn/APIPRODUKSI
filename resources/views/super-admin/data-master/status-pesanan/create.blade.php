@extends('layouts/app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-fw fa-clipboard-check mr-2"></i>
    {{ $title }}
</h1>

<div class="card">
    <div class="card-header">
            <!-- Export Buttons Create -->
            <a href="{{ route('statusPesanan.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        
    </div>

    <div class="card-body">
        <form action="{{ route('statusPesanan.store') }}" method="POST">
            @csrf

            {{-- Nama Status Pesanan --}}
            <div class="form-group">
                <label>Nama Status Pesanan</label>

                <input type="text"
                    name="nama_status_pesanan"
                    class="form-control @error('nama_status_pesanan') is-invalid @enderror"
                    value="{{ old('nama_status_pesanan') }}"
                    placeholder="Masukkan nama status pesanan">

                @error('nama_status_pesanan')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

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
