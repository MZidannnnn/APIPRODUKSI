<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Akun Anda</title>

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

        <h2 class="auth-title">Log in Ke Akun Anda</h2>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <input 
                type="text" 
                name="nama_pengguna" 
                placeholder="Username" 
                class="auth-input" 
                required
            >

            <!-- PASSWORD -->
            <div class="password-wrapper">
                <input 
                    type="password" 
                    name="password" 
                    id="loginPassword"
                    placeholder="Password" 
                    class="auth-input" 
                    required
                >
                <i class="toggle-password fa fa-eye" onclick="togglePassword('loginPassword', this)"></i>

                 <a href="{{ route('password.request') }}" class="forgot-password">Lupa Password?</a>
            </div>

           

            <button type="submit" class="btn-primary">Log In</button>
        </form>

        <p class="auth-link">
            Belum punya akun?
            <a href="{{ route('register') }}">Register</a>
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