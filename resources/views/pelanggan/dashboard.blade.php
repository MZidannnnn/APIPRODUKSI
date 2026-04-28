<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Advisel Pramana</title>
    <link rel="stylesheet" href="{{ asset('fe-pelanggan/dashboard.css') }}">
</head>
<body>

    <header class="navbar">
        <img src="{{ asset('assets/images/bg-top-left.png') }}" class="nav-decor left">
        <img src="{{ asset('assets/images/bg-top-right.png') }}" class="nav-decor right">

        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="height: 50px; z-index: 10;">

        <div class="search-wrapper">
            <input type="text" class="search-input" placeholder="Cari Produk">
            <button class="search-btn">🔍</button>
        </div>

        <div class="user-profile">
            <img src="{{ asset('assets/images/user-icon.png') }}" alt="User" style="width: 35px; border-radius: 50%; border: 1px solid #333; cursor: pointer;">
        </div>
    </header>

    <main class="container">
        <section class="about-card">
            <div class="about-text">
                <h1>ADVISEL PRAMANA</h1>
                <p>
                    ADVISEL PRAMANA adalah suatu badan usaha yang berdiri sejak Tahun 2020 hingga sekarang. 
                    Tergabung dalam CV. Hibatullah Berkah Jaya sebagai alternatif bagi klien kami, saat ini kami 
                    juga memperluas jangkauan promosi melalui jasa konsep acara, konsep promosi, yang 
                    didukung oleh sumber daya yang profesional dan handal.
                </p>
                <div class="social-links">
                    <div class="icon-circle">📞</div>
                    <div class="icon-circle">📸</div>
                    <div class="icon-circle">✉️</div>
                </div>
            </div>
            <div class="about-logo-wrapper">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Main Logo">
            </div>
        </section>

        <h3 class="section-title">Kategori</h3>
        <div class="category-flex">
            <button class="btn-cat active">All</button>
            <button class="btn-cat">Advertising Media</button>
            <button class="btn-cat">Productions Event Properties</button>
            <button class="btn-cat">Interior & Exterior</button>
            <button class="btn-cat">Space Iklan Baliho</button>
            <button class="btn-cat">Cetak Spanduk</button>
            <button class="btn-cat">Sablon</button>
        </div>

        <h3 class="section-title">Produk</h3>
        <div class="product-grid">
            @php
                $produkList = [
                    ['nama' => 'Neon Box', 'harga' => 'Rp ------'],
                    ['nama' => 'Sablon', 'harga' => 'Rp ------'],
                    ['nama' => 'Gate Event', 'harga' => 'Rp ------'],
                    ['nama' => 'Space Iklan Baliho', 'harga' => 'Rp ------'],
                    ['nama' => 'Cetak Spanduk', 'harga' => 'Rp ------'],
                ];
            @endphp

            @foreach($produkList as $item)
            <div class="product-item">
                <div class="product-img">
                    <span class="heart-icon">♡</span>
                </div>
                <div class="product-info">
                    <div class="product-name">{{ $item['nama'] }}</div>
                    <div class="product-price">{{ $item['harga'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </main>

</body>
</html>