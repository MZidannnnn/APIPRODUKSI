<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Item Produksi</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <style>
        :root {
            --bg: #f4efe6;
            --paper: #fff8f0;
            --ink: #1a1a1a;
            --accent: #0b5ed7;
        }

        body {
            font-family: "Merriweather", serif;
            background: radial-gradient(circle at 20% 20%, #f9f1e7, #efe6da);
            color: var(--ink);
            padding: 24px;
        }

        .card {
            background: var(--paper);
            border: 1px solid #e6d7c7;
            padding: 20px;
            max-width: 900px;
            margin: 0 auto 20px auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
        }

        .field label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .field input,
        .field select,
        .field textarea {
            margin-bottom: 12px;
            margin-right: 12px;
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #d9cfc2;
            border-radius: 6px;
            background: #fff;
        }

        .btn {
            background: var(--accent);
            color: #fff;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
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
    <div class="card">
        <form id="form-pesanan" method="post" action="{{ route('pesanan.beli') }}">
            @csrf
            @php
                $bolehDp =
                    strtolower($itemProduksi->kategoriUsaha->jenisPembayaran->nama_jenis_pembayaran ?? '') === 'dp';
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
    </div>
    {{-- <form id="form-pesanan" method="post" action="{{ route('pesanan.beli') }}">
        @csrf
        @php
            $bolehDp = strtolower($itemProduksi->kategoriUsaha->jenisPembayaran->nama_jenis_pembayaran ?? '') === 'dp';
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
            <label>Kuantitas</label>
            <input type="number" name="kuantitas" min="1" value="1" required>

            <label><input type="checkbox" name="jasa_sablon" value="1"> Jasa sablon</label>

            <label>Barang disediakan usaha?</label>
            <select name="barang_disediakan_usah" required>
                <option value="Ya">Ya</option>
                <option value="Tidak">Tidak</option>
            </select>

            <label>File desain (opsional)</label>
            <input type="file" name="file_desain">
        @endif


        <label>Nama Penerima</label><br>
        <input type="text" name="nama_penerima" required><br><br>

        <label>Alamat Penerima</label><br>
        <textarea name="alamat_penerima" required></textarea><br><br>

        <label>No HP Penerima</label><br>
        <input type="text" name="No_hp_penerima" required><br><br>

        <button type="submit">Beli Sekarang</button>
    </form> --}}

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
    {{-- <script>
        const form = document.getElementById('form-pesanan');

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

            const tipePembayaran = form.querySelector('input[name="tipe_pembayaran"]:checked') ?
                form.querySelector('input[name="tipe_pembayaran"]:checked').value :
                form.querySelector('input[name="tipe_pembayaran"]').value;

            // 2) Minta Snap token dari backend pembayaran
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

            // 3) Tampilkan popup Midtrans
            window.snap.pay(bayar.snap_token, {
                onSuccess: function(result) {
                    alert("Pembayaran sukses");
                    console.log(result);
                    window.location.href = `/pembayaran/${pembayaranId}/upload-bukti`;
                },
                onPending: function(result) {
                    alert("Pembayaran pending");
                    console.log(result);
                    window.location.href = `/pembayaran/${pembayaranId}/upload-bukti`;
                },
                onError: function(result) {
                    alert("Pembayaran gagal");
                    console.log(result);
                }
            });
        });
    </script> --}}
</body>

</html>
