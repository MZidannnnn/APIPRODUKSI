@extends('klien.layouts.app')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
@endpush

@section('content')
<section class="upload-wrap">
    <div class="upload-hero">
        <p class="eyebrow">Pembayaran</p>
        <h1>Unggah Bukti Bayar</h1>
        <p>
            Transaksi #{{ $pembayaran->id_pembayaran }}.
            Unggah file JPG, PNG, atau PDF dengan ukuran maksimal 5 MB.
        </p>
    </div>

    <div class="upload-card">
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert error">
                {{ $errors->first('bukti_bayar') ?: 'Terjadi kesalahan validasi.' }}
            </div>
        @endif

        @if (!empty($pembayaran->bukti_bayar))
            <div class="current-file">
                Bukti saat ini: {{ basename($pembayaran->bukti_bayar) }}
            </div>
        @endif

        <form id="upload-form" method="POST" action="{{ route('pembayaran.upload', $pembayaran->id_pembayaran) }}" enctype="multipart/form-data">
            @csrf

            <label for="bukti_bayar" id="dropzone" class="dropzone" tabindex="0">
                <input id="bukti_bayar" type="file" name="bukti_bayar" accept=".jpg,.jpeg,.png,.pdf" required hidden>
                <div class="drop-main">
                    <div class="icon">+</div>
                    <h2>Tarik file ke sini</h2>
                    <p>atau klik untuk memilih file</p>
                    <small>Format: JPG, PNG, PDF • Maks. 5 MB</small>
                </div>
            </label>

            <div id="preview" class="preview" hidden>
                <img id="img-preview" alt="Pratinjau gambar" hidden>
                <div id="pdf-preview" class="pdf-preview" hidden>
                    <strong>Dokumen PDF dipilih</strong>
                    <span id="pdf-name"></span>
                </div>
                <div class="meta">
                    <span id="file-name"></span>
                    <span id="file-size"></span>
                </div>
            </div>

            <button type="submit" class="btn-submit">Unggah Bukti Bayar</button>
        </form>
    </div>
</section>

<script>
    (function () {
        var input = document.getElementById('bukti_bayar');
        var dropzone = document.getElementById('dropzone');
        var preview = document.getElementById('preview');
        var imgPreview = document.getElementById('img-preview');
        var pdfPreview = document.getElementById('pdf-preview');
        var pdfName = document.getElementById('pdf-name');
        var fileName = document.getElementById('file-name');
        var fileSize = document.getElementById('file-size');

        function humanSize(bytes) {
            if (!bytes && bytes !== 0) return '-';
            var units = ['B', 'KB', 'MB', 'GB'];
            var i = 0;
            var size = bytes;
            while (size >= 1024 && i < units.length - 1) {
                size = size / 1024;
                i++;
            }
            return size.toFixed(2) + ' ' + units[i];
        }

        function showPreview(file) {
            if (!file) return;

            preview.hidden = false;
            fileName.textContent = file.name;
            fileSize.textContent = humanSize(file.size);

            var isImage = file.type === 'image/jpeg' || file.type === 'image/png' || file.type === 'image/jpg';
            var isPdf = file.type === 'application/pdf';

            imgPreview.hidden = true;
            pdfPreview.hidden = true;

            if (isImage) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    imgPreview.src = e.target.result;
                    imgPreview.hidden = false;
                };
                reader.readAsDataURL(file);
            } else if (isPdf) {
                pdfName.textContent = file.name;
                pdfPreview.hidden = false;
            }
        }

        function handleFiles(files) {
            if (!files || !files.length) return;
            var dt = new DataTransfer();
            dt.items.add(files[0]);
            input.files = dt.files;
            showPreview(files[0]);
        }

        dropzone.addEventListener('click', function () {
            input.click();
        });

        dropzone.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                input.click();
            }
        });

        input.addEventListener('change', function () {
            if (input.files && input.files[0]) {
                showPreview(input.files[0]);
            }
        });

        ['dragenter', 'dragover'].forEach(function (eventName) {
            dropzone.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('is-dragover');
            });
        });

        ['dragleave', 'drop'].forEach(function (eventName) {
            dropzone.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('is-dragover');
            });
        });

        dropzone.addEventListener('drop', function (e) {
            handleFiles(e.dataTransfer.files);
        });
    })();
</script>

