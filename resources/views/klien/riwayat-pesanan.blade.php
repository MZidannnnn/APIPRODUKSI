<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Riwayat Pesanan</title>

    <link rel="stylesheet" href="{{ asset('fe-klien/riwayat-pesanan.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}">
    </script>
</head>
<body>

<!-- =========================================================
     HEADER RIWAYAT PESANAN
========================================================= -->
<header class="riwayat-header">
    <img src="{{ asset('assets/images/bg-header-left.png') }}" class="bg-riwayat bg-left" alt="">
    <img src="{{ asset('assets/images/bg-header-right.png') }}" class="bg-riwayat bg-right" alt="">

    <div class="riwayat-header-content">
        <div class="user-info">
            <i class="fas fa-user-circle"></i>

            <div class="user-text">
                <h1>{{ Auth::user()->nama_pengguna ?? 'Nama Klien' }}</h1>

                <a href="{{ route('klien.profile') }}" class="btn-edit-profile">
                    <i class="far fa-pen-to-square"></i>
                    Edit Profile
                </a>
            </div>
        </div>

        <a href="{{ route('logout') }}" class="btn-logout">
            <i class="fas fa-right-from-bracket"></i>
            Log Out
        </a>
    </div>
</header>

<!-- =========================================================
     CONTENT RIWAYAT PESANAN
