@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-fw fa-box mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between">
            <div class="mb-1 mr-2">
                <a href="{{ route('admin.itemProduksi.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-2"></i> Tambah Data Produk
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Tanggal Produk Dibuat</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Status Produk</th>
                            <th>Satuan Harga</th>
                            <th>Deskripsi</th>
                            <th width="15%">
                                <i class="fas fa-cog"></i>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($itemProduksi as $item)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d-m-Y') }}
                                    <small class="text-muted d-block">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</small>
                                </td>

                                <td>{{ $item->nama_item }}</td>

                                <td>
                                {{-- Relasi Kategori usaha --}}
                                {{ $item->kategoriUsaha->nama_kategori}}
                                </td>

                                <td>
                                    @if ($item->status_aktif == 'Aktif')
                                        <span class="badge badge-success">
                                            {{ $item->status_aktif }}
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            {{ $item->status_aktif }}
                                        </span>
                                    @endif
                                </td>
                                
                                <td>
                                    {{-- Satuan Harga --}}
                                    {{ $item->satuanHarga->nama_satuan}}
                                </td>

                                <td>{{ $item->deskripsi_item ?? '-' }}</td>

                                <td>
                                    <a href="{{ route('admin.itemProduksi.show', $item->id_item_produksi) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.itemProduksi.edit', $item->id_item_produksi) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.itemProduksi.destroy', $item->id_item_produksi) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm btn-hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>
@endsection