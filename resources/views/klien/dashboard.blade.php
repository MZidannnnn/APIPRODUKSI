@extends('klien.layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('fe-klien/dashboard-v2.css') }}">
@endpush

@section('content')

<section class="profile-card">
    <div class="profile-text">
        <h1>ADVISEL PRAMANA</h1>

        <p>
            ADVISEL PRAMANA adalah suatu badan usaha yang berdiri sejak Tahun 2020 hingga sekarang.
            Tergabung dalam CV. Hibatullah Berkah Jaya sebagai alternatif bagi klien kami, saat ini kami
            juga memperluas jangkauan promosi melalui jasa konsep acara, konsep promosi, yang didukung
            oleh sumber daya yang profesional dan handal.
        </p>

        <div class="social-icons">
            <a href="#"><i class="fab fa-whatsapp"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="far fa-envelope"></i></a>
        </div>
    </div>

    <div class="profile-logo">
        <img src="{{ asset('assets/images/logo-profile.png') }}" alt="Logo Advisel">
    </div>
</section>

{{-- =========================
    KATEGORI
========================= --}}
<section class="section">
    <h2>Kategori</h2>

    <div class="kategori-list">

        {{-- ALL --}}
        <a href="{{ route('dashboard') }}"
           class="kategori {{ empty($selectedKategoriId) ? 'active' : '' }}">
            All
        </a>

        {{-- KATEGORI DATABASE --}}
        @foreach ($kategoriUsaha as $kategori)

            <a href="{{ route('dashboard', ['kategori' => $kategori->id_kategori]) }}"
               class="kategori {{ $selectedKategoriId == $kategori->id_kategori ? 'active' : '' }}">

                {{ $kategori->nama_kategori }}

            </a>

        @endforeach

    </div>
</section>

{{-- PRODUK --}}
<section class="section">
    <h2>Produk</h2>

    <div class="produk-grid">

        @forelse ($itemProduksi as $item)

            @php

                // Ambil harga minimum
                $hargaMin = $item->detailProduk->min('harga_dasar');

                // Ambil harga maksimum
                $hargaMax = $item->detailProduk->max('harga_dasar');

                // Ambil satuan pertama
                $satuan = $item->detailProduk->first()?->satuanHarga?->nama_satuan_harga;

                // Ambil foto pertama
                $foto = $item->fotoProduk->first();
 
            @endphp

            <a href="{{ route('produk.detail', $item->id_item_produksi) }}" class="produk-card">

                {{-- FOTO --}}
                <div class="produk-img">
                    @if ($foto)
                        <img src="{{ asset($foto->nama_foto) }}"
                            alt="{{ $item->nama_item }}">
                    @else
                        <img src="{{ asset('assets/images/no-image.png') }}"
                            alt="No Image">
                    @endif
                </div>

                {{-- NAMA PRODUK --}}
                <h3>
                    {{ $item->nama_item }}
                </h3>

                {{-- RANGE HARGA --}}
                <p>
                    @if ($hargaMin && $hargaMax)
                        @if ($hargaMin == $hargaMax)
                            Rp {{ number_format($hargaMin, 0, ',', '.') }}
                        @else
                            Rp {{ number_format($hargaMin, 0, ',', '.') }}
                            -
                            {{ number_format($hargaMax, 0, ',', '.') }}
                        @endif

                        @if ($satuan)
                            / {{ $satuan }}
                        @endif
                    @else
                        Harga belum tersedia
                    @endif
                </p>

            </a>
        @empty

            <p>Produk belum tersedia.</p>

        @endforelse

    </div>
</section>

@endsection

@push('scripts')
    <script>
        const profileButton = document.getElementById('profileButton');
        const profileDropdown = document.getElementById('profileDropdown');

        if (profileButton) {
            profileButton.addEventListener('click', function () {
                profileDropdown.classList.toggle('show');
            });

            document.addEventListener('click', function (event) {
                if (!event.target.closest('.profile-wrapper')) {
                    profileDropdown.classList.remove('show');
                }
            });
        }
    </script>

    <script>
        const galleryTrack = document.getElementById('galleryTrack');
        const prevButton = document.querySelector('.prev-btn');
        const nextButton = document.querySelector('.next-btn');

        let currentSlide = 0;

        function updateGallery() {
            const itemWidth = document.querySelector('.gallery-item').offsetWidth + 50;
            galleryTrack.style.transform = `translateX(-${currentSlide * itemWidth}px)`;
        }

        nextButton.addEventListener('click', function () {
            const totalItems = document.querySelectorAll('.gallery-item').length;
            const visibleItems = window.innerWidth <= 768 ? 1 : 3;

            if (currentSlide < totalItems - visibleItems) {
                currentSlide++;
                updateGallery();
            }
        });

        prevButton.addEventListener('click', function () {
            if (currentSlide > 0) {
                currentSlide--;
                updateGallery();
            }
        });

        window.addEventListener('resize', updateGallery);

        const minusQty = document.getElementById('minusQty');
        const plusQty = document.getElementById('plusQty');
        const qtyInput = document.getElementById('qtyInput');

        minusQty.addEventListener('click', function () {
            let qty = parseInt(qtyInput.value);

            if (qty > 1) {
                qtyInput.value = qty - 1;
            }
        });

        plusQty.addEventListener('click', function () {
            let qty = parseInt(qtyInput.value);
            qtyInput.value = qty + 1;
        });
    </script>
@endpush