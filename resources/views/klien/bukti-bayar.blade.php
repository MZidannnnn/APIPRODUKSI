@extends('klien.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('fe-klien/bukti-bayar.css') }}?v={{ time() }}">
@endpush

@section('content')
<section class="upload-wrap">

    <a href="{{ route('klien.pesanan.riwayat') }}" class="btn-kembali">
        <i class="fas fa-chevron-left"></i>
        Kembali
    </a>

    <div class="upload-hero">
        <p class="eyebrow">Pembayaran</p>
        <h1>Unggah Bukti Bayar</h1>

        <p>
            Transaksi #{{ $pembayaran->id_pembayaran }}
            @if ($pembayaran->pesanan)
                untuk Pesanan #{{ $pembayaran->pesanan->id_pesanan }}
            @endif
        </p>

        <small>Format file: JPG, JPEG, PNG, atau PDF. Maksimal 5 MB.</small>
    </div>

    <div class="upload-card">

        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert error">{{ $errors->first() }}</div>
        @endif

        <div class="payment-info">
            <div>
                <span>Status Pembayaran</span>
                <strong>{{ $pembayaran->status_bayar ?? 'Pending' }}</strong>
            </div>

            <div>
                <span>Tipe Pembayaran</span>
                <strong>{{ $pembayaran->tipe_pembayaran ?? '-' }}</strong>
            </div>

            <div>
                <span>Jumlah Bayar</span>
                <strong>Rp {{ number_format($pembayaran->jumlah_bayar ?? 0, 0, ',', '.') }}</strong>
            </div>
        </div>

        @if (!empty($pembayaran->bukti_bayar))
            <div class="current-file">
                <strong>Bukti bayar sudah diunggah:</strong>
                <br>
                {{ basename($pembayaran->bukti_bayar) }}
                <br>
                <a href="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" target="_blank">
                    Lihat Bukti
                </a>
            </div>
        @endif

        <form
            id="uploadForm"
            method="POST"
            action="{{ route('pembayaran.upload', $pembayaran->id_pembayaran) }}"
            enctype="multipart/form-data">
            @csrf

            <label for="bukti_bayar" id="dropzone" class="dropzone" tabindex="0">
                <input
                    id="bukti_bayar"
                    type="file"
                    name="bukti_bayar"
                    accept=".jpg,.jpeg,.png,.pdf"
                    required
                    hidden>

                <div class="drop-main">
                    <div class="icon">
                        <i class="fas fa-upload"></i>
                    </div>

                    <h2>Pilih Bukti Pembayaran</h2>
                    <p>Klik area ini atau tarik file ke sini</p>
                    <small>JPG, JPEG, PNG, PDF • Maksimal 5 MB</small>
                </div>
            </label>

            <div id="preview" class="preview" hidden>
                <img id="imgPreview" alt="Preview bukti pembayaran" hidden>

                <div id="pdfPreview" class="pdf-preview" hidden>
                    <strong>File PDF dipilih</strong>
                    <span id="pdfName"></span>
                </div>

                <div class="meta">
                    <span id="fileName"></span>
                    <span id="fileSize"></span>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                Unggah Bukti Bayar
            </button>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('bukti_bayar');
    const dropzone = document.getElementById('dropzone');
    const preview = document.getElementById('preview');
    const imgPreview = document.getElementById('imgPreview');
    const pdfPreview = document.getElementById('pdfPreview');
    const pdfName = document.getElementById('pdfName');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');

    const maxFileSize = 5 * 1024 * 1024;

    const allowedTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'application/pdf'
    ];

    function formatFileSize(bytes) {
        const units = ['B', 'KB', 'MB', 'GB'];
        let size = bytes;
        let unitIndex = 0;

        while (size >= 1024 && unitIndex < units.length - 1) {
            size = size / 1024;
            unitIndex++;
        }

        return size.toFixed(2) + ' ' + units[unitIndex];
    }

    function resetPreview() {
        preview.hidden = true;
        imgPreview.hidden = true;
        pdfPreview.hidden = true;
        imgPreview.removeAttribute('src');
        fileName.textContent = '';
        fileSize.textContent = '';
        pdfName.textContent = '';
    }

    function showPreview(file) {
        resetPreview();

        if (!file) return;

        if (!allowedTypes.includes(file.type)) {
            alert('Format file tidak valid. Gunakan JPG, JPEG, PNG, atau PDF.');
            input.value = '';
            return;
        }

        if (file.size > maxFileSize) {
            alert('Ukuran file maksimal 5 MB.');
            input.value = '';
            return;
        }

        preview.hidden = false;
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);

        if (file.type === 'application/pdf') {
            pdfName.textContent = file.name;
            pdfPreview.hidden = false;
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            imgPreview.src = event.target.result;
            imgPreview.hidden = false;
        };

        reader.readAsDataURL(file);
    }

    input.addEventListener('change', function () {
        showPreview(input.files[0]);
    });

    dropzone.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            input.click();
        }
    });

    ['dragenter', 'dragover'].forEach(function (eventName) {
        dropzone.addEventListener(eventName, function (event) {
            event.preventDefault();
            dropzone.classList.add('is-dragover');
        });
    });

    ['dragleave', 'drop'].forEach(function (eventName) {
        dropzone.addEventListener(eventName, function (event) {
            event.preventDefault();
            dropzone.classList.remove('is-dragover');
        });
    });

    dropzone.addEventListener('drop', function (event) {
        const file = event.dataTransfer.files[0];

        if (!file) return;

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        input.files = dataTransfer.files;

        showPreview(file);
    });
});
</script>
@endsection