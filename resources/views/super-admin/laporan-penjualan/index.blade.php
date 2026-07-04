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

            @if (!empty($filters['tanggal_mulai']) && !empty($filters['tanggal_selesai']))
                <div class="alert alert-success mt-3 mb-0">
                    <strong>Total Penjualan:</strong>
                    Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
                </div>
            @endif

        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr class="text-center">
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
                        @foreach($laporan as $row)
                            <tr>
                                <td class="font-weight-bold text-gray-800">
                                    <span class="badge badge-dark px-2 py-1">
                                        {{ $row->kode_resi_pesanan ?? '#' . $row->id_pesanan }}
                                    </span>
                                </td>
                                <td>
                                    <i class="far fa-calendar-alt text-muted mr-1"></i>
                                    {{ $row->tanggal_pesan ? \Carbon\Carbon::parse($row ->tanggal_pesan)->format('d M Y') : '-' }}
                                </td>
                                <td>
                                    <i class="far fa-calendar-alt text-muted mr-1"></i>
                                    {{ $row->tanggal_selesai ? \Carbon\Carbon::parse($row->tanggal_selesai)->format('d M Y') : '-' }}
                                </td>
                                <td>{{ $row->nama_penerima }}</td>
                                <td>{{ $row->nama_item }}</td>
                                <td>{{ $row->ukuran }}</td>
                                <td class="text-center">{{ $row->kuantitas }}</td>
                                <td class="font-weight-bold text-success text-right">
                                    Rp {{ number_format($row->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="text-center">{{ $row->tipe_pembayaran }}</td>
                                <td class="font-weight-bold text-success text-right">
                                    Rp {{ number_format($row->jumlah_bayar ?? 0, 0, ',', '.') }}
                                </td>
                                <td>{{ $row->payment_type }}</td>
                            </tr>
                        @endforeach
                        </tbody>

                </table>
            </div>
        </div>
    </div>
@endsection