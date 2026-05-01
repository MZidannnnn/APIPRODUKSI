<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Advisel Pramana</title>
    <link rel="stylesheet" href="{{ asset('sbadmin2/css/loginadmin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="login-card">
        <img src="{{ asset('assets/images/logo.png') }}" class="logo-admin" alt="Logo">
        
        <h1 class="title">Advisel Pramana Company System</h1>
        <p class="subtitle">Login</p>

        <form action="{{ route('login-admin') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <input type="email" name="email" class="admin-input" placeholder="Email" required autofocus>
            </div>

            <div class="form-group">
                <input type="password" name="password" id="passwordField" class="admin-input" placeholder="Password" required>
                <i class="fa-regular fa-eye toggle-password" id="eyeIcon"></i>
            </div>

            <button type="submit" class="btn-admin-login">Login</button>
        </form>
    </div>

    <script>
        const passwordField = document.querySelector('#passwordField');
        const eyeIcon = document.querySelector('#eyeIcon');

        eyeIcon.addEventListener('click', function() {
            // Toggle tipe input
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            // Toggle ikon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>

</body>
</html>