<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun</title>

    <link rel="stylesheet" href="{{ asset('fe-pelanggan/auth.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="auth-container">

    <!-- Background -->
    <img src="{{ asset('assets/images/bg-top-left.png') }}" class="asset-corner top-left">
    <img src="{{ asset('assets/images/bg-top-right.png') }}" class="asset-corner top-right">
    <img src="{{ asset('assets/images/bg-bottom-left.png') }}" class="asset-corner bottom-left">
    <img src="{{ asset('assets/images/bg-bottom-right.png') }}" class="asset-corner bottom-right">

    <!-- Form -->
    <div class="auth-box">
        <img src="{{ asset('assets/images/logo.png') }}" class="logo-main" alt="Logo">

        <h2 class="auth-title">Register Akun</h2>

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <input type="text" name="username" placeholder="Username/Company" class="auth-input" required>

            <input type="email" name="email" placeholder="Email" class="auth-input" required>

            <p class="label-text">Jenis Kelamin</p>

            <div class="gender-container">
                <label class="gender-option">
                    <input type="radio" name="gender" value="L">
                    Laki-Laki
                </label>
                <label class="gender-option">
                    <input type="radio" name="gender" value="P">
                    Perempuan
                </label>
            </div>

            <!-- PASSWORD -->
            <div class="password-wrapper">
                <input 
                    type="password" 
                    name="password" 
                    id="password"
                    placeholder="Password" 
                    class="auth-input" 
                    required
                >
                <i class="toggle-password fa fa-eye" onclick="togglePassword('password', this)"></i>
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="password-wrapper">
                <input 
                    type="password" 
                    name="password_confirmation" 
                    id="confirmPassword"
                    placeholder="Konfirmasi Password" 
                    class="auth-input" 
                    required
                >
                <i class="toggle-password fa fa-eye" onclick="togglePassword('confirmPassword', this)"></i>
            </div>

            <button type="submit" class="btn-primary">Register</button>
        </form>

        <p class="auth-link">
            Sudah punya akun?
            <a href="{{ route('login') }}">Log In</a>
        </p>
    </div>

</div>

<!-- SCRIPT -->
<script>
function togglePassword(fieldId, icon) {
    const field = document.getElementById(fieldId);
    const type = field.type === 'password' ? 'text' : 'password';
    field.type = type;

    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
}
</script>

</body>
</html>