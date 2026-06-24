@extends('layouts/app')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4 mt-2">
    <div>
        <h1 class="h4 mb-0 text-gray-800 font-weight-bold">
            <i class="fas fa-fw fa-edit text-warning mr-2"></i> {{ $title }}
        </h1>

        <p class="text-muted small mt-2 mb-0">
            Periksa detail pesanan dan perbarui status progres pesanan.
        </p>
    </div>

    <a href="{{ route('admin.tampilPesanan') }}" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Terjadi kesalahan!</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.progres-pesanan.update', $pesanan->id_pesanan) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">

        <div class="col-lg-8">

            <div class="card shadow mb-4" style="border-radius: 8px; overflow: hidden;">
                <div class="card-header py-3 bg-dark" style="border-bottom: 2px solid #ef6c00;">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-receipt mr-2" style="color: #ef6c00;"></i>
                        Detail Pesanan {{ $pesanan->kode_resi_pesanan ?? '#' . $pesanan->id_pesanan }}
                    </h6>
                </div>

                <div class="card-body">

                    <h6 class="font-weight-bold text-primary mb-3">
                        <i class="fas fa-user mr-1"></i> Data Pemesan
                    </h6>

                    <table class="table table-bordered">
                        <tr>
                            <th width="220">Kode Transaksi</th>
                            <td>
                                <span class="badge badge-dark px-3 py-2">
                                    {{ $pesanan->kode_resi_pesanan ?? '#' . $pesanan->id_pesanan }}
                                </span>
                            </td>
                        </tr>
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
                            <th>Alamat Penerima</th>
                            <td>{{ $pesanan->alamat_penerima ?? '-' }}</td>
                        </tr>
                    </table>

                    <h6 class="font-weight-bold text-primary mt-4 mb-3">
                        <i class="fas fa-box mr-1"></i> Data Produk
                    </h6>

                    <table class="table table-bordered">
                        <tr>
                            <th width="220">Nama Produk</th>
                            <td>{{ $pesanan->detailProduk?->itemProduksi?->nama_item ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>
                                <span class="badge badge-secondary px-2 py-1">
                                    {{ $pesanan->detailProduk?->itemProduksi?->kategoriUsaha?->nama_kategori ?? '-' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Ukuran</th>
                            <td>{{ $pesanan->detailProduk?->ukuran ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Harga Dasar</th>
                            <td>
                                Rp {{ number_format($pesanan->detailProduk?->harga_dasar ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>

                    <h6 class="font-weight-bold text-primary mt-4 mb-3">
                        <i class="fas fa-list mr-1"></i> Rincian Pesanan
                    </h6>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th>Ukuran</th>
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
                                        <td class="text-right">
                                            Rp {{ number_format($rincian->detailProduk?->harga_dasar ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td>{{ $rincian->kuantitas ?? 0 }}</td>
                                        <td class="text-right font-weight-bold text-success">
                                            Rp {{ number_format($rincian->subtotal ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            Rincian pesanan belum tersedia.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-right">Total Harga</th>
                                    <th class="text-right text-success">
                                        Rp {{ number_format($pesanan->total_harga ?? 0, 0, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <h6 class="font-weight-bold text-primary mt-4 mb-3">
                        <i class="fas fa-credit-card mr-1"></i> Detail Pembayaran
                    </h6>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Tipe</th>
                                    <th>Jumlah Bayar</th>
                                    <th>Status Bayar</th>
                                    <th>Order ID</th>
                                    <th>Tanggal</th>
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
                                                <span class="badge badge-success">Lunas</span>
                                            @elseif ($bayar->status_bayar === 'Pending')
                                                <span class="badge badge-warning text-dark">Pending</span>
                                            @else
                                                <span class="badge badge-secondary">
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
                                                    <i class="fas fa-image mr-1"></i> Lihat Bukti
                                                </a>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-times-circle mr-1"></i> Belum Ada
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            Data pembayaran belum tersedia.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>

        <div class="col-lg-4">

            <div class="card shadow mb-4" style="border-radius: 8px; overflow: hidden;">
                <div class="card-header py-3 bg-dark" style="border-bottom: 2px solid #ef6c00;">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-tasks mr-2" style="color: #ef6c00;"></i>
                        Update Progres
                    </h6>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="font-weight-bold">Status Saat Ini</label>
                        <div>
                            <span class="badge badge-warning text-dark px-3 py-2">
                                {{ $pesanan->statusPesanan?->nama_status_pesanan ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="id_status_pesanan" class="font-weight-bold">
                            Ubah Status Pesanan
                        </label>

                        <select name="id_status_pesanan"
                                id="id_status_pesanan"
                                class="form-control @error('id_status_pesanan') is-invalid @enderror"
                                required>
                            <option value="">-- Pilih Status --</option>

                            @foreach ($statusPesanan as $status)
                                <option value="{{ $status->id_status_pesanan }}"
                                    {{ old('id_status_pesanan', $pesanan->id_status_pesanan) == $status->id_status_pesanan ? 'selected' : '' }}>
                                    {{ $status->nama_status_pesanan }}
                                </option>
                            @endforeach
                        </select>

                        @error('id_status_pesanan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <div class="small text-muted mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Admin hanya dapat mengubah status pesanan sesuai kategori yang dikelola.
                    </div>

                    <button type="submit" class="btn btn-warning btn-block font-weight-bold shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>

            <div class="card shadow mb-4 border-left-warning">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        Tanggal Pesan
                    </div>
                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                        {{ $pesanan->tanggal_pesan ? \Carbon\Carbon::parse($pesanan->tanggal_pesan)->format('d M Y') : '-' }}
                    </div>

                    <hr>

                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        Jadwal Pemasangan
                    </div>
                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                        {{ $pesanan->jadwal_pemasangan ? \Carbon\Carbon::parse($pesanan->jadwal_pemasangan)->format('d M Y') : '-' }}
                    </div>
                </div>
            </div>

        </div>

    </div>
</form>
@endsection