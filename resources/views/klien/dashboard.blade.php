@extends('klien.layouts.app')

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

        <section class="section">
            <h2>Kategori</h2>

            <div class="kategori-list">
                <a href="#" class="kategori active">All</a>
                <a href="#" class="kategori">Advertising Media</a>
                <a href="#" class="kategori">Productions Event Properties</a>
                <a href="#" class="kategori">Interior & Exterior</a>
                <a href="#" class="kategori">Space Iklan Baliho</a>
                <a href="#" class="kategori">Cetak Spanduk</a>
                <a href="#" class="kategori">Sablon</a>
            </div>
        </section>

        <section class="section">
            <h2>Produk</h2>

            <div class="produk-grid">
                <div class="produk-card">
                    <div class="produk-img"></div>
                    <h3>Neon Box</h3>
                    <p>Rp -------</p>
                </div>

                <div class="produk-card">
                    <div class="produk-img"></div>
                    <h3>Sablon</h3>
                    <p>Rp -------</p>
                </div>

                <div class="produk-card">
                    <div class="produk-img"></div>
                    <h3>Gate Event</h3>
                    <p>Rp -------</p>
                </div>

                <div class="produk-card">
                    <div class="produk-img"></div>
                    <h3>Space Iklan Baliho</h3>
                    <p>Rp -------</p>
                </div>

                <div class="produk-card">
                    <div class="produk-img"></div>
                    <h3>Cetak Spanduk</h3>
                    <p>Rp -------</p>
                </div>
            </div>
        </section>

@endsection