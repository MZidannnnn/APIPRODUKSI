@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-comments text-primary mr-2"></i>Daftar Chat
        </h1>
    </div>

    <div class="card shadow mb-4 border-bottom-primary">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Pesan Masuk Klien</h6>
        </div>
        
        <div class="card-body p-0"> 
            <div class="list-group list-group-flush rounded-bottom">
                
                @forelse ($percakapanList as $p)
                    <a href="{{ route('admin.chat.show', $p->id_percakapan) }}" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3 border-bottom {{ $p->unread_count > 0 ? 'bg-light' : '' }}">
                        
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm mr-3 {{ $p->unread_count > 0 ? 'bg-primary' : 'bg-secondary' }}" style="width: 45px; height: 45px;">
                                <i class="fas fa-user"></i>
                            </div>
                            
                            <div>
                                <h6 class="font-weight-bold text-dark mb-1">
                                    {{ $p->pengguna->nama_pengguna }}
                                    @if ($p->unread_count > 0)
                                        <span class="badge badge-danger ml-1 badge-pill">{{ $p->unread_count }} Baru</span>
                                    @endif
                                </h6>
                                <div class="text-muted small">
                                    <i class="fas fa-box-open mr-1"></i> 
                                    {{ $p->itemProduksi->nama_item ?? 'Pertanyaan Umum' }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-none d-sm-block text-right">
                            <span class="btn btn-sm {{ $p->unread_count > 0 ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3 shadow-sm">
                                Buka Chat <i class="fas fa-chevron-right ml-1 fa-sm"></i>
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="text-center p-5">
                        <div class="mb-3">
                            <i class="fas fa-comment-slash fa-3x text-gray-300"></i>
                        </div>
                        <h5 class="text-gray-500 font-weight-bold">Belum ada percakapan</h5>
                        <p class="text-muted small">Pesan dari klien akan otomatis muncul di sini.</p>
                    </div>
                @endforelse
                
            </div>
        </div>
    </div>

</div>
@endsection