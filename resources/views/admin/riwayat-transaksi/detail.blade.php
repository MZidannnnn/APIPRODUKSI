@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-2">
        <div>
            <h1 class="h4 mb-0 text-gray-800 font-weight-bold">
                <i class="fas fa-fw fa-file-invoice-dollar text-warning mr-2"></i>
                Detail Transaksi
            </h1>

            <div class="mt-2 d-flex align-items-center flex-wrap">
                <span class="text-muted small">
                    <i class="fas fa-receipt mr-1"></i>
                    {{ $pesanan->kode_resi_pesanan ?? '#' . $pesanan->id_pesanan }}
                </span>

                <span class="mx-2 text-gray-300">|</span>

                <span class="badge text-white px-2 py-1 font-weight-bold shadow-sm"
                      style="background-color:#ef6c00; font-size:11px; border-radius:4px;">
                    <i class="fas fa-tags mr-1"></i>
                    {{ $pesanan->detailProduk?->itemProduksi?->kategoriUsaha?->nama_kategori ?? '-' }}
                </span>
            </div>
        </div>

        <a href="{{ route('admin.riwayat-transaksi.index') }}"
           class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i>
            Kembali
        </a>
    </div>

    <div class="card shadow mb-4" style="border-radius:8px; overflow:hidden;">
        <div class="card-header py-3 bg-dark" style="border-bottom:2px solid #ef6c00;">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-file-invoice mr-2" style="color:#ef6c00;"></i>
                Nota Transaksi
            </h6>
        </div>

        <div class="card-body">

            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card border-left-warning shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Kode Transaksi
                            </div>
                            <div class="font-weight-bold text-gray-800">
                                {{ $pesanan->kode_resi_pesanan ?? '#' . $pesanan->id_pesanan }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card border-left-success shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Transaksi
                            </div>
                            <div class="font-weight-bold text-success">
                                Rp {{ number_format($pesanan->total_harga ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card border-left-info shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tanggal Selesai
                            </div>
                            <div class="font-weight-bold text-gray-800">
                                {{ $pesanan->updated_at ? $pesanan->updated_at->format('d M Y') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="font-weight-bold mb-3" style="color:#ef6c00;">
                <i class="fas fa-user mr-2"></i>
                Detail Pemesan
            </h5>

            <table class="table table-bordered mb-4">
                <tr>
                    <th width="220">Nama Akun</th>
                    <td>{{ $pesanan->pengguna?->nama_pengguna ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Nama Penerima</th>
                    <td>{{ $pesanan->nama_penerima ?? '-' }}</td>
                </tr>
                <tr>
                    <th>No HP</th>
                    <td>{{ $pesanan->No_hp_penerima ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td>{{ $pesanan->alamat_penerima ?? '-' }}</td>
                </tr>
            </table>

            <h5 class="font-weight-bold mt-4 mb-3" style="color:#ef6c00;">
                <i class="fas fa-clipboard-list mr-2"></i>
                Detail Pesanan
            </h5>

            <table class="table table-bordered mb-4">
                <tr>
                    <th width="220">Kode Transaksi</th>
                    <td>
                        <span class="badge badge-dark px-3 py-2">
                            {{ $pesanan->kode_resi_pesanan ?? '#' . $pesanan->id_pesanan }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Tanggal Pesan</th>
                    <td>
                        {{ $pesanan->tanggal_pesan ? \Carbon\Carbon::parse($pesanan->tanggal_pesan)->format('d M Y') : '-' }}
                    </td>
                </tr>
                <tr>
                    <th>Tanggal Selesai</th>
                    <td>
                        {{ $pesanan->updated_at ? $pesanan->updated_at->format('d M Y') : '-' }}
                    </td>
                </tr>
                <tr>
                    <th>Status Pesanan</th>
                    <td>
                        @php
                            $status = $pesanan->statusPesanan?->nama_status_pesanan;
                        @endphp

                        @if ($status === 'Selesai')
                            <span class="badge badge-success px-3 py-2 font-weight-bold">
                                <i class="fas fa-check-circle mr-1"></i>
                                {{ $status }}
                            </span>
                        @elseif ($status === 'Dibatalkan')
                            <span class="badge badge-danger px-3 py-2 font-weight-bold">
                                <i class="fas fa-times-circle mr-1"></i>
                                {{ $status }}
                            </span>
                        @else
                            <span class="badge badge-secondary px-3 py-2 font-weight-bold">
                                <i class="fas fa-info-circle mr-1"></i>
                                {{ $status ?? '-' }}
                            </span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Jadwal Pemasangan</th>
                    <td>
                        {{ $pesanan->jadwal_pemasangan ? \Carbon\Carbon::parse($pesanan->jadwal_pemasangan)->format('d M Y') : '-' }}
                    </td>
                </tr>
            </table>

            <h5 class="font-weight-bold mt-4 mb-3" style="color:#ef6c00;">
                <i class="fas fa-box mr-2"></i>
                Rincian Produk
            </h5>

            <div class="table-responsive mt-3 mb-4">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light text-gray-800">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Produk</th>
                            <th>Ukuran</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Kuantitas</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($pesanan->rincianPesanan as $rincian)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $rincian->detailProduk?->itemProduksi?->nama_item ?? '-' }}</td>
                                <td>{{ $rincian->detailProduk?->ukuran ?? '-' }}</td>
                                <td>{{ $rincian->detailProduk?->itemProduksi?->satuanHarga?->nama_satuan ?? '-' }}</td>
                                <td class="text-right">
                                    Rp {{ number_format($rincian->detailProduk?->harga_dasar ?? 0, 0, ',', '.') }}
                                </td>
                                <td>{{ $rincian->kuantitas ?? 1 }}</td>
                                <td class="text-right font-weight-bold text-success">
                                    Rp {{ number_format($rincian->subtotal ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr class="text-center">
                                <td>1</td>
                                <td>{{ $pesanan->detailProduk?->itemProduksi?->nama_item ?? '-' }}</td>
                                <td>{{ $pesanan->detailProduk?->ukuran ?? '-' }}</td>
                                <td>{{ $pesanan->detailProduk?->itemProduksi?->satuanHarga?->nama_satuan ?? '-' }}</td>
                                <td class="text-right">
                                    Rp {{ number_format($pesanan->detailProduk?->harga_dasar ?? 0, 0, ',', '.') }}
                                </td>
                                <td>{{ $pesanan->kuantitas ?? 1 }}</td>
                                <td class="text-right font-weight-bold text-success">
                                    Rp {{ number_format($pesanan->total_harga ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-right">Total Harga</th>
                            <th class="text-right text-success h5 font-weight-bold">
                                Rp {{ number_format($pesanan->total_harga ?? 0, 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <h5 class="font-weight-bold mt-4 mb-3" style="color:#ef6c00;">
                <i class="fas fa-credit-card mr-2"></i>
                Detail Pembayaran
            </h5>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light text-gray-800">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Tipe Pembayaran</th>
                            <th>Jumlah Bayar</th>
                            <th>Status Bayar</th>
                            <th>Order ID</th>
                            <th>Tanggal Bayar</th>
                            <th>Bukti Bayar</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($pesanan->pembayaran as $bayar)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>

                                <td>{{ $bayar->tipe_pembayaran ?? '-' }}</td>

                                <td class="text-right">
                                    Rp {{ number_format($bayar->jumlah_bayar ?? 0, 0, ',', '.') }}
                                </td>

                                <td>
                                    @if ($bayar->status_bayar === 'Lunas')
                                        <span class="badge badge-success px-2 py-1 font-weight-bold">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Lunas
                                        </span>
                                    @elseif ($bayar->status_bayar === 'Pending')
                                        <span class="badge badge-warning px-2 py-1 font-weight-bold text-dark">
                                            <i class="fas fa-clock mr-1"></i>
                                            Pending
                                        </span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1 font-weight-bold">
                                            {{ $bayar->status_bayar ?? '-' }}
                                        </span>
                                    @endif
                                </td>

                                <td>{{ $bayar->order_id ?? '-' }}</td>

                                <td>
                                    {{ $bayar->created_at ? $bayar->created_at->format('d M Y H:i') : '-' }}
                                </td>

                                <td>
                                    @if ($bayar->bukti_bayar)
                                        <a href="{{ route('admin.riwayat-transaksi.bukti-bayar', $pesanan->id_pesanan) }}"
                                           class="btn btn-info btn-sm shadow-sm">
                                            <i class="fas fa-image mr-1"></i>
                                            Lihat Bukti
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Belum ada data pembayaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
@endsection