<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Klien</title>

    <link rel="stylesheet" href="{{ asset('fe-klien/auth-klien.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .input-error {
            border: 1px solid #dc3545 !important;
        }

        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: -8px;
            margin-bottom: 10px;
            text-align: left;
            width: 100%;
        }

        .alert-error {
            background: #ffe5e5;
            color: #b00020;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 15px;
            text-align: left;
        }

        .alert-success {
            background: #e6f7ec;
            color: #087f3f;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 15px;
            text-align: left;
        }
    </style>
</head>
<body>

    <img src="{{ asset('assets/images/bg-top-left.png') }}" class="auth-bg bg-left-top">
    <img src="{{ asset('assets/images/bg-top-right.png') }}" class="auth-bg bg-right-top">
    <img src="{{ asset('assets/images/bg-bottom-left.png') }}" class="auth-bg bg-left-bottom">
    <img src="{{ asset('assets/images/bg-bottom-right.png') }}" class="auth-bg bg-right-bottom">

    <main class="auth-wrapper">
        <div class="auth-card">

            <img src="{{ asset('assets/images/logo.png') }}"
                class="auth-logo"
                alt="Logo">

            <h1>Log in Ke Akun Anda</h1>

            @if (session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert-error">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="auth-form">
                @csrf

                <input type="text"
                    name="nama_pengguna"
                    placeholder="Username"
                    value="{{ old('nama_pengguna') }}"
                    class="@error('nama_pengguna') input-error @enderror">

                @error('nama_pengguna')
                    <div class="error-message">
                        {{ $message }}
                    </div>
                @enderror

                <div class="password-wrapper">
                    <input type="password"
                        name="password"
                        placeholder="Password"
                        class="password-input @error('password') input-error @enderror">

                    <i class="fa-solid fa-eye toggle-password"></i>
                </div>

                @error('password')
                    <div class="error-message">
                        {{ $message }}
                    </div>
                @enderror

                <div class="forgot-link">
                    <a href="{{ route('password.request') }}">Lupa Password?</a>
                </div>

                <button type="submit">
                    Log In
                </button>
            </form>

            <p class="auth-link">
                Belum punya akun?
                <a href="{{ route('register') }}">Sign Up</a>
            </p>

        </div>
    </main>

    <script>
        const togglePasswordButtons = document.querySelectorAll('.toggle-password');

        togglePasswordButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const passwordInput = this.previousElementSibling;

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                }
            });
        });
    </script>

</body>
</html>