========================================================= -->
<main class="riwayat-container">

    <a href="{{ route('dashboard') }}" class="btn-kembali-beranda">
        <i class="fas fa-arrow-left"></i>
        Kembali ke Beranda
    </a>

    <div class="page-heading">
        <h2 class="page-title">Riwayat Pesanan</h2>
        <p class="page-subtitle">Lihat status dan pembayaran pesanan kamu di sini.</p>
    </div>

    <!-- =========================================================
         TAB STATUS PESANAN
    ========================================================= -->
    <form class="status-tabs" method="GET">
        <input type="hidden" name="tipe" value="{{ $filters['tipe'] ?? '' }}">

        <button name="status" value=""
            class="{{ empty($filters['status']) ? 'active' : '' }}">
            Semua
        </button>

        <button name="status" value="Belum Bayar"
            class="{{ ($filters['status'] ?? '') === 'Belum Bayar' ? 'active' : '' }}">
            Belum Bayar
        </button>

        <button name="status" value="Pesanan Diproses"
            class="{{ ($filters['status'] ?? '') === 'Pesanan Diproses' ? 'active' : '' }}">
            Diproses
        </button>

        <button name="status" value="Pesanan selesai, silahkan lunasi pembayaran"
            class="{{ ($filters['status'] ?? '') === 'Pesanan selesai, silahkan lunasi pembayaran' ? 'active' : '' }}">
            Belum Lunas
        </button>

        <button name="status" value="Pesanan Selesai"
            class="{{ ($filters['status'] ?? '') === 'Pesanan Selesai' ? 'active' : '' }}">
            Selesai
        </button>

        <button name="status" value="Dibatalkan"
            class="{{ ($filters['status'] ?? '') === 'Dibatalkan' ? 'active' : '' }}">
            Dibatalkan
        </button>
    </form>

    <hr class="divider">

    <!-- =========================================================
         JUDUL STATUS AKTIF
    ========================================================= -->
    @php
        $statusTitle = match ($filters['status'] ?? '') {
            'Belum Bayar' => 'Belum Bayar',
            'Pesanan Diproses' => 'Pesanan Diproses',
            'Pesanan selesai, silahkan lunasi pembayaran' => 'Belum Lunas',
            'Pesanan Selesai' => 'Pesanan Selesai',
            'Dibatalkan' => 'Pesanan Dibatalkan',
            default => 'Semua Pesanan',
        };
    @endphp

    <h3 class="status-title">{{ $statusTitle }}</h3>

    <!-- =========================================================
         LIST PESANAN
    ========================================================= -->
    <div class="order-list">
        @forelse ($pesanan as $order)
            @php
                $status = $order->statusPesanan->nama_status_pesanan ?? '-';

                $latest = $order->latestPembayaran ?? null;
                $latestStatus = $latest?->status_bayar;
                $latestType = $latest?->tipe_pembayaran ?? $latest?->tahap_pembayaran;

                $displayAmount = $latest?->jumlah_bayar ?? ($order->total_harga ?? 0);

                $dpPaid = $order->pembayaran->contains(
                    fn($p) => ($p->tipe_pembayaran ?? $p->tahap_pembayaran) === 'DP' && $p->status_bayar === 'Lunas'
                );

                $pelunasanPaid = $order->pembayaran->contains(
                    fn($p) => ($p->tipe_pembayaran ?? $p->tahap_pembayaran) === 'Pelunasan' && $p->status_bayar === 'Lunas'
                );

                $sisaBayar = method_exists($order, 'sisaBayar')
                    ? $order->sisaBayar()
                    : (($order->total_harga ?? 0) - $order->pembayaran->where('status_bayar', 'Lunas')->sum('jumlah_bayar'));

                $isExpired = $status === 'Pesanan Kadaluarsa';

                $canPayDp = $status === 'Belum Bayar'
                    && $latestType === 'DP'
                    && $latestStatus !== 'Lunas';

                $canPayFull = $status === 'Belum Bayar'
                    && in_array($latestType, ['Full'], true)
                    && $latestStatus !== 'Lunas';

                $canPayPelunasan = $status === 'Pesanan selesai, silahkan lunasi pembayaran'
                    && !$pelunasanPaid
                    && $sisaBayar > 0;

                $canCancel = $status === 'Belum Bayar';

                $produk = $order->detailProduk?->itemProduksi;
                $foto = $produk?->fotoProduk?->first();

                $badgeClass = match ($status) {
                    'Belum Bayar' => 'badge-warning',
                    'Pesanan Diproses' => 'badge-process',
                    'Pesanan selesai, silahkan lunasi pembayaran' => 'badge-unpaid',
                    'Pesanan Selesai' => 'badge-success',
                    'Pesanan Dibatalkan', 'Pesanan Kadaluarsa' => 'badge-danger',
                    default => 'badge-default',
                };

                $badgeText = match ($status) {
                    'Pesanan selesai, silahkan lunasi pembayaran' => 'Belum Lunas',
                    'Pesanan Kadaluarsa' => 'Kadaluarsa',
                    default => $status,
                };

                $rincian = $order->rincianPesanan->first();
            @endphp

            <div class="order-card">
                <!-- BAGIAN KIRI CARD -->
                <div class="order-left">
                   @if ($foto)
                        <img class="order-img"
                            src="{{ asset($foto->nama_foto) }}"
                            alt="{{ $produk->nama_item }}">
                    @else
                        <img class="order-img"
                            src="{{ asset('assets/images/no-image.png') }}"
                            alt="No Image">
                    @endif

                    <div class="order-info">
                        <h4>{{ $produk->nama_item ?? '-' }}</h4>

                        <div class="order-meta">
                            <span>
                                <i class="fas fa-ruler-combined"></i>
                                Ukuran: {{ $order->detailProduk->ukuran ?? '-' }}
                            </span>

                            <span>
                                <i class="fas fa-box"></i>
                                {{ $rincian->kuantitas ?? 1 }}x
                            </span>
                        </div>

                        <p>
                            <i class="fas fa-calendar-days"></i>
                            Jadwal Pemasangan:
                            {{ $order->jadwal_pemasangan ? \Carbon\Carbon::parse($order->jadwal_pemasangan)->translatedFormat('d F Y') : '-' }}
                        </p>

                        <span class="badge-status {{ $badgeClass }}">
                            {{ $badgeText }}
                        </span>

                        @if ($isExpired)
                            <p class="expired-text">
                                Pembayaran sudah melewati batas waktu 24 jam.
                            </p>
                        @endif
                    </div>
                </div>

                <!-- BAGIAN KANAN CARD -->
                <div class="order-right">
                    <small>Total Pembayaran:</small>
                    <h3>Rp {{ number_format((float) $displayAmount, 0, ',', '.') }}</h3>

                    @if ($canPayPelunasan)
                        <small class="sisa-bayar">
                            Sisa Bayar:
                            Rp {{ number_format((float) $sisaBayar, 0, ',', '.') }}
                        </small>
                    @endif

                    <div class="order-actions">
                        @if ($canCancel)
                            <button type="button" class="btn-outline-danger cancel-btn"
                                data-endpoint="{{ route('pesanan.batal', $order->id_pesanan) }}">
                                Batalkan Pesanan
                            </button>
                        @endif

                        @if ($canPayDp)
                            <button type="button" class="btn-success pay-btn"
                                data-id="{{ $order->id_pesanan }}"
                                data-tipe="DP">
                                Bayar DP
                            </button>
                        @elseif ($canPayFull)
                            <button type="button" class="btn-success pay-btn"
                                data-id="{{ $order->id_pesanan }}"
                                data-tipe="Full">
                                Bayar Sekarang
                            </button>
                        @endif

                        @if ($canPayPelunasan)
                            <button type="button" class="btn-success pay-btn"
                                data-id="{{ $order->id_pesanan }}"
                                data-tipe="Pelunasan">
                                Lunasi Pembayaran
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-order">
                <i class="fas fa-box-open"></i>
                <h4>Belum ada pesanan</h4>
                <p>Pesanan kamu akan tampil di halaman ini.</p>
            </div>
        @endforelse
    </div>

    <!-- =========================================================
         PAGINATION
    ========================================================= -->
    <div class="pagination-wrapper">
        {{ $pesanan->links() }}
    </div>

