<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="{{ asset('fe-pelanggan/auth.css') }}">
</head>
<body>
<div class="auth-container">
    <div class="auth-box">
        <h2 class="auth-title">Reset Password</h2>

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <input type="email" name="email" value="{{ $email }}" placeholder="Email" class="auth-input" required>

            <input type="password" name="password" placeholder="Password baru" class="auth-input" required>
            <input type="password" name="password_confirmation" placeholder="Ulangi password" class="auth-input" required>

            @error('email')
                <div class="auth-error">{{ $message }}</div>
            @enderror
            @error('password')
                <div class="auth-error">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn-primary">Reset Password</button>
        </form>
    </div>
</div>
</body>
</html>