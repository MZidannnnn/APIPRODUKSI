@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Laporan Penjualan</h4>

    <form method="GET" action="{{ route('laporan.penjualan.index') }}" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <label>Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}" class="form-control">
            </div>
            <div class="col-md-6 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
                @if (!empty($filters['tanggal_mulai']) && !empty($filters['tanggal_selesai']))
                    <a href="{{ route('laporan.penjualan.excel', $filters) }}" class="btn btn-success">Export Excel</a>
                    <a href="{{ route('laporan.penjualan.pdf', $filters) }}" class="btn btn-danger">Export PDF</a>
                @endif
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered">
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
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $rows->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection