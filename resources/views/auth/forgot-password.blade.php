<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>

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
            <h1>Lupa Password</h1>

            <!-- Success Message -->
            @if (session('status'))
                <div class="auth-success">
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
                    required>

                <!-- Error -->
                @error('email')
                    <div class="auth-error">
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