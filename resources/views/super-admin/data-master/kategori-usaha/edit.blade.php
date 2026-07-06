@extends('layouts/app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-fw fa-tags mr-2"></i>
    {{ $title }}
</h1>

<div class="card">
    <div class="card-header">
        <a href="{{ route('kategoriUsaha.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="card-body">
        <form action="{{ route('kategoriUsaha.update', $kategori->id_kategori) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Nama Kategori --}}
            <div class="form-group">
                <label>Nama Kategori Usaha</label>

                <input type="text"
                    name="nama_kategori"
                    class="form-control @error('nama_kategori') is-invalid @enderror"
                    value="{{ old('nama_kategori', $kategori->nama_kategori) }}"
                    placeholder="Masukkan nama kategori usaha">
                @error('nama_kategori')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- kode unik --}}
            <div class="form-group">
                <label>Kode Unik</label>
                <input type="text"
                    name="kode_unik"
                    class="form-control @error('kode_unik') is-invalid @enderror"
                    value="{{ old('kode_unik', $kategori->kode_unik) }}"
                    maxlength="10"
                    pattern="[A-Z0-9]+"
                    oninput="this.value = this.value.toUpperCase()"
                    placeholder="Contoh: SBL"
                    style="text-transform: uppercase;">
                @error('kode_unik')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Jenis Harga --}}
            <div class="form-group">
                <label>Bidang Layanan</label>

                <select name="bidang_layanan"
                    class="form-control @error('bidang_layanan') is-invalid @enderror">
                    <option value="">
                        -- Pilih Bidang Layanan --
                    </option>
                    <option value="Media Promosi"
                        {{ old('bidang_layanan', $kategori->bidang_layanan) == 'Media Promosi' ? 'selected' : '' }}>
                        Media Promosi
                    </option>

                    <option value="Produksi"
                        {{ old('bidang_layanan', $kategori->bidang_layanan) == 'Produksi' ? 'selected' : '' }}>
                        Produksi
                    </option>
                </select>

                @error('bidang_layanan')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Jenis Pembayaran --}}
            <div class="form-group">
                <label>Jenis Pembayaran</label>

                <select name="id_jenis_pembayaran"
                    class="form-control @error('id_jenis_pembayaran') is-invalid @enderror">
                    <option value="">
                        -- Pilih Jenis Pembayaran --
                    </option>
                    @foreach ($jenisPembayaran as $item)
                        <option value="{{ $item->id_jenis_pembayaran }}"
                            {{ old('id_jenis_pembayaran', $kategori->id_jenis_pembayaran) == $item->id_jenis_pembayaran ? 'selected' : '' }}>
                            {{ $item->nama_jenis_pembayaran }}
                        </option>
                    @endforeach
                </select>

                @error('id_jenis_pembayaran')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            {{-- Deskripsi --}}
            <div class="form-group">
                <label>Deskripsi</label>

                <textarea name="deskripsi"
                    rows="4"
                    class="form-control @error('deskripsi') is-invalid @enderror"
                    placeholder="Masukkan deskripsi kategori usaha">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>

                @error('deskripsi')
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