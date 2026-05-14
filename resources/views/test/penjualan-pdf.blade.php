<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h3>Laporan Penjualan</h3>
    <p>Periode: {{ $filters['tanggal_mulai'] }} s.d {{ $filters['tanggal_selesai'] }}</p>

    <table>
        <thead>
        <tr>
            <th>No Pesanan</th>
            <th>Tgl Pesan</th>
            <th>Nama Penerima</th>
            <th>Produk</th>
            <th>Ukuran</th>
            <th>Qty</th>
            <th>Total Harga</th>
            <th>Status Pesanan</th>
            <th>Tgl Bayar</th>
            <th>Tipe Bayar</th>
            <th>Jumlah Bayar</th>
            <th>Payment Type</th>
            <th>Status Bayar</th>
        </tr>
        </thead>
        <tbody>
        @forelse($rows as $row)
            <tr>
                <td>{{ $row->id_pesanan }}</td>
                <td>{{ $row->tanggal_pesan }}</td>
                <td>{{ $row->nama_penerima }}</td>
                <td>{{ $row->nama_item }}</td>
                <td>{{ $row->ukuran }}</td>
                <td>{{ $row->kuantitas }}</td>
                <td>{{ $row->total_harga }}</td>
                <td>{{ $row->status_pesanan }}</td>
                <td>{{ optional($row->tanggal_bayar)->format('Y-m-d') }}</td>
                <td>{{ $row->tipe_pembayaran }}</td>
                <td>{{ $row->jumlah_bayar }}</td>
                <td>{{ $row->payment_type }}</td>
                <td>{{ $row->status_bayar }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="13">Tidak ada data</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>