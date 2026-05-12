<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penawaran Harga</title>
</head>
<body>
    <h2>Penawaran Harga Pesanan</h2>

    <p><strong>ID Pesanan:</strong> {{ $pesanan->id_pesanan }}</p>
    <p><strong>Nama Penerima:</strong> {{ $pesanan->nama_penerima }}</p>
    <p><strong>Harga Awal:</strong> Rp{{ number_format($persetujuan->harga_awal, 0, ',', '.') }}</p>
    <p><strong>Status:</strong> {{ $persetujuan->status_persetujuan }}</p>

    <form method="post" action="{{ route('admin.pesanan.ajukanHarga', $pesanan->id_pesanan) }}">
        @csrf
        <label>Harga Tawaran</label><br>
        <input type="number" name="harga_tawaran" min="0" required><br><br>

        <label>Catatan (opsional)</label><br>
        <textarea name="catatan"></textarea><br><br>

        <button type="submit">Ajukan Harga</button>
    </form>
</body>
</html>