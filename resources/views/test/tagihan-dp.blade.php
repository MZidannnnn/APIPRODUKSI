<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tagihan Pesanan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
</head>
<body>
    <h2>Tagihan Pesanan</h2>

    <p><strong>Status:</strong> {{ $pesanan->statusPesanan->nama_status_pesanan ?? '-' }}</p>
    <p><strong>Total Pesanan:</strong> Rp{{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
    <p><strong>Sudah Dibayar:</strong> Rp{{ number_format($pesanan->pembayaran->sum('jumlah_bayar'), 0, ',', '.') }}</p>
    <p><strong>Sisa Tagihan:</strong> Rp{{ number_format($pesanan->sisaBayar(), 0, ',', '.') }}</p>

    @if($pesanan->statusPesanan && $pesanan->statusPesanan->nama_status_pesanan === 'Selesai' && $pesanan->sisaBayar() > 0)
        <button id="bayar-tagihan">Bayar Tagihan</button>
    @else
        <p>Tagihan belum tersedia.</p>
    @endif

    <script>
        const button = document.getElementById('bayar-tagihan');

        if (button) {
            button.addEventListener('click', async function () {
                const resBayar = await fetch("{{ route('pembayaran.midtrans') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        id_pesanan: {{ $pesanan->id_pesanan }},
                        tipe_pembayaran: "Pelunasan"
                    })
                });

                const bayar = await resBayar.json();

                if (!bayar.snap_token) {
                    alert('Gagal membuat tagihan');
                    return;
                }

                window.snap.pay(bayar.snap_token, {
                    onSuccess: function () {
                        alert('Pembayaran sukses');
                        window.location.reload();
                    },
                    onPending: function () {
                        alert('Pembayaran pending');
                        window.location.reload();
                    },
                    onError: function () {
                        alert('Pembayaran gagal');
                    }
                });
            });
        }
    </script>
</body>
</html>