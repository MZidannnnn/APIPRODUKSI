<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>

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
            <h1>Reset Password</h1>

            <!-- Form -->
            <form action="{{ route('password.update') }}"
                method="POST"
                class="auth-form">

                @csrf

                <!-- Token -->
                <input type="hidden"
                    name="token"
                    value="{{ $token }}">

                <!-- Email -->
                <input type="email"
                    name="email"
                    value="{{ $email ?? old('email') }}"
                    placeholder="Email"
                    required>

                <!-- Password -->
                <div class="password-wrapper">

                    <input type="password"
                        name="password"
                        placeholder="Password Baru"
                        class="password-input"
                        required>

                    <i class="fa-solid fa-eye toggle-password"></i>

                </div>

                <!-- Konfirmasi Password -->
                <div class="password-wrapper">

                    <input type="password"
                        name="password_confirmation"
                        placeholder="Ulangi Password"
                        class="password-input"
                        required>

                    <i class="fa-solid fa-eye toggle-password"></i>

                </div>

                <!-- Error Email -->
                @error('email')
                    <div class="auth-error">
                        {{ $message }}
                    </div>
                @enderror

                <!-- Error Password -->
                @error('password')
                    <div class="auth-error">
                        {{ $message }}
                    </div>
                @enderror

                <!-- Button -->
                <button type="submit">
                    Reset Password
                </button>

            </form>

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