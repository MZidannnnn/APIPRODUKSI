@extends('layouts/app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-fw fa-layer-group mr-2"></i>
    {{ $title }}
</h1>

<div class="card">
    <div class="card-header">
            <!-- Export Buttons Create -->
            <a href="{{ route('satuanHarga.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        
    </div>

    <div class="card-body">
        <form action="{{ route('satuanHarga.store') }}" method="POST">
            @csrf

            {{-- Nama Satuan Harga --}}
            <div class="form-group">
                <label>Nama Satuan Harga</label>

                <input type="text"
                    name="nama_satuan"
                    class="form-control @error('nama_satuan') is-invalid @enderror"
                    value="{{ old('nama_satuan') }}"
                    placeholder="Masukkan nama satuan harga">

                @error('nama_satuan')
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
