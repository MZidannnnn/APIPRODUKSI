@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-plus mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a href="{{ route('admin.itemProduksi.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.itemProduksi.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off" novalidate>
                @csrf

                <h5 class="font-weight-bold text-primary mb-3">Informasi Produk / Jasa</h5>

                <div class="row mb-3">
                    <div class="col-xl-4 mb-2">
                        <label><span class="text-danger">*</span> Kategori Usaha :</label>
                        <select name="id_kategori" class="form-control @error('id_kategori') is-invalid @enderror">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($kategoriUsaha as $kat)
                                <option value="{{ $kat->id_kategori }}" {{ old('id_kategori') == $kat->id_kategori ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kategori')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-xl-4 mb-2">
                        <label><span class="text-danger">*</span> Satuan Produk :</label>
                        <select name="id_satuan" class="form-control @error('id_satuan') is-invalid @enderror">
                            <option value="">-- Pilih Satuan --</option>
                            @foreach ($satuanHarga as $satuan)
                                <option value="{{ $satuan->id_satuan }}" {{ old('id_satuan') == $satuan->id_satuan ? 'selected' : '' }}>
                                    {{ $satuan->nama_satuan }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_satuan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-xl-4 mb-2">
                        <label><span class="text-danger">*</span> Nama Item :</label>
                        <input type="text" name="nama_item" class="form-control @error('nama_item') is-invalid @enderror" value="{{ old('nama_item') }}">
                        @error('nama_item')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-xl-8 mb-2">
                        <label>Deskripsi Item :</label>
                        <textarea name="deskripsi_item"
                            rows="3"
                            class="form-control @error('deskripsi_item') is-invalid @enderror">{{ old('deskripsi_item') }}</textarea>

                        @error('deskripsi_item')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-xl-4 mb-2">
                        <label><span class="text-danger">*</span> Status Produk :</label>

                        <select name="status_aktif"
                            class="form-control @error('status_aktif') is-invalid @enderror">

                            <option value="Aktif"
                                {{ old('status_aktif', 'Aktif') == 'Aktif' ? 'selected' : '' }}>
                                Aktif
                            </option>

                            <option value="Non-aktif"
                                {{ old('status_aktif') == 'Non-aktif' ? 'selected' : '' }}>
                                Non-aktif
                            </option>
                        </select>

                        @error('status_aktif')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="font-weight-bold text-primary m-0">Varian Ukuran & Harga Dasar</h5>
                    <button type="button" class="btn btn-success btn-sm" id="btn-tambah-varian">
                        <i class="fas fa-plus mr-1"></i> Tambah Varian
                    </button>
                </div>

                @error('harga_dasar')
                    <small class="text-danger d-block mb-2">{{ $message }}</small>
                @enderror

                <div id="container-varian">
                @php
                    $oldUkuran = old('ukuran', ['']);
                    $oldHarga  = old('harga_dasar', ['']);
                @endphp

                @foreach ($oldUkuran as $index => $ukuran)
                    <div class="row mb-2 varian-item align-items-end">
                        <div class="col-xl-6 mb-2">
                            <label>Ukuran :</label>
                            <input type="text"
                                name="ukuran[]"
                                class="form-control @error('ukuran.' . $index) is-invalid @enderror"
                                placeholder="Contoh: A4, 2x3 meter, L"
                                value="{{ $ukuran }}">

                            @error('ukuran.' . $index)
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-xl-5 mb-2">
                            <label><span class="text-danger">*</span> Harga Dasar :</label>
                            <input type="text"
                                name="harga_dasar[]"
                                class="form-control harga @error('harga_dasar.' . $index) is-invalid @enderror"
                                placeholder="Format angka saja"
                                value="{{ $oldHarga[$index] ?? '' }}">

                            @error('harga_dasar.' . $index)
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-xl-1 mb-2">
                            <button type="button"
                                class="btn btn-danger btn-block btn-hapus-varian"
                                {{ $index == 0 ? 'disabled' : '' }}>
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

                <hr class="mt-4">

                <!-- Pengaturan Biaya Dinamis -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="font-weight-bold text-primary m-0">Pengaturan Biaya Tambahan & Pengiriman</h5>
                </div>
                
                <div class="row mb-3 bg-light p-3 border rounded mx-1">
                    <div class="col-md-6 border-right">
                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" class="custom-control-input" id="is_biaya_jarak_aktif" name="is_biaya_jarak_aktif" value="1" {{ old('is_biaya_jarak_aktif') ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold" for="is_biaya_jarak_aktif">Aktifkan Biaya Transport/Jarak</label>
                        </div>
                        <div id="form-biaya-jarak" class="pl-4 {{ old('is_biaya_jarak_aktif') ? '' : 'd-none' }}">
                            <div class="form-group mb-2">
                                <label>Tarif per Kilometer (Rp) :</label>
                                <input type="text" name="tarif_per_km" class="form-control harga" placeholder="Contoh: 10.000" value="{{ old('tarif_per_km') }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" class="custom-control-input" id="is_biaya_waktu_aktif" name="is_biaya_waktu_aktif" value="1" {{ old('is_biaya_waktu_aktif') ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold" for="is_biaya_waktu_aktif">Aktifkan Biaya Waktu/Urgensi</label>
                        </div>
                        <div id="form-biaya-waktu" class="pl-4 {{ old('is_biaya_waktu_aktif') ? '' : 'd-none' }}">
                            <div class="form-group mb-2">
                                <label>Batas Hari Zona Merah (Blokir Pesanan) :</label>
                                <input type="number" name="batas_hari_zona_merah" class="form-control" placeholder="Contoh: 1 (Untuk H-1)" value="{{ old('batas_hari_zona_merah') }}">
                            </div>
                            <div class="form-group mb-2">
                                <label>Batas Hari Zona Kuning (Dikenakan Urgensi) :</label>
                                <input type="number" name="batas_hari_zona_kuning" class="form-control" placeholder="Contoh: 3 (Untuk H-3)" value="{{ old('batas_hari_zona_kuning') }}">
                            </div>
                            <div class="form-group mb-2">
                                <label>Tarif Tambahan Urgensi (Rp) :</label>
                                <input type="text" name="biaya_urgensi" class="form-control harga" placeholder="Contoh: 50.000" value="{{ old('biaya_urgensi') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="mt-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="font-weight-bold text-primary m-0">Foto Produk</h5>
                    <button type="button" class="btn btn-success btn-sm" id="btn-tambah-foto">
                        <i class="fas fa-plus mr-1"></i> Tambah Foto
                    </button>
                </div>

                @error('foto_produk')
                    <small class="text-danger d-block mb-2">{{ $message }}</small>
                @enderror

               <div id="container-foto" class="row">
                    <div class="col-xl-4 mb-3 foto-item">
                        <div class="card bg-light p-2">
                            <label><span class="text-danger">*</span> File Gambar :</label>

                            <input type="file"
                                name="foto_produk[]"
                                class="form-control-file mb-1 input-foto @error('foto_produk.0') is-invalid @enderror"
                                accept=".jpg,.jpeg,.png,.webp,image/*">

                            <small class="text-muted d-block mb-2">
                                Format: JPG, JPEG, PNG, WEBP. Maksimal 2 MB.
                            </small>

                            <img src=""
                                class="img-preview mt-2 d-none"
                                style="width: 100%; height: 160px; object-fit: cover; border-radius: 5px;">

                            @error('foto_produk.0')
                                <small class="text-danger d-block mt-2">{{ $message }}</small>
                            @enderror

                            <button type="button" class="btn btn-danger btn-sm btn-block btn-hapus-foto mt-2" disabled>
                                <i class="fas fa-trash mr-1"></i> Hapus Foto
                            </button>
                        </div>
                    </div>
                </div>

                <hr class="mt-4">

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i> Simpan Semua Data
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const containerVarian = document.getElementById('container-varian');
            const btnTambahVarian = document.getElementById('btn-tambah-varian');

            btnTambahVarian.addEventListener('click', function () {
                const barisVarian = document.querySelector('.varian-item').cloneNode(true);

                barisVarian.querySelectorAll('input').forEach(input => {
                    input.value = '';
                    input.classList.remove('is-invalid');
                });

                barisVarian.querySelectorAll('.text-danger').forEach(errorText => {
                    errorText.remove();
                });

                const btnHapus = barisVarian.querySelector('.btn-hapus-varian');
                btnHapus.removeAttribute('disabled');
                btnHapus.addEventListener('click', function () {
                    barisVarian.remove();
                });

                containerVarian.appendChild(barisVarian);
            });

            const containerFoto = document.getElementById('container-foto');
            const btnTambahFoto = document.getElementById('btn-tambah-foto');

            btnTambahFoto.addEventListener('click', function () {
                const kotakFoto = document.querySelector('.foto-item').cloneNode(true);

                kotakFoto.querySelector('input[type="file"]').value = '';

                const preview = kotakFoto.querySelector('.img-preview');
                preview.src = '';
                preview.classList.add('d-none');

                const btnHapusFoto = kotakFoto.querySelector('.btn-hapus-foto');
                btnHapusFoto.removeAttribute('disabled');
                btnHapusFoto.addEventListener('click', function () {
                    kotakFoto.remove();
                });

                containerFoto.appendChild(kotakFoto);
            });

            document.addEventListener('input', function (e) {
                if (e.target.classList.contains('harga')) {
                    // hanya angka
                    let angka = e.target.value.replace(/[^0-9]/g, '');

                    // format ribuan
                    if (angka === '') {
                        e.target.value = '';
                    } else {
                        e.target.value = new Intl.NumberFormat('id-ID').format(angka);
                    }
                }
            });

            document.addEventListener('change', function (e) {
                if (e.target.classList.contains('input-foto')) {
                    const file = e.target.files[0];
                    const preview = e.target.closest('.foto-item').querySelector('.img-preview');

                    if (file) {
                        const reader = new FileReader();

                        reader.onload = function (event) {
                            preview.src = event.target.result;
                            preview.classList.remove('d-none');
                        }

                        reader.readAsDataURL(file);
                    } else {
                        preview.src = '';
                        preview.classList.add('d-none');
                    }
                }
            });

            // Toggle logika untuk Biaya Tambahan
            const toggleJarak = document.getElementById('is_biaya_jarak_aktif');
            const formJarak = document.getElementById('form-biaya-jarak');
            if (toggleJarak && formJarak) {
                // Cek state awal
                if (toggleJarak.checked) {
                    formJarak.classList.remove('d-none');
                } else {
                    formJarak.classList.add('d-none');
                }
                
                toggleJarak.addEventListener('change', function() {
                    if (this.checked) {
                        formJarak.classList.remove('d-none');
                    } else {
                        formJarak.classList.add('d-none');
                    }
                });
            }

            const toggleWaktu = document.getElementById('is_biaya_waktu_aktif');
            const formWaktu = document.getElementById('form-biaya-waktu');
            if (toggleWaktu && formWaktu) {
                // Cek state awal
                if (toggleWaktu.checked) {
                    formWaktu.classList.remove('d-none');
                } else {
                    formWaktu.classList.add('d-none');
                }
                
                toggleWaktu.addEventListener('change', function() {
                    if (this.checked) {
                        formWaktu.classList.remove('d-none');
                    } else {
                        formWaktu.classList.add('d-none');
                    }
                });
            }
        });
    </script>
@endsection