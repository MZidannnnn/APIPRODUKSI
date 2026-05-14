<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sign Up Klien</title>

    <link rel="stylesheet" href="{{ asset('fe-klien/auth-klien.css') }}">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <!-- Background -->
     <img src="{{ asset('assets/images/bg-top-left.png') }}" class="auth-bg bg-left-top">
     <img src="{{ asset('assets/images/bg-top-right.png') }}" class="auth-bg bg-right-top">
     <img src="{{ asset('assets/images/bg-bottom-left.png') }}" class="auth-bg bg-left-bottom">
     <img src="{{ asset('assets/images/bg-bottom-right.png') }}" class="auth-bg bg-right-bottom">

    <!-- Content -->
    <main class="auth-wrapper">

        <div class="auth-card">

            <!-- Logo -->
            <img src="{{ asset('assets/images/logo.png') }}"
                class="auth-logo"
                alt="Logo">

            <!-- Title -->
            <h1>Sign Up Akun Anda</h1>

            <!-- Form -->
            <form action="{{ route('register') }}" method="POST" class="auth-form">
                @csrf

                <!-- Username -->
                <input type="text"
                    name="nama_pengguna"
                    placeholder="Username"
                    required>

                <!-- Email -->
                <input type="email"
                    name="email"
                    placeholder="Email"
                    required>

                <!-- Password -->
                <div class="password-wrapper">

                    <input type="password"
                        name="password"
                        placeholder="Password"
                        class="password-input"
                        required>

                    <i class="fa-solid fa-eye toggle-password"></i>

                </div>

                <!-- Konfirmasi Password -->
                <div class="password-wrapper">

                    <input type="password"
                        name="password_confirmation"
                        placeholder="Konfirmasi Password"
                        class="password-input"
                        required>

                    <i class="fa-solid fa-eye toggle-password"></i>

                </div>

                <!-- Button -->
                <button type="submit">
                    Sign Up
                </button>

            </form>

            <!-- Login -->
            <p class="auth-link">
                Sudah punya akun?
                <a href="{{ route('login') }}">Log In</a>
            </p>

        </div>

    </main>

    <!-- Toggle Password -->
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