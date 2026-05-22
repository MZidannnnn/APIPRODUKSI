@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-solid fa-file mr-2"></i>
        {{ $title }}
    </h1>
 
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.penjualan.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Tanggal Mulai</label>
                        <input 
                            type="date" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}" class="form-control">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Tanggal Selesai</label>
                        <input 
                            type="date" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3 d-flex align-items-end flex-wrap">
                        <button type="submit" class="btn btn-primary mr-2 mb-2">
                            <i class="fas fa-search mr-1"></i>
                            Tampilkan
                        </button>
                        <a href="{{ route('laporan.penjualan.index') }}" class="btn btn-secondary mr-2 mb-2">
                            <i class="fas fa-sync-alt mr-1"></i>
                            Reset
                        </a>

                        @if (!empty($filters['tanggal_mulai']) && !empty($filters['tanggal_selesai']))
                            <a href="{{ route('laporan.penjualan.excel', $filters) }}"
                                class="btn btn-success mr-2 mb-2">
                                <i class="fas fa-file-excel mr-1"></i>
                                Export Excel
                            </a>
                            <a href="{{ route('laporan.penjualan.pdf', $filters) }}"
                                class="btn btn-danger mb-2">
                                <i class="fas fa-file-pdf mr-1"></i>
                                Export PDF
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr class="text-center">
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
                        @foreach($laporan as $row)
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
                        @endforeach
                        </tbody>

                </table>
            </div>
        </div>
    </div>
@endsection