@include('klien.layouts.header')

<body>

    <!-- Header -->
    <header class="header">

        <img src="{{ asset('assets/images/bg-header-left.png') }}" class="bg bg-header-left">
        <img src="{{ asset('assets/images/bg-header-right.png') }}" class="bg bg-header-right">

        <div class="header-content">
            <div class="logo">
                <img src="{{ asset('assets/images/logo-side.png') }}" alt="Logo">
            </div>

            <form class="search-box" action="{{ route('klien.search') }}" method="GET" id="searchForm">
                <div class="search-input-wrapper" style="position: relative; width: 100%;">
                    <input type="text" name="q" id="searchInput" placeholder="Cari Produk" autocomplete="off" value="{{ request('q') }}">
                    
                    <ul id="searchDropdown" class="search-dropdown" style="display: none; position: absolute; top: 100%; left: 0; width: 100%; background: white; border: 1px solid #ddd; list-style: none; padding: 0; z-index: 999;"></ul>
                </div>
                
                <button type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <div id="searchDropdown" class="search-dropdown"></div>
        </form>

            <div class="auth-button">

                @guest
                    <a href="{{ route('login') }}" class="btn">Log In</a>
                    <a href="{{ route('register') }}" class="btn">Sign Up</a>
                @endguest

                @auth
                    <a href="{{ route('chat.index') }}" class="chat-icon">
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
                                        <span>Belum Bayar</span>
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
        @yield('content')
    </main>

    @include('klien.layouts.footer')