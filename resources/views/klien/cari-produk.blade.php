@extends('klien.layouts.app')

@section('content')

<div class="produk-page">

    <a href="{{ url()->previous() }}" class="btn-kembali">
        <i class="fas fa-chevron-left"></i> Kembali
    </a>

    <div class="produk-header">
        <h4>Produk</h4>

        @if(request('keyword'))
            <p>Hasil pencarian: <b>{{ request('keyword') }}</b></p>
        @endif
    </div>

    <div class="produk-container">

        <div class="produk-grid">
            @forelse ($produk as $item)
                <a href="{{ route('pesanan.detail', $item->id_item) }}" class="produk-card">

                    <img 
                        src="{{ asset('storage/' . $item->nama_foto) }}" 
                        alt="{{ $item->nama_item }}"
                        class="produk-img"
                    >

                    <h5>{{ $item->nama_item }}</h5>

                    <p>
                        Rp {{ number_format($item->harga_dasar, 0, ',', '.') }}
                    </p>

                </a>
            @empty
                <div class="produk-kosong">
                    Produk tidak ditemukan.
                </div>
            @endforelse
        </div>

        <div class="kategori-box">
            <div class="filter-icon">
                <i class="fas fa-filter"></i>
            </div>

            <div class="kategori-card">
                <h5>Kategori</h5>

                @foreach ($kategori as $item)
                    <a href="{{ route('produk.index', ['kategori' => $item->id_kategori]) }}">
                        {{ $item->nama_kategori }}
                    </a>
                @endforeach
            </div>
        </div>

    </div>

</div>

@endsection