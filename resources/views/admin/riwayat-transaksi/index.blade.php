@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-2">
        <div>
            <h1 class="h4 mb-0 text-gray-800 font-weight-bold">
                <i class="fas fa-fw fa-receipt text-warning mr-2"></i> Riwayat Transaksi
            </h1>

            <div class="mt-2 d-flex align-items-center flex-wrap">
                <span class="text-muted small">
                    <i class="fas fa-user-shield mr-1"></i> Area Kerja Admin
                </span>
                <span class="mx-2 text-gray-300">|</span>
                <span class="badge text-white px-2 py-1 font-weight-bold shadow-sm"
                      style="background-color: #ef6c00; font-size: 11px; border-radius: 4px;">
                    <i class="fas fa-tags mr-1"></i>
                    Kategori {{ Auth::user()->kategori->nama_kategori ?? '-' }}
                </span>
                <span class="mx-2 text-gray-300">|</span>
                <p class="text-muted small mb-0">
                    Menampilkan transaksi pesanan yang sudah selesai.
                </p>
            </div>
        </div>

        <div class="d-flex card-shadow-sm">
            <div class="bg-dark text-white px-3 py-2 rounded-left small font-weight-bold d-flex align-items-center">
                <i class="fas fa-check-circle mr-2 text-warning"></i> Total Transaksi
            </div>
            <div class="bg-white border border-left-0 px-3 py-2 rounded-right small font-weight-bold text-gray-800 d-flex align-items-center">
                {{ $riwayatTransaksi->total() }} Data
            </div>
        </div>
    </div>

    <div class="card shadow mb-4" style="border-radius: 8px; overflow: hidden;">
        <div class="card-header py-3 bg-dark d-flex align-items-center justify-content-between"
             style="border-bottom: 2px solid #ef6c00;">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-list mr-2" style="color: #ef6c00;"></i>
                Data Riwayat Transaksi Selesai
            </h6>

            <span class="badge text-white small" style="background-color: #ef6c00; padding: 5px 10px;">
                <i class="fas fa-calendar-alt mr-1"></i> Filter Bulanan
            </span>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.riwayat-transaksi.index') }}" method="GET" class="mb-4">
                <div class="row align-items-end">
                    <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold text-gray-700">Bulan</label>
                        <select name="bulan" class="form-control">
                            @php
                                $namaBulan = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                                    4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                    7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                                    10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                            @endphp

                            @foreach ($namaBulan as $angka => $nama)
                                <option value="{{ $angka }}" {{ (int) $bulan === $angka ? 'selected' : '' }}>
                                    {{ $nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold text-gray-700">Tahun</label>
                        <select name="tahun" class="form-control">
                            @for ($i = now()->year; $i >= now()->year - 5; $i--)
                                <option value="{{ $i }}" {{ (int) $tahun === $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <button type="submit" class="btn text-white font-weight-bold shadow-sm"
                                style="background-color: #ef6c00;">
                            <i class="fas fa-filter mr-1"></i> Tampilkan
                        </button>

                        <a href="{{ route('admin.riwayat-transaksi.index') }}" class="btn btn-secondary font-weight-bold shadow-sm">
                            <i class="fas fa-sync-alt mr-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="bg-light text-gray-800 border-bottom">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Kode Transaksi</th>
                            <th>Tanggal Selesai</th>
                            <th>Nama Pemesan</th>
                            <th>Produk</th>
                            <th>Status Pesanan</th>
                            <th>Total Harga</th>
                            <th width="10%">
                                <i class="fas fa-cog mr-1"></i> Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($riwayatTransaksi as $item)
                            <tr class="text-center align-middle">
                                <td class="font-weight-bold text-gray-800">
                                    {{ $loop->iteration + ($riwayatTransaksi->currentPage() - 1) * $riwayatTransaksi->perPage() }}
                                </td>

                                <td>
                                    <span class="badge badge-dark px-2 py-1">
                                        {{ $item->kode_resi_pesanan ?? '#' . $item->id_pesanan }}
                                    </span>
                                </td>

                                <td>
                                    <i class="far fa-calendar-check text-muted mr-1"></i>
                                    {{ $item->updated_at ? $item->updated_at->format('d M Y') : '-' }}
                                </td>

                                <td>{{ $item->nama_penerima ?? $item->pengguna?->nama_pengguna ?? '-' }}</td>

                                <td>{{ $item->detailProduk?->itemProduksi?->nama_item ?? '-' }}</td>

                                <td>
                                    @php
                                        $status = $item->statusPesanan?->nama_status_pesanan;
                                    @endphp

                                    @if($status === 'Pesanan Selesai')
                                        <span class="badge badge-success px-2 py-1 font-weight-bold">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            {{ $status }}
                                        </span>

                                    @elseif($status === 'Pesanan Dibatalkan')
                                        <span class="badge badge-danger px-2 py-1 font-weight-bold">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            {{ $status }}
                                        </span>

                                    @endif
                                </td>
                                <td class="font-weight-bold text-success text-right">
                                    Rp {{ number_format($item->total_harga ?? 0, 0, ',', '.') }}
                                </td>

                                <td>
                                    <a href="{{ route('admin.riwayat-transaksi.detail', $item->id_pesanan) }}"
                                       class="btn btn-sm text-white shadow-sm"
                                       style="background-color: #ef6c00;">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Tidak ada riwayat transaksi pada bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $riwayatTransaksi->links() }}
            </div>

        </div>
    </div>

</div>
@endsection