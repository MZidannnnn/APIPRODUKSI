@extends('klien.layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('fe-klien/detail-produk.css') }}">
@endpush

@section('content')
<a href="{{ route('dashboard') }}" class="btn-kembali"> <- Kembali</a>

<section class="detail-produk">

    <!-- Galeri Foto Produk -->
    <div class="produk-gallery">
        <button type="button" class="gallery-btn prev-btn">
            <i class="fas fa-chevron-left"></i>
        </button>

        <div class="gallery-track-wrapper">
            <div class="gallery-track" id="galleryTrack">

                <div class="gallery-item">
                    <img src="{{ asset('assets/images/produk-1.png') }}" alt="Produk 1">
                </div>

                <div class="gallery-item">
                    <img src="{{ asset('assets/images/produk-2.png') }}" alt="Produk 2">
                </div>

                <div class="gallery-item">
                    <img src="{{ asset('assets/images/produk-3.png') }}" alt="Produk 3">
                </div>

                <div class="gallery-item">
                    <img src="{{ asset('assets/images/produk-4.png') }}" alt="Produk 4">
                </div>

            </div>
        </div>

        <button type="button" class="gallery-btn next-btn">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>

    <!-- Informasi Produk -->
    <div class="produk-detail-content">

        <div class="produk-description">
            <h1>Space Iklan Baliho</h1>

            <p>
                Jasa Space Iklan Baliho merupakan layanan penyediaan media promosi luar ruang
                (outdoor advertising) berupa baliho yang ditempatkan di lokasi strategis dengan
                tingkat lalu lintas tinggi. Media ini bertujuan untuk meningkatkan visibilitas merek,
                produk, atau layanan kepada masyarakat secara luas dan berkelanjutan.
            </p>
        </div>

        <div class="produk-order-box">

            <div class="harga-row">
                <h2>Rp 2.000.000</h2>
                <span>Per Meter</span>
            </div>

            <hr>

            <div class="form-group">
                <label>Ukuran</label>

                <div class="size-options">
                    <button type="button">16 x 8</button>
                    <button type="button">20 x 5</button>
                    <button type="button">10 x 5</button>
                    <button type="button">8 x 4</button>
                    <button type="button">4 x 6</button>
                </div>
            </div>

            <hr>

            <div class="form-group">
                <label>Kuantitas</label>

                <div class="qty-box">
                    <button type="button" id="minusQty">-</button>
                    <input type="text" id="qtyInput" value="1" readonly>
                    <button type="button" id="plusQty">+</button>
                </div>
            </div>

            <hr>

            <div class="form-group">
                <label>Jadwal Pemasangan</label>
                <input type="date" class="date-input">
            </div>

            <hr>

            <div class="button-buy-wrapper">
                <button type="button" class="btn-beli">
                    Beli Sekarang
                </button>
            </div>

        </div>

    </div>

</section>

@endsection

@push('scripts')
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