@extends('klien.layouts.app')
@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
@endpush

@section('content')
    <div class="detail-page">
        <header class="detail-hero">
            <a class="back-link" href="{{ route('pesanan.listItem') }}">&larr; Kembali</a>
            <h2 class="item-title">{{ $itemProduksi->nama_item }}</h2>
            <div class="item-meta">
                <span class="meta-pill">Kategori: {{ $itemProduksi->kategoriUsaha->nama_kategori ?? '-' }}</span>
                <span class="meta-pill">Status: {{ $itemProduksi->status_aktif }}</span>
            </div>
        </header>

        <div class="detail-grid">
            <section class="detail-card">
                <div class="gallery">
                    @if ($itemProduksi->fotoProduk->isNotEmpty())
                        <div class="swiper main-swiper">
                            <div class="swiper-wrapper">
                                @foreach ($itemProduksi->fotoProduk as $foto)
                                    <div class="swiper-slide">
                                        <img src="{{ asset($foto->nama_foto) }}" alt="Foto {{ $loop->iteration }}"
                                            loading="lazy">
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-pagination"></div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>
                        </div>
                        <div class="swiper thumbs-swiper" aria-label="Thumbnail produk">
                            <div class="swiper-wrapper">
                                @foreach ($itemProduksi->fotoProduk as $foto)
                                    <button class="swiper-slide thumb" type="button">
                                        <img src="{{ asset($foto->nama_foto) }}" alt="Thumb {{ $loop->iteration }}"
                                            loading="lazy">
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="gallery-empty">Tidak ada gambar produk</div>
                    @endif
                </div>

                <p class="item-desc">{{ $itemProduksi->deskripsi_item }}</p>

                @php
                ($detailDefault = $itemProduksi->detailProduk->first())
                @endphp
                @if ($detailDefault)
                    <div class="item-detail">
                        <div>
                            <span class="label">Satuan</span>
                            <span class="value">{{ $itemProduksi->satuanHarga->nama_satuan ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="label">Ukuran</span>
                            <span class="value">{{ $detailDefault->ukuran ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="label">Harga Dasar</span>
                            <span
                                class="value price">Rp{{ number_format($detailDefault->harga_dasar, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endif

                @auth
                    <form method="POST" action="{{ route('chat.start', $itemProduksi->id_item_produksi) }}">
                        @csrf
                        <button class="btn btn-outline chat-cta" type="submit">Chat Admin</button>
                    </form>
                @endauth
            </section>

            <section class="purchase-card">
                <h3 class="section-title">Beli Sekarang</h3>

                <form id="form-pesanan" method="post" action="{{ route('pesanan.beli') }}" enctype="multipart/form-data">
                    @csrf
                    @php
                        $bolehDp =
                            strtolower($itemProduksi->kategoriUsaha->jenisPembayaran->nama_jenis_pembayaran ?? '') ===
                            'dp';
                    @endphp

                    @if ($bolehDp)
                        <div class="field radio-group">
                            <label class="radio">
                                <input type="radio" name="tipe_pembayaran" value="DP" checked>
                                <span>Bayar DP 50%</span>
                            </label>
                            <label class="radio">
                                <input type="radio" name="tipe_pembayaran" value="Full">
                                <span>Bayar Full 100%</span>
                            </label>
                        </div>
                    @else
                        <input type="hidden" name="tipe_pembayaran" value="Full">
                    @endif

                    <div class="field">
                        <label>Pilihan Ukuran</label>
                        <select name="id_detail_produk" required>
                            @foreach ($itemProduksi->detailProduk as $detail)
                                <option value="{{ $detail->id_detail_produk }}">
                                    {{ $detail->ukuran ?? 'Standar' }} -
                                    Rp{{ number_format($detail->harga_dasar, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @php
                        $sablon = strtolower($itemProduksi->kategoriUsaha->nama_kategori ?? '') === 'sablon';
                    @endphp

                    @if ($sablon)
                        <div class="field">
                            <label>Kuantitas</label>
                            <input type="number" name="kuantitas" min="1" value="1" required>
                        </div>
                        <div class="field">
                            <label>File desain (opsional)</label>
                            <input type="file" name="file_desain">
                        </div>
                    @endif

                    <div class="field">
                        <label>Nama Penerima</label>
                        <input type="text" name="nama_penerima" required>
                    </div>

                    <div class="field">
                        <label>Alamat Penerima</label>
                        <textarea name="alamat_penerima" required></textarea>
                    </div>

                    <div class="field">
                        <label>No HP Penerima</label>
                        <input type="text" name="No_hp_penerima" required>
                    </div>

                    <div id="payment-error" class="form-error" role="alert" aria-live="polite"></div>

                    <button class="btn btn-primary" type="submit" data-submit>
                        <span class="btn-text">Beli Sekarang</span>
                        <span class="btn-spinner" aria-hidden="true"></span>
                    </button>
                </form>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        const mainEl = document.querySelector('.main-swiper');
        const thumbsEl = document.querySelector('.thumbs-swiper');

        if (mainEl && thumbsEl) {
            const thumbs = new Swiper(thumbsEl, {
                slidesPerView: 5,
                spaceBetween: 10,
                watchSlidesProgress: true,
                breakpoints: {
                    0: {
                        slidesPerView: 4
                    },
                    640: {
                        slidesPerView: 5
                    },
                    960: {
                        slidesPerView: 6
                    }
                }
            });

            new Swiper(mainEl, {
                spaceBetween: 12,
                loop: false,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev'
                },
                thumbs: {
                    swiper: thumbs
                }
            });
        }

        const form = document.getElementById('form-pesanan');
        const submitBtn = form.querySelector('[data-submit]');
        const errorBox = document.getElementById('payment-error');

        const setLoading = (isLoading) => {
            form.classList.toggle('is-loading', isLoading);
            submitBtn.disabled = isLoading;
            submitBtn.setAttribute('aria-busy', String(isLoading));
        };

        const showError = (message) => {
            errorBox.textContent = message;
        };

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            showError('');
            setLoading(true);

            try {
                const formData = new FormData(form);

                const resPesanan = await fetch("{{ route('pesanan.beli') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Accept": "application/json"
                    },
                    body: formData
                });

                const pesanan = await resPesanan.json().catch(() => ({}));
                if (!resPesanan.ok || !pesanan.id_pesanan) {
                    throw new Error(pesanan.message || "Gagal membuat pesanan. Coba lagi.");
                }

                const tipePembayaran = form.querySelector('input[name="tipe_pembayaran"]:checked') ?
                    form.querySelector('input[name="tipe_pembayaran"]:checked').value :
                    form.querySelector('input[name="tipe_pembayaran"]').value;

                const resBayar = await fetch("{{ route('pembayaran.midtrans') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        id_pesanan: pesanan.id_pesanan,
                        tipe_pembayaran: tipePembayaran
                    })
                });

                const bayar = await resBayar.json().catch(() => ({}));
                if (!resBayar.ok || !bayar.snap_token || !bayar.id_pembayaran) {
                    throw new Error(bayar.message || "Gagal membuat transaksi. Coba lagi.");
                }

                window.snap.pay(bayar.snap_token, {
                    onSuccess: function() {
                        window.location.href = `/pembayaran/${bayar.id_pembayaran}/upload-bukti`;
                    },
                    onPending: function() {
                        window.location.href = `/pembayaran/${bayar.id_pembayaran}/upload-bukti`;
                    },
                    onError: function() {
                        showError("Pembayaran gagal. Silakan ulangi.");
                    }
                });
            } catch (err) {
                showError(err.message || "Terjadi kesalahan jaringan.");
            } finally {
                setLoading(false);
            }
        });
    </script>

    <style>
        :root {
            --bg: #f7f5f2;
            --card: #ffffff;
            --ink: #1f2933;
            --muted: #5b6570;
            --primary: #0f766e;
            --primary-dark: #0a5a55;
            --accent: #f59e0b;
            --border: #e6e3de;
            --shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        .detail-page {
            max-width: 1100px;
            margin: 0 auto;
            padding: 28px 16px 40px;
            font-family: "Instrument Sans", "Segoe UI", Tahoma, sans-serif;
            color: var(--ink);
        }

        .detail-hero {
            margin-bottom: 18px;
        }

        .back-link {
            color: var(--muted);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .item-title {
            font-family: "Space Grotesk", "Segoe UI", Tahoma, sans-serif;
            font-size: 28px;
            margin: 10px 0 6px;
            letter-spacing: 0.2px;
        }

        .item-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .meta-pill {
            background: #f1f4f2;
            border: 1px solid var(--border);
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            color: var(--muted);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(0, 0.85fr);
            gap: 22px;
        }

        .detail-card,
        .purchase-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--shadow);
        }

        .gallery {
            margin-bottom: 16px;
        }

        .main-swiper {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .main-swiper img {
            display: block;
            width: 100%;
            height: 380px;
            object-fit: cover;
        }

        .thumbs-swiper {
            margin-top: 10px;
        }

        .thumbs-swiper .thumb {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            padding: 0;
            background: #fff;
        }

        .thumbs-swiper img {
            width: 100%;
            height: 70px;
            object-fit: cover;
            display: block;
        }

        .gallery-empty {
            border: 1px dashed var(--border);
            border-radius: 14px;
            padding: 24px;
            text-align: center;
            color: var(--muted);
            background: #faf9f7;
        }

        .item-desc {
            color: var(--muted);
            line-height: 1.7;
            margin-bottom: 16px;
        }

        .item-detail {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin-bottom: 14px;
        }

        .item-detail .label {
            display: block;
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .item-detail .value {
            font-weight: 600;
        }

        .item-detail .price {
            color: var(--primary);
        }

        .section-title {
            font-family: "Space Grotesk", "Segoe UI", Tahoma, sans-serif;
            font-size: 20px;
            margin-bottom: 14px;
        }

        .field {
            display: grid;
            gap: 6px;
            margin-bottom: 12px;
        }

        .field label {
            font-size: 13px;
            color: var(--muted);
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        select,
        textarea {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.15);
        }

        .radio-group {
            display: grid;
            gap: 8px;
            margin-bottom: 14px;
        }

        .radio {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn {
            border-radius: 999px;
            padding: 12px 18px;
            font-weight: 700;
            border: 1px solid transparent;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            transition: transform 0.15s ease, box-shadow 0.2s ease, background 0.2s ease, color 0.2s ease;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 10px 24px rgba(15, 118, 110, 0.25);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-outline {
            color: var(--primary);
            border-color: var(--primary);
            background: #fff;
        }

        .btn-outline:hover {
            background: rgba(15, 118, 110, 0.08);
        }

        .form-error {
            color: #b91c1c;
            font-size: 13px;
            margin: 4px 0 10px;
            min-height: 18px;
        }

        .btn-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-top-color: #fff;
            border-radius: 50%;
            display: none;
            animation: spin 0.8s linear infinite;
        }

        .is-loading .btn-text {
            opacity: 0.7;
        }

        .is-loading .btn-spinner {
            display: inline-block;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 960px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }

            .main-swiper img {
                height: 320px;
            }
        }

        @media (max-width: 640px) {
            .detail-page {
                padding: 18px 12px 28px;
            }

            .item-title {
                font-size: 22px;
            }

            .main-swiper img {
                height: 240px;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
@endsection
