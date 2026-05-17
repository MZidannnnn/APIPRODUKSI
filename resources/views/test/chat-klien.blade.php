@extends('klien.layouts.app')

@section('content')
<div class="chat-list-page">
    <h2 class="page-title">Daftar Chat</h2>

    <div class="room-list">
        @forelse ($percakapanList as $p)
            <a class="room-item" href="{{ route('chat.show', $p->id_item_produksi) }}">
                <div class="room-main">
                    <div class="room-title">{{ $p->itemProduksi->nama_item ?? '-' }}</div>
                    <div class="room-meta">{{ $p->itemProduksi->kategoriUsaha->nama_kategori ?? '-' }}</div>
                </div>
                @if ($p->unread_count > 0)
                    <span class="room-badge">{{ $p->unread_count }}</span>
                @endif
            </a>
        @empty
            <div class="room-empty">Belum ada chat.</div>
        @endforelse
    </div>
</div>

<style>
.chat-list-page { max-width: 900px; margin: 0 auto; }
.page-title { font-size: 22px; margin-bottom: 16px; }
.room-list { display: grid; gap: 12px; }
.room-item { display: flex; justify-content: space-between; align-items: center; text-decoration: none; color: #222; border: 1px solid #ddd; border-radius: 10px; padding: 14px 16px; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
.room-title { font-weight: 700; }
.room-meta { font-size: 12px; color: #666; margin-top: 4px; }
.room-badge { background: #008a46; color: #fff; font-size: 12px; min-width: 22px; height: 22px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; padding: 0 6px; }
.room-empty { color: #777; }
</style>
@endsection