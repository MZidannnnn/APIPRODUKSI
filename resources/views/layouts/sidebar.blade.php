<!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href=#>
                <div class="sidebar-brand-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Advisel Pramana</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item #">
                <a class="nav-link" href=#>
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            @if(Auth::user()->id_role == 1)
            <!-- Menu role 1 super admin -->
            <!-- Heading -->
            <div class="sidebar-heading">
                MENU SUPER ADMIN
            </div>

            <!-- Kelola Akun -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Kelola Akun</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href=#>Super Admin</a>
                        <a class="collapse-item" href=#>Admin</a>
                        <a class="collapse-item" href=#>Klien</a>
                    </div>
                </div>
            </li>

            <!-- Monitoring Data Master -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-tools"></i>
                    <span>Data Master</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="utilities-color.html">Kategori Usaha</a>
                        <a class="collapse-item" href="utilities-border.html">Status Pesanan</a>
                        <a class="collapse-item" href="utilities-animation.html">Satuan Harga</a>
                        <a class="collapse-item" href="utilities-other.html">Jenis Pembayaran</a>
                    </div>
                </div>
            </li>

            <!-- Laporan Penjualan -->
            <li class="nav-item #">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-folder-open"></i>
                    <span>Laporan Penjualan</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">
        @endif

        @if(in_array(Auth::user()->id_role, [1,2]))
            <!-- Menu role 2 admin -->
            <!-- Heading -->
            <div class="sidebar-heading">
                MENU ADMIN
            </div>

            <li class="nav-item #">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-box"></i>
                    <span>Data Produk Jasa</span></a>
            </li>

            <li class="nav-item #">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-shipping-fast"></i>
                    <span>Progres Pesanan</span></a>
            </li>

            <li class="nav-item #">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-file-invoice-dollar"></i>
                    <span>Perbarui Tagihan Pesanan</span></a>
            </li>

            <li class="nav-item #">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-receipt"></i>
                    <span>Riwayat Transaksi</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        @endif

        </ul>
        <!-- End of Sidebar -->