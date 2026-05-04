<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <link rel="stylesheet" href="{{ asset('fe-pelanggan/auth.css') }}">
</head>
<body>
<div class="auth-container">
    <div class="auth-box">
        <h2 class="auth-title">Lupa Password</h2>

        @if (session('status'))
            <div class="auth-success">{{ session('status') }}</div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <input type="email" name="email" placeholder="Email terdaftar" class="auth-input" required>
            @error('email')
                <div class="auth-error">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn-primary">Kirim Link Reset</button>
        </form>

        <p class="auth-link">
            Ingat password?
            <a href="{{ route('login') }}">Login</a>
        </p>
    </div>
</div>
</body>
</html>