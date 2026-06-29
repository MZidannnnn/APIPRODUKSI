<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan</title>

    <link rel="stylesheet" href="{{ asset('fe-klien/detail-pesanan.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<section class="detail-wrapper">

    <a href="{{ route('klien.pesanan.riwayat') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
    </a>

    <div class="detail-header">
        <h1>Detail Pesanan</h1>
        <p>{{ $pesanan->kode_resi_pesanan ?? '#' . $pesanan->id_pesanan }}</p>
    </div>
    
    <div class="detail-card">

        <div class="produk-section">
            @php
                $produk = $pesanan->detailProduk?->itemProduksi;
                $foto = $produk?->fotoProduk?->first();
            @endphp

            @if ($foto)
                <img src="{{ asset($foto->nama_foto) }}" alt="{{ $produk->nama_item }}">
            @else
                <img src="{{ asset('images/no-image.png') }}" alt="Tidak ada foto">
            @endif

            <div class="produk-info">
                <h2>{{ $pesanan->detailProduk->itemProduksi->nama_item }}</h2>
                <p>{{ $pesanan->detailProduk->ukuran }}</p>

                <span class="badge-status">
                    {{ $pesanan->statusPesanan->nama_status_pesanan }}
                </span>
            </div>
        </div>

        <div class="info-grid">

            <div class="info-box">
                <label>Nama Penerima</label>
                <p>{{ $pesanan->nama_penerima }}</p>
            </div>

            <div class="info-box">
                <label>No HP</label>
                <p>{{ $pesanan->No_hp_penerima }}</p>
            </div>

            <div class="info-box full">
                <label>Alamat</label>
                <p>{{ $pesanan->alamat_penerima }}</p>
            </div>

            <div class="info-box">
                <label>Tanggal Pesan</label>
                <p>{{ $pesanan->tanggal_pesan }}</p>
            </div>

            <div class="info-box">
                @php
                    $kategori = $pesanan->detailProduk?->itemProduksi?->kategoriUsaha?->nama_kategori;
                @endphp

                @if (strtolower($kategori) === strtolower('Space Iklan Baliho')) 
                    <div class="info-box">
                        <label>Jadwal Pemasangan</label>
                        <p>{{ $pesanan->jadwal_pemasangan ?? '-' }}</p>
                    </div>
                @endif
            </div>

        </div>

        <div class="payment-section">
            <h3>Riwayat Pembayaran</h3>

            @forelse ($pesanan->pembayaran as $bayar)
                <div class="payment-item">
                    <div>
                        <strong>{{ $bayar->tipe_pembayaran }}</strong>
                        <p>{{ $bayar->payment_type ?? '-' }}</p>
                    </div>

                    <div>
                        <strong>Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</strong>
                        <span>{{ $bayar->status_bayar }}</span>
                    </div>
                </div>
            @empty
                <p>Belum ada pembayaran.</p>
            @endforelse
        </div>

    </div>

</section>

</body>
</html>