@extends('klien.layouts.app')
@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
@endpush
@section('content')
    <div class="detail-page">
        <div class="detail-grid">
            <section class="detail-card">
                <a class="back-link" href="{{ route('pesanan.listItem') }}">← Kembali</a>
                <h2 class="item-title">{{ $itemProduksi->nama_item }}</h2>
                <div class="item-meta">
                    <span>Kategori: {{ $itemProduksi->kategoriUsaha->nama_kategori ?? '-' }}</span>
                    <span>Status: {{ $itemProduksi->status_aktif }}</span>
                </div>
                <p class="item-desc">{{ $itemProduksi->deskripsi_item }}</p>
                @if ($itemProduksi->gambar_item)
                    <img class="item-image" src="{{ asset('storage/' . $itemProduksi->gambar_item) }}" alt="Gambar">
                @endif

                @if ($itemProduksi->detailProduk)
                    <div class="item-detail">
                        <div>Satuan: {{ $itemProduksi->detailProduk->satuanHarga->nama_satuan ?? '-' }}</div>
                        <div>Ukuran: {{ $itemProduksi->detailProduk->ukuran }}</div>
                        <div>Harga Dasar: Rp{{ number_format($itemProduksi->detailProduk->harga_dasar, 0, ',', '.') }}
                        </div>
                    </div>
                @endif

                @auth
                    <a class="btn chat-cta" href="{{ route('chat.show', $percakapan->id_percakapan) }}"">Chat Admin</a>
                @endauth
            </section>

            <section class="purchase-card">
                <h3 class="section-title">Beli Sekarang</h3>
                {{-- Pindahkan form pembelian kamu yang sudah ada ke sini, tanpa mengubah field --}}
                <form id="form-pesanan" method="post" action="{{ route('pesanan.beli') }}">
                    @csrf
                    @php
                        $bolehDp =
                            strtolower($itemProduksi->kategoriUsaha->jenisPembayaran->nama_jenis_pembayaran ?? '') ===
                            'dp';
                    @endphp
                    @if ($bolehDp)
                        <label><input type="radio" name="tipe_pembayaran" value="DP" checked> Bayar DP 50%</label><br>
                        <label><input type="radio" name="tipe_pembayaran" value="Full"> Bayar Full 100%</label><br><br>
                    @else
                        <input type="hidden" name="tipe_pembayaran" value="Full">
                    @endif

                    <input type="hidden" name="id_detail_produk"
                        value="{{ $itemProduksi->detailProduk->id_detail_produk ?? '' }}">

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
        </div>

        <br>
        <button class="btn" type="submit">Beli Sekarang</button>
        </form>


        {{-- script pembayaran --}}
        <script>
            const form = document.getElementById('form-pesanan');
            // const isCustom = @json($sablon ?? false);

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(form);

                // 1) Buat pesanan
                const resPesanan = await fetch("{{ route('pesanan.beli') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Accept": "application/json"
                    },
                    body: formData
                });

                const pesanan = await resPesanan.json();
                if (!pesanan.id_pesanan) {
                    alert("Gagal buat pesanan");
                    return;
                }

                // 2) Jika item custom/sablon, arahkan ke status penawaran
                // if (isCustom) {
                //     window.location.href = `/pesanan/${pesanan.id_pesanan}/status-harga`;
                //     return;
                // }

                // 3) Jika bukan custom, lanjut ke Midtrans
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

                const bayar = await resBayar.json();
                const pembayaranId = bayar.id_pembayaran;
                if (!bayar.snap_token) {
                    alert("Gagal buat transaksi");
                    return;
                }

                window.snap.pay(bayar.snap_token, {
                    onSuccess: function() {
                        window.location.href = `/pembayaran/${pembayaranId}/upload-bukti`;
                    },
                    onPending: function() {
                        window.location.href = `/pembayaran/${pembayaranId}/upload-bukti`;
                    },
                    onError: function() {
                        alert("Pembayaran gagal");
                    }
                });
            });
        </script>
        </section>
    </div>


    </div>

    <style>
        .detail-page {
            max-width: 1000px;
            margin: 0 auto;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .detail-card,
        .purchase-card,
        .chat-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 18px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .item-title {
            font-size: 22px;
            margin: 10px 0;
        }

        .item-meta {
            display: flex;
            gap: 16px;
            font-size: 13px;
            color: #555;
        }

        .item-desc {
            margin: 12px 0;
            line-height: 1.6;
        }

        .item-image {
            max-width: 100%;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .item-detail {
            margin-top: 12px;
            font-size: 14px;
            color: #333;
            display: grid;
            gap: 6px;
        }

        .section-title {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .chat-cta {
            display: inline-block;
            margin-top: 12px;
        }

        .chat-card {
            margin-top: 24px;
        }

        .chat-header {
            font-weight: 700;
            margin-bottom: 10px;
        }

        .chat-messages {
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            padding: 12px;
            height: 340px;
            overflow-y: auto;
            background: #fafafa;
        }

        .chat-bubble {
            background: #ffffff;
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 8px 10px;
            margin-bottom: 8px;
            max-width: 80%;
        }

        .chat-bubble.me {
            background: #e9f6ff;
            border-color: #d0ecff;
            margin-left: auto;
        }

        .chat-text {
            font-size: 14px;
        }

        .chat-time {
            font-size: 11px;
            color: #777;
            margin-top: 4px;
            text-align: right;
        }

        .chat-form {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .chat-form textarea {
            flex: 1;
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 8px;
        }
    </style>

@endsection
