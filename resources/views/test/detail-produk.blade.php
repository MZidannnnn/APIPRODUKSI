<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Item Produksi</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
</head>

<body>
    <h2>Detail Item Produksi</h2>
    <a href="{{ route('pesanan.listItem') }}">← Kembali ke List</a>
    <div>
        <strong>{{ $itemProduksi->nama_item }}</strong><br>
        Kategori: {{ $itemProduksi->kategoriUsaha->nama_kategori ?? '-' }}<br>
        Status: {{ $itemProduksi->status_aktif }}<br>
        <p>{{ $itemProduksi->deskripsi_item }}</p>
        @if ($itemProduksi->gambar_item)
            <img src="{{ asset('storage/' . $itemProduksi->gambar_item) }}" width="200"><br>
        @endif
        <h4>Detail Produk</h4>
        @if ($itemProduksi->detailProduk)
            Satuan: {{ $itemProduksi->detailProduk->satuanHarga->nama_satuan ?? '-' }}<br>
            Ukuran: {{ $itemProduksi->detailProduk->ukuran }}<br>
            Harga Dasar: Rp{{ number_format($itemProduksi->detailProduk->harga_dasar, 0, ',', '.') }}<br>
        @endif
    </div>
    <hr>
    <h4>Beli Sekarang</h4>
 <form id="form-pesanan">
        <input type="hidden" name="id_detail_produk" value="{{ $itemProduksi->detailProduk->id_detail_produk ?? '' }}">
        <label>Nama Penerima</label><br>
        <input type="text" name="nama_penerima" required><br><br>

        <label>Alamat Penerima</label><br>
        <textarea name="alamat_penerima" required></textarea><br><br>

        <label>No HP Penerima</label><br>
        <input type="text" name="No_hp_penerima" required><br><br>

        <button type="submit">Beli Sekarang</button>
    </form>

    <script>
        const form = document.getElementById('form-pesanan');

        form.addEventListener('submit', async function (e) {
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

            // 2) Minta Snap token dari backend pembayaran
            const resBayar = await fetch("{{ route('pembayaran.midtrans') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json"
                },
                body: JSON.stringify({ id_pesanan: pesanan.id_pesanan })
            });

            const bayar = await resBayar.json();
            const pembayaranId = bayar.id_pembayaran;
            if (!bayar.snap_token) {
                alert("Gagal buat transaksi");
                return;
            }

            // 3) Tampilkan popup Midtrans
            window.snap.pay(bayar.snap_token, {
                onSuccess: function (result) {
                    alert("Pembayaran sukses");
                    console.log(result);
                    window.location.href = `/pembayaran/${pembayaranId}/upload-bukti`;
                },
                onPending: function (result) {
                    alert("Pembayaran pending");
                    console.log(result);
                    window.location.href = `/pembayaran/${pembayaranId}/upload-bukti`;
                },
                onError: function (result) {
                    alert("Pembayaran gagal");
                    console.log(result);
                }
            });
        });
    </script>
</body>
</html>
