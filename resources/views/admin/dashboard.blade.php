@extends('layouts/app')

@section('content')
<div class="container-fluid">
    
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-fw fa-tachometer-alt mr-2"></i>
        {{ $title ?? 'Dashboard Admin' }}
    </h1>

    <div class="row">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pesanan Selesai (Bulan Ini)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $pesananSelesaiBulanIni ?? 0 }} Transaksi
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Item & Produk Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalProduk ?? 0 }} Produk
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Jumlah Produk per Kategori</h6>
                </div>
                <div class="card-body">
                    @forelse($kategoriData as $kategori)
                        @php
                            // Menghitung persentase untuk lebar progress bar
                            $persentase = $totalProduk > 0 ? ($kategori->item_produksi_count / $totalProduk) * 100 : 0;
                            // Memutar warna progress bar agar tidak monoton
                            $warna = ['danger', 'warning', 'info', 'success', 'primary'][$loop->index % 5];
                        @endphp
                        
                        <h4 class="small font-weight-bold">
                            {{ $kategori->nama_kategori }} 
                            <span class="float-right">{{ $kategori->item_produksi_count ?? 0 }} Produk</span>
                        </h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-{{ $warna }}" role="progressbar" 
                                 style="width: {{ $persentase }}%" 
                                 aria-valuenow="{{ $kategori->item_produksi_count }}" 
                                 aria-valuemin="0" aria-valuemax="{{ $totalProduk }}">
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted py-3">Belum ada data kategori yang direkam.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Produk Baru Ditambahkan</h6>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-borderless mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-dark">Nama Produk</th>
                                    <th class="text-dark">Kategori</th>
                                    <th class="text-dark">Tgl Input</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produkTerbaru as $produk)
                                    <tr>
                                        <td class="font-weight-bold align-middle">
                                            {{ $produk->nama_item ?? '-' }}
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge badge-info px-2 py-1">
                                                {{ $produk->kategoriUsaha?->nama_kategori ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-muted small align-middle">
                                            {{ optional($produk->created_at)->format('d M Y') ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            Belum ada produk baru yang diinput.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">5 Pesanan Terbaru</h6>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped m-0" style="border: 0;">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th>Nota</th>
                                    <th>Klien</th>
                                    <th>Tgl Pesan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($pesananTerbaru as $pesanan)
                                    <tr>
                                        <td class="font-weight-bold text-primary">
                                            #{{ $pesanan->id_pesanan }}
                                        </td>

                                        <td>
                                            {{ $pesanan->pengguna?->nama_pengguna ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $pesanan->created_at?->translatedFormat('d M Y') ?? '-' }}
                                        </td>

                                        <td>
                                            @php
                                                $status = $pesanan->statusPesanan?->nama_status_pesanan ?? '-';
                                            @endphp

                                            <span class="badge badge-pill
                                                @if($status == 'Selesai') badge-success
                                                @elseif($status == 'Diproses') badge-info
                                                @elseif($status == 'Menunggu Pembayaran') badge-warning
                                                @else badge-secondary
                                                @endif p-2">
                                                {{ $status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            Belum ada pesanan masuk untuk kategori admin ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection