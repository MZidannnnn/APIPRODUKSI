@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-fw fa-tags mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between">
            <div class="mb-1 mr-2">
                <a href="{{ route('kategoriUsaha.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-2"></i> Tambah Kategori Usaha
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Nama Kategori</th>
                            <th>Kode Unik Pesanan</th>
                            <th>Bidang Layanan</th>
                            <th>Jenis Pembayaran</th>
                            <th width="15%">
                                <i class="fas fa-cog"></i>
                            </th>
                        </tr> 
                    </thead> 

                    <tbody>
                        @foreach ($kategori as $item)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>

                                <td>{{ $item->nama_kategori }}</td>

                                <td>{{ $item->kode_unik }}</td>

                                <td class="text-center">
                                    @if ($item->bidang_layanan == 'Media Promosi')
                                        <span class="badge badge-success">
                                            {{ $item->bidang_layanan }}
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            {{ $item->bidang_layanan }}
                                        </span>
                                    @endif
                                </td>
                                
                                {{-- Relasi jenis pembayaran --}}
                                <td>{{ $item->jenisPembayaran->nama_jenis_pembayaran ?? '-' }}</td>

                                <td>
                                    <a href="{{ route('kategoriUsaha.edit', $item->id_kategori) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('kategoriUsaha.destroy', $item->id_kategori) }}" method="POST" class="d-inline">
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