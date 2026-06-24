<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('fe-klien/profile.css') }}">
</head>
<body>

<img src="{{ asset('assets/images/bg-top-left.png') }}" class="auth-bg bg-left-top">
<img src="{{ asset('assets/images/bg-top-right.png') }}" class="auth-bg bg-right-top">
<img src="{{ asset('assets/images/bg-bottom-left.png') }}" class="auth-bg bg-left-bottom">
<img src="{{ asset('assets/images/bg-bottom-right.png') }}" class="auth-bg bg-right-bottom">


<div class="profile-container">
    
    <a href="{{ route('dashboard') }}" class="btn-kembali">
        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
    </a>

    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card h-100 text-center py-5">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    
                    <i class="fas fa-user-circle profile-avatar-icon"></i>
                    
                    <h4 class="font-weight-bold text-dark mt-2 mb-1">
                        {{ Auth::user()->nama_pengguna ?? 'Nama Klien' }}
                    </h4>
                    <p class="text-muted mb-3">
                        <i class="fas fa-envelope mr-1"></i> {{ Auth::user()->email ?? 'email@klien.com' }}
                    </p>
                    
                    <!-- <span class="badge badge-success px-3 py-2" style="background-color: #e8f5e9; color: #008a46; border: 1px solid #008a46;">
                        Pelanggan Aktif
                    </span> -->
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-4">
            <div class="card h-100">
                <div class="card-header-custom">
                    <i class="fas fa-user-cog mr-2" style="color: #008a46;"></i> Pengaturan Akun
                </div>
                <div class="card-body p-4">
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('klien.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h6 class="text-muted font-weight-bold mb-3">Informasi Dasar</h6>
                        
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_pengguna" class="form-control" value="{{ Auth::user()->nama_pengguna ?? '' }}" required>
                        </div>

                        <hr class="mb-4">
                        <h6 class="text-muted font-weight-bold mb-3">Keamanan (Ubah Password)</h6>

                        <div class="form-group">
                            <label>Password Lama <small class="text-muted">(Wajib jika ingin mengubah password)</small></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                </div>
                                <input type="password" id="password_lama" name="password_lama"
                                    class="form-control border-left-0 border-right-0"
                                    placeholder="Masukkan password lama">

                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password" toggle="#password_lama">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>

                            @error('password_lama')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Password Baru <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fas fa-lock"></i></span>
                                </div>
                                <input type="password" id="password" name="password" class="form-control border-left-0 border-right-0" placeholder="Masukkan password baru">
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password" toggle="#password">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="text-right mt-5">
                            <button type="submit" class="btn btn-primary-custom px-5">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.querySelectorAll('.toggle-password').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.querySelector(this.getAttribute('toggle'));
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                icon.style.color = '#008a46';
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                icon.style.color = '#6c757d';
            }
        });
    });
</script>

</body>
</html>