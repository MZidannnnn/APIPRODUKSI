<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Advisel Pramana | Login</title>

    <!-- Font & Icon -->
    <link rel="shortcut icon" href="/favicon.ico?v={{ time() }}">
    <link rel="icon" href="/favicon.ico?v={{ time() }}" type="image/x-icon">
    
    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700,800" rel="stylesheet">

    <!-- SB Admin 2 -->
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        body {
            background: #f8f9fc;
        }

        .login-card {
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,.08);
            border: none;
        }

        .form-control {
            height: 48px;
            border-radius: 8px;
            font-size: 1.2rem;
        }

        .btn-login {
            height: 48px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .login-logo {
            width: 100%;
            max-width: 400px;
            height: auto;
        }

        /* ⛔ HILANGKAN ICON MATA BAWAAN BROWSER */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }

        input[type="password"]::-webkit-credentials-auto-fill-button {
            display: none !important;
        }
    </style>
</head>

<body>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh">
    <div class="col-lg-8 col-md-6 col-sm-10 p-0">

        <div class="card login-card p-4">

            <!-- LOGO -->
            <div class="text-center mb-3">
                <img src="{{ asset('assets/images/logo-adv1.png') }}" class="login-logo mb-3">
                <h4 class="font-weight-bold text-gray-800 mb-1">
                    Aplikasi Pemesanan Jasa Media Promosi Dan Produksi
                </h4>
                <h6 class="text-muted">
                    Advisel Pramana Company
                </h6>
            </div>

            <hr>

            <!-- FORM LOGIN -->
            <form method="POST" action="{{ route('loginAdminProses') }}">
                @csrf

                <!-- EMAIL / USERNAME -->
                <div class="form-group">
                    <input type="text"
                           name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="Email"
                           value="{{ old('email') }}">
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- PASSWORD -->
                <div class="form-group position-relative">
                    <input type="password"
                           name="password"
                           id="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Password">

                    <span class="position-absolute" style="right: 15px; top: 12px; cursor: pointer"
                          onclick="togglePassword()">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </span>

                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- BUTTON -->
                <button type="submit" class="btn btn-primary btn-login btn-block">
                    Login
                </button>
            </form>
            <hr>
            <div class="text-center">
                Kembali Ke Beranda?
                <a href="{{ route('welcomeAdmin') }}">Klik Disini!</a>
            </div>

        </div>

    </div>
</div>


    <!-- Bootstrap core JavaScript -->
    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript -->
    <script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts -->
    <script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>

    <!-- Sweet Alert -->
    <script src="{{ asset('sweetalert2/dist/sweetalert2.all.min.js') }}"></script>

    <!-- Session sukses -->
    @session('success')
        <script>
            Swal.fire({
                title: "Sukses",
                text: "{{ session('success') }}",
                icon: "success"
            });
        </script>
    @endsession

    <!-- Session gagal -->
    @session('error')
        <script>
            Swal.fire({
                title: "Gagal",
                text: "{{ session('error') }}",
                icon: "error"
            });
        </script>
    @endsession

    <!-- TOGGLE PASSWORD -->
    <script>
        function togglePassword() {
            const pass = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');

            if (pass.type === 'password') {
                pass.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                pass.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>

</body>

</html>
