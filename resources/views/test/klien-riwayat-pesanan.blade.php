@extends('klien.layouts.app')

@push('head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600&family=Space+Grotesk:wght@400;500;600&display=swap"
        rel="stylesheet">
@endpush
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>

    <section class="order-page">
        <div class="order-hero">
            <div class="order-eyebrow">Klien</div>
            <h1 class="order-title">Riwayat Pesanan</h1>
            <p class="order-subtitle">Pantau status dan bayar ulang jika diperlukan.</p>
        </div>

        <form class="order-filters" method="GET">
            <button name="status" value="" class="{{ empty($filters['status']) ? 'active' : '' }}">Semua</button>
            <button name="status" value="Menunggu Pembayaran">Menunggu Pembayaran</button>
            <button name="status" value="Diproses">Diproses</button>
            <button name="status" value="Selesai">Selesai</button>
            <button name="status" value="Kedaluwarsa/Batal">Kedaluwarsa/Batal</button>
        </form>

        @foreach ($pesanan as $order)
            @php
                $status = $order->statusPesanan->nama_status_pesanan ?? '-';
                $latest = $order->latestPembayaran;
                $isExpired =
                    $status === 'Menunggu Pembayaran' &&
                    $latest &&
                    $latest->status_bayar === 'Pending' &&
                    $latest->created_at->lt(now()->subHours(24));
            @endphp

            <div class="order-card">
                <div class="order-meta">
                    <strong>#{{ $order->id_pesanan }}</strong>
                    <span>{{ optional($order->tanggal_pesan)->format('d M Y') }}</span>
                </div>
                <div class="order-product">
                    {{ $order->detailProduk->itemProduksi->nama_item ?? '-' }}
                </div>
                <div class="order-status {{ $isExpired ? 'expired' : '' }}">
                    {{ $isExpired ? 'Kedaluwarsa' : $status }}
                </div>
                <div class="order-total">
                    Rp {{ number_format($order->total_harga ?? 0, 0, ',', '.') }}
                </div>

                @if ($status === 'Menunggu Pembayaran')
                    <button class="pay-btn" data-id="{{ $order->id_pesanan }}"
                        data-tipe="{{ $latest->tipe_pembayaran ?? 'Full' }}">
                        Bayar Sekarang
                    </button>
                @endif
            </div>
        @endforeach

        {{ $pesanan->links() }}
    </section>

    <script>
        document.querySelectorAll('.pay-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const res = await fetch("{{ route('pembayaran.retry') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                            .content,
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        id_pesanan: btn.dataset.id,
                        tipe_pembayaran: btn.dataset.tipe
                    })
                });

                const data = await res.json();
                if (!data.snap_token) {
                    alert('Gagal membuat transaksi');
                    return;
                }

                window.snap.pay(data.snap_token, {
                    onSuccess: () => window.location.reload(),
                    onPending: () => window.location.reload(),
                    onError: () => alert('Pembayaran gagal')
                });
            });
        });
    </script>
    <style>
        :root {
            --rp-ink: #1f2937;
            --rp-muted: #6b7280;
            --rp-accent: #0f8a4c;
            --rp-accent-2: #f59e0b;
            --rp-danger: #b45309;
            --rp-bg: #f7f4ee;
            --rp-card: #ffffff;
            --rp-border: #ece6db;
            --rp-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
        }

        .order-page {
            font-family: "Space Grotesk", "Nunito", sans-serif;
            color: var(--rp-ink);
        }

        .order-hero {
            position: relative;
            padding: 26px 28px;
            border-radius: 18px;
            border: 1px solid var(--rp-border);
            background:
                radial-gradient(120% 120% at 10% 0%, rgba(15, 138, 76, 0.12), transparent 50%),
                linear-gradient(135deg, #fff6e9 0%, #f2fbf6 100%);
            box-shadow: var(--rp-shadow);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .order-hero::after {
            content: "";
            position: absolute;
            right: -80px;
            top: -70px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle at 30% 30%, rgba(245, 158, 11, 0.35), transparent 60%);
        }

        .order-eyebrow {
            font-size: 12px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #8b6b55;
        }

        .order-title {
            font-family: "Fraunces", serif;
            font-size: 28px;
            margin: 4px 0 6px;
        }

        .order-subtitle {
            margin: 0;
            color: var(--rp-muted);
            max-width: 680px;
        }

        .order-filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 16px 0 18px;
        }

        .order-filters button {
            border: 1px solid var(--rp-border);
            background: #fff;
            color: var(--rp-ink);
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .order-filters button:hover {
            border-color: #d8cdbd;
            transform: translateY(-1px);
        }

        .order-filters .active {
            background: var(--rp-ink);
            color: #fff;
            border-color: var(--rp-ink);
        }

        .order-card {
            position: relative;
            display: grid;
            grid-template-columns: 140px 1.4fr auto auto;
            gap: 16px;
            align-items: center;
            background: var(--rp-card);
            border: 1px solid var(--rp-border);
            border-radius: 14px;
            padding: 16px 18px;
            box-shadow: var(--rp-shadow);
            margin-bottom: 14px;
        }

        .order-card::before {
            content: "";
            position: absolute;
            left: 0;
            top: 12px;
            bottom: 12px;
            width: 4px;
            border-radius: 12px;
            background: linear-gradient(180deg, var(--rp-accent), #18a15c);
        }

        .order-meta strong {
            display: block;
            font-size: 16px;
        }

        .order-meta span {
            color: var(--rp-muted);
            font-size: 13px;
        }

        .order-product {
            font-weight: 600;
        }

        .order-status {
            padding: 6px 12px;
            border-radius: 999px;
            background: #ecfdf3;
            color: #166534;
            font-weight: 600;
            font-size: 12px;
            border: 1px solid #bbf7d0;
        }

        .order-status.expired {
            background: #fff1e5;
            color: #9a3412;
            border-color: #fed7aa;
        }

        .order-total {
            font-weight: 700;
            color: var(--rp-accent);
        }

        .pay-btn {
            border: 0;
            background: var(--rp-accent);
            color: #fff;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            box-shadow: 0 10px 24px rgba(15, 138, 76, 0.2);
        }

        .pay-btn:hover {
            transform: translateY(-1px);
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .order-hero,
        .order-card {
            animation: fadeUp 0.45s ease both;
        }

        @media (max-width: 768px) {
            .order-card {
                grid-template-columns: 1fr;
                text-align: left;
            }

            .order-total {
                order: 3;
            }
        }
    </style>
@endsection
