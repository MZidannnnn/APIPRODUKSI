@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Daftar Chat</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            @forelse ($percakapanList as $p)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <strong>{{ $p->pengguna->nama_pengguna }}</strong>
                        <div class="text-muted small">{{ $p->itemProduksi->nama_item ?? '-' }}</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if ($p->unread_count > 0)
                            <span class="badge badge-danger">{{ $p->unread_count }}</span>
                        @endif
                        <a class="btn btn-sm btn-primary" href="{{ route('admin.chat.show', $p->id_percakapan) }}">Buka</a>
                    </div>
                </div>
            @empty
                <div class="text-muted">Belum ada percakapan.</div>
            @endforelse
        </div>
    </div>
@endsection
