@include('layouts/header')

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        @include('layouts/sidebar')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        @if(Auth::user()->id_role == 2)
                            @php
                                $admin = Auth::user();
                                
                                // Hitung total pesan global yang belum dibaca khusus untuk kategori admin ini
                                $totalUnreadMessages = \App\Models\Pesan::whereHas('percakapan', function($q) use ($admin) {
                                        // Pastikan percakapannya sesuai dengan kategori admin
                                        $q->where('id_kategori', $admin->id_kategori);
                                    })
                                    ->whereNull('dibaca_pada') // Pesan belum dibaca
                                    ->where('id_pengirim', '!=', $admin->id_pengguna) // Pesan bukan dari admin itu sendiri
                                    ->count(); 
                            @endphp

                            <li class="nav-item no-arrow mx-1">
                                <a class="nav-link" href="{{ route('admin.chat.index') }}">
                                    <i class="fas fa-envelope fa-fw"></i>

                                    @if($totalUnreadMessages > 0)
                                        <span class="badge badge-danger badge-counter">
                                            {{ $totalUnreadMessages > 9 ? '9+' : $totalUnreadMessages }}
                                        </span>
                                    @endif
                                </a>
                            </li>

                            <div class="topbar-divider d-none d-sm-block"></div>
                        @endif
                        
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    {{ Auth::user()->nama_pengguna }}
                                </span>
                                <img class="img-profile rounded-circl e"
                                    src="{{ asset('sbadmin2/img/undraw_profile.svg') }}">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    @if(Auth::user()->id_role == 2)
                                        {{-- Khusus Admin --}}
                                        <div class="badge badge-info justify-content-center d-flex flex-column align-items-center">
                                            <span>{{ Auth::user()->nama_role }}</span>
                                            <!-- <small>{{ Auth::user()->kategori->nama_kategori ?? '-' }}</small> -->
                                        </div>

                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
                                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                            Profile
                                        </a>
                                    @else {{--Super Admin atau peran lain (perlu dibedah ini)--}} 
                                        {{-- Super Admin --}}
                                        <div class="badge badge-success justify-content-center d-flex">
                                            {{ Auth::user()->nama_role }}
                                        </div>
                                    @endif
                                </a>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    @yield('content')

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; ADVISEL PRAMANA 2026</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    @include('layouts/footer')