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
                <th>Kode Transaksi</th>
                <th>Tgl Pesan</th>
                <th>Tgl Selesai</th>
                <th>Nama Penerima</th>
                <th>Produk</th> 
                <th>Ukuran</th>
                <th>Qty</th>
                <th>Total Harga</th>
                <th>Tipe Bayar</th>
                <th>Jumlah Bayar</th>
                <th>Payment Type</th>
            </tr>
        </thead>

        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->kode_resi_pesanan ?? '#' . $row->id_pesanan }}</td>
                    <td>{{ $row->tanggal_pesan }}</td>
                    <td>{{ $row->tanggal_selesai }}</td>
                    <td>{{ $row->nama_penerima }}</td>
                    <td>{{ $row->nama_item }}</td>
                    <td>{{ $row->ukuran }}</td>
                    <td>{{ $row->kuantitas }}</td>
                    <td>{{ $row->total_harga }}</td>
                    <td>{{ $row->tipe_pembayaran }}</td>
                    <td>{{ $row->jumlah_bayar }}</td>
                    <td>{{ $row->payment_type }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12">Tidak ada data</td>
                </tr>
            @endforelse

            @if($rows->count() > 0)
                <tr>
                    <td colspan="10" align="right">
                        <strong>Total Pendapatan</strong>
                    </td>
                    <td colspan="2">
                        <strong>
                            Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
                        </strong>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>