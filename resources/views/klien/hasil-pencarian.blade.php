@extends('klien.layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('fe-klien/hasil-pencarian.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="search-result-container">
    <div class="search-result-header">
        <a href="{{ url('/') }}" class="btn-kembali-search">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <h2 class="search-title">Hasil Pencarian untuk: <span>"{{ $keyword }}"</span></h2>
        <p class="search-count">Menampilkan {{ $itemProduksi->count() }} produk ditemukan</p>
    </div>

    <div class="product-grid-wrapper">
        @forelse ($itemProduksi as $item)
            @php
                // Ambil foto pertama, jika kosong pakai gambar default
                $foto = $item->fotoProduk?->first();
                $gambarPath = $foto ? asset($foto->nama_foto) : asset('assets/images/no-image.png');
                
                // Hitung rentang harga dari detail produk
                $hargaMin = $item->detailProduk?->min('harga_dasar');
                $hargaMax = $item->detailProduk?->max('harga_dasar');
                
                // Ambil satuan harga
                $satuan = $item->satuanHarga?->nama_satuan ?? $item->detailProduk?->first()?->satuanHarga?->nama_satuan;
            @endphp

            <div class="product-card">
                <div class="product-card-image">
                    <img src="{{ $gambarPath }}" alt="{{ $item->nama_item }}" loading="lazy">
                    <span class="category-badge">{{ $item->kategoriUsaha?->nama_kategori ?? 'Umum' }}</span>
                </div>

                <div class="product-card-body">
                    <h3 class="product-name" title="{{ $item->nama_item }}">{{ $item->nama_item }}</h3>
                    <p class="product-description">
                        {{ Str::limit(strip_tags($item->deskripsi_item), 65, '...') }}
                    </p>
                    
                    <div class="product-price-box">
                        @if ($hargaMin)
                            <span class="price-label">Mulai dari</span>
                            <div class="price-value">
                                Rp{{ number_format($hargaMin, 0, ',', '.') }}
                                @if ($satuan)
                                    <span class="price-unit">/ {{ $satuan }}</span>
                                @endif
                            </div>
                        @else
                            <span class="price-empty">Harga belum tersedia</span>
                        @endif
                    </div>
                </div>

                <div class="product-card-footer">
                    <a href="{{ route('pesanan.detail', ['id' => $item->id_item_produksi, 'from' => 'search']) }}" class="btn-detail-product">
                        Lihat Detail
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-search-state">
                <div class="empty-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <h3>Waduh, produk tidak ditemukan!</h3>
                <p>Coba gunakan kata kunci lain atau periksa kembali ejaan tulisanmu.</p>
                <a href="{{ url('/') }}" class="btn-back-home">Belanja Kembali</a>
            </div>
        @endforelse
    </div>
</div>
@endsection