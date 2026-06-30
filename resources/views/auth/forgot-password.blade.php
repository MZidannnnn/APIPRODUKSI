<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('fe-klien/auth-klien.css') }}">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
            <h1>Lupa Password</h1>

            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('password.email') }}"
                method="POST"
                class="auth-form">

                @csrf

                <!-- Email -->
                <input type="email"
                    name="email"
                    placeholder="Email terdaftar"
                    value="{{ old('email') }}"
                    class="@error('email') input-error @enderror">

                @error('email')
                    <div class="error-message">
                        {{ $message }}
                    </div>
                @enderror

                <!-- Button -->
                <button type="submit">
                    Kirim Link Reset
                </button>

            </form>

            <!-- Login -->
            <p class="auth-link">
                Ingat password?
                <a href="{{ route('login') }}">Login</a>
            </p>

        </div>

    </main>

</body>
</html>