<style>
    :root {
        --up-bg: #f5f7fb;
        --up-card: #ffffff;
        --up-ink: #1f2937;
        --up-muted: #6b7280;
        --up-primary: #0f766e;
        --up-primary-2: #0b5d57;
        --up-border: #dbe2ea;
        --up-success-bg: #ecfdf3;
        --up-success-border: #86efac;
        --up-success-ink: #166534;
        --up-error-bg: #fef2f2;
        --up-error-border: #fecaca;
        --up-error-ink: #991b1b;
        --up-shadow: 0 20px 45px rgba(15, 23, 42, 0.09);
    }

    .upload-wrap {
        max-width: 920px;
        margin: 0 auto;
        padding: 22px 14px 36px;
        color: var(--up-ink);
        font-family: "Manrope", sans-serif;
    }

    .upload-hero {
        margin-bottom: 16px;
        border: 1px solid var(--up-border);
        border-radius: 18px;
        padding: 18px 20px;
        background:
            radial-gradient(120% 120% at 0% 0%, rgba(15, 118, 110, 0.14), transparent 52%),
            linear-gradient(120deg, #f8fffc 0%, #f7f9ff 100%);
        box-shadow: var(--up-shadow);
    }

    .upload-hero .eyebrow {
        margin: 0;
        text-transform: uppercase;
        letter-spacing: .12em;
        font-size: 11px;
        color: #64748b;
    }

    .upload-hero h1 {
        margin: 8px 0 6px;
        font: 400 34px/1.1 "DM Serif Display", serif;
    }

    .upload-hero p {
        margin: 0;
        color: var(--up-muted);
    }

    .upload-card {
        border: 1px solid var(--up-border);
        border-radius: 18px;
        padding: 18px;
        background: var(--up-card);
        box-shadow: var(--up-shadow);
    }

    .alert {
        margin-bottom: 12px;
        border-radius: 12px;
        padding: 10px 12px;
        font-weight: 600;
        font-size: 14px;
    }

    .alert.success {
        background: var(--up-success-bg);
        border: 1px solid var(--up-success-border);
        color: var(--up-success-ink);
    }

    .alert.error {
        background: var(--up-error-bg);
        border: 1px solid var(--up-error-border);
        color: var(--up-error-ink);
    }

    .current-file {
        margin-bottom: 12px;
        background: #f8fafc;
        border: 1px solid var(--up-border);
        border-radius: 10px;
        padding: 8px 10px;
        font-size: 13px;
        color: #334155;
    }

    .dropzone {
        display: block;
        border: 2px dashed #9fb4c7;
        border-radius: 16px;
        padding: 34px 16px;
        text-align: center;
        background: linear-gradient(180deg, #fcfdff 0%, #f6faff 100%);
        transition: .2s ease;
        cursor: pointer;
        outline: none;
    }

    .dropzone:hover,
    .dropzone.is-dragover {
        border-color: var(--up-primary);
        transform: translateY(-1px);
        box-shadow: 0 12px 28px rgba(15, 118, 110, 0.12);
    }

    .drop-main .icon {
        width: 44px;
        height: 44px;
        border-radius: 999px;
        margin: 0 auto 10px;
        background: #e6fffa;
        color: var(--up-primary);
        font-size: 26px;
        font-weight: 700;
        display: grid;
        place-items: center;
    }

    .drop-main h2 {
        margin: 0;
        font-size: 20px;
    }

    .drop-main p {
        margin: 4px 0 0;
        color: var(--up-muted);
    }

    .drop-main small {
        display: inline-block;
        margin-top: 8px;
        color: #64748b;
    }

    .preview {
        margin-top: 14px;
        border: 1px solid var(--up-border);
        border-radius: 14px;
        padding: 12px;
        background: #fafcff;
    }

    .preview img {
        width: 100%;
        max-height: 300px;
        object-fit: contain;
        border-radius: 12px;
        border: 1px solid var(--up-border);
        background: #fff;
    }

    .pdf-preview {
        border-radius: 10px;
        padding: 14px;
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        color: #1e3a8a;
        display: grid;
        gap: 4px;
    }

    .preview .meta {
        margin-top: 10px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        color: #475569;
        font-size: 13px;
    }

    .btn-submit {
        margin-top: 14px;
        border: 0;
        background: var(--up-primary);
        color: #fff;
        font-weight: 700;
        border-radius: 12px;
        padding: 12px 16px;
        width: 100%;
        cursor: pointer;
        transition: .2s ease;
    }

    .btn-submit:hover {
        background: var(--up-primary-2);
        transform: translateY(-1px);
    }

    @media (max-width: 640px) {
        .upload-hero h1 {
            font-size: 28px;
        }

        .upload-card {
            padding: 14px;
        }

        .dropzone {
            padding: 28px 12px;
        }
    }
</style>
@endsection