@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Profil Admin</h1>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between border-bottom-primary">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Akun</h6>
                </div>
                <div class="card-body text-center mt-3">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-gray-200 rounded-circle border" style="width: 100px; height: 100px;">
                            <i class="fas fa-user-tie fa-3x text-gray-400"></i>
                        </div>
                    </div>
                    
                    <h5 class="font-weight-bold text-dark mb-1">{{ $user->nama_pengguna }}</h5>
                    <p class="text-muted mb-4">{{ $user->kategori->nama_kategori ?? 'Semua Kategori' }}</p>
                    
                    <ul class="list-group list-group-flush text-left">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-envelope text-gray-500 mr-2"></i> Email</span>
                            <span class="font-weight-600 text-dark">{{ $user->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-circle text-success mr-2" style="font-size: 10px;"></i> Status</span>
                            <span class="badge badge-success px-2 py-1">Aktif</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 border-bottom-primary">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-key mr-2"></i>Ubah Password
                    </h6>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group row mb-4">
                            <label class="col-sm-4 col-form-label font-weight-bold text-gray-700">Password Lama</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-unlock text-gray-500"></i></span>
                                    </div>
                                    <input type="password" 
                                           id="password_lama"
                                           name="password_lama" 
                                           class="form-control border-left-0 border-right-0 @error('password_lama') is-invalid @enderror"
                                           placeholder="Masukkan password lama">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-light text-gray-600 border-left-0 toggle-password" type="button" data-target="password_lama">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('password_lama')
                                        <div class="invalid-feedback text-right">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="mb-4">

                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold text-gray-700">Password Baru</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-lock text-gray-500"></i></span>
                                    </div>
                                    <input type="password" 
                                           id="password_baru"
                                           name="password" 
                                           class="form-control border-left-0 border-right-0 @error('password') is-invalid @enderror"
                                           placeholder="Buat password baru">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-light text-gray-600 border-left-0 toggle-password" type="button" data-target="password_baru">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback text-right">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-sm-4 col-form-label font-weight-bold text-gray-700">Ulangi Password</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-check-circle text-gray-500"></i></span>
                                    </div>
                                    <input type="password" 
                                           id="confirm_password"
                                           name="password_confirmation" 
                                           class="form-control border-left-0 border-right-0"
                                           placeholder="Ketik ulang password baru">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-light text-gray-600 border-left-0 toggle-password" type="button" data-target="confirm_password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mt-5 mb-0">
                            <div class="col-sm-12 text-right">
                                <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">
                                    <i class="fas fa-save mr-2"></i> Update Password
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Fitur Show/Hide Password Interaktif
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButtons = document.querySelectorAll('.toggle-password');

        toggleButtons.forEach(button => {
            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (passwordInput.type === 'password') {
                    // Ubah tipe ke 'text' (tampilkan) dan ubah icon mata silang
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                    
                    // Tambahkan border-color oranye accent kodemu agar terlihat aktif
                    this.classList.add('text-primary');
                    this.style.borderColor = '#ef6c00'; // Sesuaikan accent hex
                } else {
                    // Ubah kembali tipe ke 'password' (sembunyikan) dan ubah icon mata
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    
                    // Reset styling tombol
                    this.classList.remove('text-primary');
                    this.style.borderColor = '#e3e6f0'; // Reset ke border default admin
                }
            });
        });
    });
</script>
@endsection