</main>

<!-- =========================================================
     JAVASCRIPT MIDTRANS & CANCEL PESANAN
========================================================= -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.pay-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const endpoint = btn.dataset.tipe === "Pelunasan"
                ? "{{ route('pembayaran.midtrans') }}"
                : "{{ route('pembayaran.retry') }}";

            const res = await fetch(endpoint, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    id_pesanan: btn.dataset.id,
                    tipe_pembayaran: btn.dataset.tipe
                })
            });

            const data = await res.json();

            if (!res.ok || !data.snap_token) {
                alert(data.message || 'Gagal membuat pembayaran.');
                return;
            }

            window.snap.pay(data.snap_token, {
                onSuccess: async function () {
                    await fetch("{{ route('pembayaran.sync-success', ':id') }}".replace(':id', data.id_pembayaran), {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                            "Accept": "application/json"
                        }
                    });

                    window.location.href =
                        "{{ route('pembayaran.upload.form', ':id') }}"
                        .replace(':id', data.id_pembayaran);
                },
                onPending: function () {
                    window.location.reload();
                },
                onError: function () {
                    alert('Pembayaran gagal.');
                },
                onClose: function () {
                    window.location.reload();
                }
            });
        });
    });

    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm('Yakin ingin membatalkan pesanan ini?')) return;

            const res = await fetch(btn.dataset.endpoint, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json"
                }
            });

            const data = await res.json();

            if (!res.ok) {
                alert(data.message || 'Gagal membatalkan pesanan.');
                return;
            }

            alert(data.message || 'Pesanan berhasil dibatalkan.');
            window.location.reload();
        });
    });

    const params = new URLSearchParams(window.location.search);

    if (params.get('bayar') === '1') {
        const snapToken = sessionStorage.getItem('snap_token');
        const idPembayaran = sessionStorage.getItem('id_pembayaran');

        if (snapToken && window.snap) {
            window.snap.pay(snapToken, {
                onSuccess: async function () {
                    await fetch("{{ route('pembayaran.sync-success', ':id') }}".replace(':id', idPembayaran), {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                            "Accept": "application/json"
                        }
                    });

                    sessionStorage.removeItem('snap_token');
                    sessionStorage.removeItem('id_pembayaran');

                    window.location.href =
                        "{{ route('pembayaran.upload.form', ':id') }}"
                        .replace(':id', idPembayaran);
                },

                onPending: function () {
                    sessionStorage.removeItem('snap_token');
                    sessionStorage.removeItem('id_pembayaran');

                    window.location.href =
                        "{{ route('pembayaran.upload.form', ':id') }}"
                        .replace(':id', idPembayaran);
                },

                onError: function () {
                    alert('Pembayaran gagal.');
                },

                onClose: function () {
                    console.log('Popup Midtrans ditutup');
                }
            });
        }
    }

});
</script>

</body>
</html>