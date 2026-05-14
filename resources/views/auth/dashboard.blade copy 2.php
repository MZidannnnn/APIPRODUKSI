<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advisel Pramana</title>

    <link rel="stylesheet" href="{{ asset('fe-klien/klien.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

    <!-- Header -->
    <header class="header">

        <img src="{{ asset('assets/images/bg-header-left.png') }}" class="bg bg-header-left">
        <img src="{{ asset('assets/images/bg-header-right.png') }}" class="bg bg-header-right">

        <div class="header-content">
            <div class="logo">
                <img src="{{ asset('assets/images/logo-side.png') }}" alt="Logo">
            </div>

            <form class="search-box">
                <input type="text" placeholder="Cari Produk">
                <button type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <div class="auth-button">

                @guest
                    <a href="{{ route('login') }}" class="btn">Log In</a>
                    <a href="{{ route('register') }}" class="btn">Sign Up</a>
                @endguest

                @auth
                    <a href="#" class="chat-icon">
                        <i class="fas fa-message"></i>
                    </a>

                    <div class="profile-wrapper">
                        <button type="button" class="profile-button" id="profileButton">
                            <i class="fas fa-user-circle"></i>
                            <span>{{ Auth::user()->nama_pengguna }}</span>
                        </button>

                        <div class="profile-dropdown" id="profileDropdown">

                            <div class="dropdown-header">
                                <div class="dropdown-user">
                                    <i class="fas fa-user-circle"></i>
                                    <span>{{ Auth::user()->nama_pengguna }}</span>
                                </div>

                                <a href="#" class="setting-icon">
                                    <i class="fas fa-gear"></i>
                                </a>
                            </div>

                            <div class="dropdown-body">
                                <div class="dropdown-title">
                                    <strong>Riwayat Pesanan</strong>
                                    <a href="#">Lihat Semua &gt;</a>
                                </div>

                                <div class="order-menu">
                                    <a href="#">
                                        <i class="fas fa-wallet"></i>
                                        <span>Bayar Sekarang</span>
                                    </a>

                                    <a href="#">
                                        <i class="fas fa-box-open"></i>
                                        <span>Pesanan Di Proses</span>
                                    </a>

                                    <a href="#">
                                        <i class="fas fa-screwdriver-wrench"></i>
                                        <span>Belum Lunas</span>
                                    </a>

                                    <a href="#">
                                        <i class="fas fa-cube"></i>
                                        <span>Pesanan Selesai</span>
                                    </a>
                                </div>

                                <div class="logout-form">
                                    <a href="{{ route('logout') }}" class="logout-btn">
                                        Log Out
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                @endauth

            </div>
        </div>
    </header>

    <main class="container">

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

    </main>

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

</body>
</html>