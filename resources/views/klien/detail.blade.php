@extends('klien.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('fe-klien/detail-produk.css') }}?v={{ time() }}">
@endpush

@section('content')
@php
    $hargaMin = $item->detailProduk->min('harga_dasar');
    $satuan = $item->satuanHarga->nama_satuan ?? $item->detailProduk->first()?->satuanHarga?->nama_satuan;
@endphp

<div class="detail-produk-wrapper">
    <div class="back-button-container">
        <a href="{{ url()->previous() }}" class="btn-kembali">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="produk-detail-content">
        <div class="produk-gallery">
            <div class="gallery-track-wrapper">
                <div class="gallery-track">
                    @forelse ($item->fotoProduk as $foto)
                        <div class="gallery-item">
                            <img src="{{ asset($foto->nama_foto) }}" alt="Foto Produk">
                        </div>
                    @empty
                        <div class="gallery-item">
                            <img src="{{ asset('assets/images/no-image.png') }}" alt="No Image">
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="order-card">
            <div class="produk-description">
                <h1>{{ $item->nama_item }}</h1>
            </div>

            <div class="produk-order-box">
                <div class="harga-row">
                    <h2>Rp{{ number_format($hargaMin, 0, ',', '.') }}</h2>
                    @if ($satuan)
                        <span class="product-unit">/ {{ $satuan }}</span>
                    @endif
                </div>
                <hr>
                
                <div class="button-action-wrapper">
                    <button class="btn-chat">
                        <i class="fas fa-comments"></i> Tanya Admin
                    </button>
                    <button class="btn-beli">
                        Beli Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection