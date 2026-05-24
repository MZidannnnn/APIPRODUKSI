@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-list-alt mr-2"></i>
        {{ $title }}
    </h1>

    <div class="row">
        <div class="col-xl-8 col-lg-10 mx-auto">
            
            <div class="mb-3">
                <a href="{{ route('itemProduksi.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <a href="{{ route('itemProduksi.edit', $itemProduksi->id_item_produksi) }}" class="btn btn-primary btn-sm shadow-sm float-right">
                    <i class="fas fa-edit mr-2"></i> Edit Produk Jasa
                </a>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body p-4">
                    
                    {{-- ==================== 1. TAMPILAN FOTO PRODUK ==================== --}}
                    <div class="row justify-content-center mb-4">
                        <div class="col-md-8">
                            @if($itemProduksi->fotoProduk->count() > 0)
                                <div id="carouselFotoProduk" class="carousel slide border rounded shadow-sm" data-ride="carousel">
                                    <div class="carousel-inner">
                                        @foreach($itemProduksi->fotoProduk as $index => $foto)
                                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                <img src="{{ asset($foto->nama_foto) }}" class="d-block w-100 object-fit-cover" style="height: 350px; object-fit: cover;" alt="Foto {{ $itemProduksi->nama_item }}">
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    @if($itemProduksi->fotoProduk->count() > 1)
                                        <a class="carousel-control-prev" href="#carouselFotoProduk" role="button" data-slide="prev">
                                            <span class="carousel-control-prev-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
                                            <span class="sr-only">Previous</span>
                                        </a>
                                        <a class="carousel-control-next" href="#carouselFotoProduk" role="button" data-slide="next">
                                            <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
                                            <span class="sr-only">Next</span>
                                        </a>
                                    @endif
                                </div>
                            @else
                                <div class="text-center p-5 bg-light border rounded">
                                    <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                    <p class="text-muted m-0">Belum ada foto untuk produk ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr class="sidebar-divider">

                    {{-- ==================== 2. NAMA & INFORMASI PRODUK ==================== --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge badge-info mb-2 py-1 px-2">{{ $itemProduksi->kategoriUsaha->nama_kategori }}</span>
                                <h2 class="font-weight-bold text-gray-800 mb-1">{{ $itemProduksi->nama_item }}</h2>
                                
                                {{-- TANGGAL INPUT DARI CREATED_AT (TAMBAHAN BARU) --}}
                                <p class="text-muted small m-0">
                                    <i class="fas fa-calendar-alt mr-1"></i> 
                                    Didaftarkan pada: <strong>{{ \Carbon\Carbon::parse($itemProduksi->created_at)->translatedFormat('d F Y') }}</strong> 
                                    ({{ \Carbon\Carbon::parse($itemProduksi->created_at)->diffForHumans() }})
                                </p>
                            </div>
                            <div>
                                @if($itemProduksi->status_aktif == 'Aktif')
                                    <span class="badge badge-success py-1 px-3"><i class="fas fa-check-circle mr-1"></i> Aktif</span>
                                @else
                                    <span class="badge badge-danger py-1 px-3"><i class="fas fa-times-circle mr-1"></i> Non-aktif</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <h6 class="font-weight-bold text-primary">Deskripsi :</h6>
                            <p class="text-gray-600 bg-light p-3 rounded border">
                                {!! nl2br(e($itemProduksi->deskripsi_item ?? 'Tidak ada deskripsi untuk produk ini.')) !!}
                            </p>
                        </div>
                    </div>

                    <hr class="sidebar-divider">

                    {{-- ==================== 3. DETAIL UKURAN & HARGA ==================== --}}
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="font-weight-bold text-gray-800 m-0">Daftar Variasi & Harga Dasar</h5>
                            <span class="text-muted">Satuan hitung: <strong>{{ $itemProduksi->satuanHarga->nama_satuan }}</strong></span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th width="8%" class="text-center">No</th>
                                        <th>Ukuran / Variasi</th>
                                        <th width="35%" class="text-right">Harga Dasar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($itemProduksi->detailProduk as $index => $detail)
                                        <tr>
                                            <td class="text-center font-weight-bold text-gray-700">{{ $index + 1 }}</td>
                                            <td>{{ $detail->ukuran ?? 'Semua Ukuran (Standar)' }}</td>
                                            <td class="text-right font-weight-bold text-success">
                                                Rp {{ number_format($detail->harga_dasar, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Belum ada data variasi ukuran.</td>
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