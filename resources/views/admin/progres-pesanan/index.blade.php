@extends('layouts/app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-2">
        <div>
            <h1 class="h4 mb-0 text-gray-800 font-weight-bold">
                <i class="fas fa-fw fa-shipping-fast text-warning mr-2"></i> {{ $title }}
            </h1>
            
            <div class="mt-2 d-flex align-items-center flex-wrap">
                <span class="text-muted small">
                    <i class="fas fa-user-shield mr-1"></i> Area Kerja Admin
                </span>
                <span class="mx-2 text-gray-300">|</span>
                <span class="badge text-white px-2 py-1 font-weight-bold shadow-sm" style="background-color: #ef6c00; font-size: 11px; border-radius: 4px;">
                    <i class="fas fa-tags mr-1"></i> Kategori {{ Auth::user()->kategori->nama_kategori ?? '-' }}
                </span>
                <span class="mx-2 text-gray-300">|</span>
                <p class="text-muted small mb-0">Memantau alur produksi pesanan masuk secara real-time.</p>
            </div>
        </div>
        
        <div class="d-flex card-shadow-sm">
            <div class="bg-dark text-white px-3 py-2 rounded-left small font-weight-bold d-flex align-items-center">
                <i class="fas fa-shopping-basket mr-2 text-warning"></i> Total Monitor
            </div>
            <div class="bg-white border border-left-0 px-3 py-2 rounded-right small font-weight-bold text-gray-800 d-flex align-items-center">
                {{ $pesanan->count() }} Data
            </div>
        </div>
    </div>

    <div class="card shadow mb-4" style="border-radius: 8px; overflow: hidden;">
        <div class="card-header py-3 bg-dark d-flex align-items-center justify-content-between" style="border-bottom: 2px solid #ef6c00;">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-list mr-2" style="color: #ef6c00;"></i> Daftar Antrean Produksi
            </h6>
            <span class="badge text-white small" style="background-color: #ef6c00; padding: 5px 10px;">
                <i class="fas fa-sync fa-spin mr-1"></i> Live Mode
            </span>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-gray-800 border-bottom">
                        <tr class="text-center">
                            <th width="8%">ID Pesanan</th>
                            <th>Penerima</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th width="15%">Status</th>
                            <th>Tanggal Masuk</th>
                            <th>Total Harga</th>
                            <th width="10%">
                                <i class="fas fa-cog"></i>Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        {{-- Trik @if agar DataTables tidak crash & search box tetap muncul --}}
                        @if ($pesanan->count() > 0)
                            @foreach ($pesanan as $item)
                                <tr class="text-center align-middle">
                                    <td class="font-weight-bold text-gray-800">#{{ $item->id_pesanan }}</td>
                                    <td>{{ $item->nama_penerima }}</td>
                                    <td>{{ $item->detailProduk?->itemProduksi?->nama_item ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-secondary px-2 py-1">
                                            {{ $item->detailProduk?->itemProduksi?->kategoriUsaha?->nama_kategori ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning text-dark font-weight-bold px-2 py-1 w-100">
                                            <i class="fas fa-spinner fa-pulse mr-1"></i> 
                                            {{ $item->statusPesanan?->nama_status_pesanan ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <i class="far fa-calendar-alt text-muted mr-1"></i>
                                        {{ $item->tanggal_pesan ? \Carbon\Carbon::parse($item->tanggal_pesan)->format('d M Y') : '-' }}
                                    </td>
                                    <td class="font-weight-bold text-success text-right">
                                        Rp {{ number_format($item->total_harga ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <a href="{{ url('admin/pesanan/'.$item->id_pesanan) }}" class="btn btn-sm text-white shadow-sm" style="background-color: #ef6c00;" title="Detail Pesanan">
                                            <i class="fas fa-eye mr-1"></i> Periksa
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection