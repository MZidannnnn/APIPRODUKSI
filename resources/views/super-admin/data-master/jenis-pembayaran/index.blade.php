@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-fw fa-credit-card mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between">
            <div class="mb-1 mr-2">
                <a href="{{ route('jenisPembayaran.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-2"></i> Tambah Jenis Pembayaran
                </a>
            </div> 
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Nama Jenis Pembayaran</th>
                            <th>
                                <i class="fas fa-cog"></i>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($jenisPembayaran as $item)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>

                                <td>{{ $item->nama_jenis_pembayaran }}</td>

                                <td>
                                    <a href="{{ route('jenisPembayaran.edit', $item->id_jenis_pembayaran) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('jenisPembayaran.destroy', $item->id_jenis_pembayaran) }}" method="POST" class="d-inline">
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