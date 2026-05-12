<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Status Penawaran</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
</head>
<body>
    <h2>Status Penawaran Harga</h2>

    <p><strong>Status:</strong> {{ $persetujuan->status_persetujuan }}</p>
    <p><strong>Harga Awal:</strong> Rp{{ number_format($persetujuan->harga_awal, 0, ',', '.') }}</p>
    <p><strong>Harga Tawaran:</strong>
        @if($persetujuan->harga_tawaran)
            Rp{{ number_format($persetujuan->harga_tawaran, 0, ',', '.') }}
        @else
            Belum ada
        @endif
    </p>
    <p><strong>Catatan:</strong> {{ $persetujuan->catatan ?? '-' }}</p>

    @if($persetujuan->status_persetujuan === 'Menunggu' || $persetujuan->status_persetujuan === 'Ditawarkan')
        <form method="post" action="{{ route('pesanan.setujuHarga', $pesanan->id_pesanan) }}">
            @csrf
            <button type="submit">Setujui Harga</button>
        </form>

        <form method="post" action="{{ route('pesanan.tolakHarga', $pesanan->id_pesanan) }}">
            @csrf
            <button type="submit">Tolak Harga</button>
        </form>
    @endif

    @if($persetujuan->status_persetujuan === 'Disetujui')
        <hr>
        <h3>Pembayaran</h3>

        <label><input type="radio" name="tipe_pembayaran" value="DP" checked> DP 50%</label>
        <label><input type="radio" name="tipe_pembayaran" value="Full"> Full 100%</label><br><br>

        <button id="btn-bayar">Bayar Sekarang</button>
    @endif

    <script>
        const btn = document.getElementById('btn-bayar');
        if (btn) {
            btn.addEventListener('click', async function () {
                const tipePembayaran = document.querySelector('input[name="tipe_pembayaran"]:checked').value;

                const resBayar = await fetch("{{ route('pembayaran.midtrans') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        id_pesanan: {{ $pesanan->id_pesanan }},
                        tipe_pembayaran: tipePembayaran
                    })
                });

                const bayar = await resBayar.json();
                if (!bayar.snap_token) {
                    alert("Gagal membuat transaksi");
                    return;
                }

                window.snap.pay(bayar.snap_token, {
                    onSuccess: function() { window.location.reload(); },
                    onPending: function() { window.location.reload(); },
                    onError: function() { alert("Pembayaran gagal"); }
                });
            });
        }
    </script>
</body>
</html>