<!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('welcome') }}">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-address-card"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Advisel Pramana</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item #">
                <a class="nav-link" href=" {{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

             @if ($role == 'Admin')
            <!-- Heading -->
            <div class="sidebar-heading">
                MENU ADMIN
            </div>

            <li class="nav-item #">
                <a class="nav-link" href="#">
                    <i class="fas fa-user"></i>
                    <span>Data Produk Jasa</span></a>
            </li>

            <li class="nav-item #">
                <a class="nav-link" href="#">
                    <i class="fas fa-id-card"></i>
                    <span>Progres Pesanan</span></a>
            </li>

            @elseif ($role == 'Super-Admin')
            <!-- Heading -->
            <div class="sidebar-heading">
                MENU SUPER ADMIN
            </div>

            <li class="nav-item #">
                <a class="nav-link" href="{{ route('') }}">
                    <i class="fas fa-id-card"></i>
                    <span>Kelola Akun</span></a>
            </li>

            <li class="nav-item #">
                <a class="nav-link" href="{{ route('') }}">
                    <i class="fas fa-mobile-alt"></i>
                    <span>Monitoring Data Master</span></a>
            </li>

            @endif

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->