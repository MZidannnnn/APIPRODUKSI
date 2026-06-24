@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 mb-0 text-gray-800 font-weight-bold">
            <i class="fas fa-fw fa-image text-warning mr-2"></i>
            Bukti Pembayaran
        </h1>

        <a href="{{ route('admin.riwayat-transaksi.detail', $pesanan->id_pesanan) }}"
           class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-dark" style="border-bottom: 2px solid #ef6c00;">
            <h6 class="m-0 font-weight-bold text-white">
                Bukti Bayar {{ $pesanan->kode_resi_pesanan ?? '#' . $pesanan->id_pesanan }}
            </h6>
        </div>

        <div class="card-body">
            <div class="row">
                @forelse ($pesanan->pembayaran as $bayar)
                    @if ($bayar->bukti_bayar)
                        @php
                            $ext = strtolower(pathinfo($bayar->bukti_bayar, PATHINFO_EXTENSION));
                        @endphp

                        <div class="col-md-6 mb-4">
                            <div class="card border-left-info shadow-sm h-100">
                                <div class="card-body">
                                    <h6 class="font-weight-bold text-primary">
                                        {{ $bayar->tipe_pembayaran ?? 'Pembayaran' }}
                                    </h6>

                                    <p class="mb-1">
                                        <strong>Jumlah:</strong>
                                        Rp {{ number_format($bayar->jumlah_bayar ?? 0, 0, ',', '.') }}
                                    </p>

                                    <p class="mb-3">
                                        <strong>Status:</strong>
                                        {{ $bayar->status_bayar ?? '-' }}
                                    </p>

                                    @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                                        <img src="{{ route('admin.bukti-bayar.file', $bayar->id_pembayaran) }}"
                                             class="img-fluid img-thumbnail mb-3"
                                             style="max-height: 420px; object-fit: contain;">
                                    @elseif ($ext === 'pdf')
                                        <div class="alert alert-secondary">
                                            <i class="fas fa-file-pdf mr-1"></i>
                                            File bukti berupa PDF.
                                        </div>
                                    @endif

                                    <a href="{{ route('admin.bukti-bayar.file', $bayar->id_pembayaran) }}"
                                       target="_blank"
                                       class="btn btn-sm text-white"
                                       style="background-color: #ef6c00;">
                                        <i class="fas fa-external-link-alt mr-1"></i>
                                        Buka File
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            Belum ada bukti pembayaran yang diunggah.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection