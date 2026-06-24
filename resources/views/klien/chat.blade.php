@extends('klien.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('fe-klien/chat.css') }}">
@endpush

@section('content')
<div class="chat-list-page">

    <a href="{{ url()->previous() }}" class="btn-kembali">
        <i class="fas fa-chevron-left"></i>
        Kembali
    </a>

    <div class="chat-list-header">
        <h2 class="page-title">Daftar Chat</h2>
        <p class="page-subtitle">Pilih percakapan produk yang ingin kamu buka</p>
    </div>

    <div class="room-list">
        @forelse ($percakapanList as $p)
            @php
                $produk = $p->itemProduksi;
                $foto = $produk?->fotoProduk?->first();
            @endphp

            <a class="room-item {{ $p->unread_count > 0 ? 'has-unread' : '' }}" href="{{ route('chat.show', $p->id_percakapan) }}" data-id="{{ $p->id_percakapan }}">

                <div class="room-avatar">
                    @if ($foto)
                        <img src="{{ asset($foto->nama_foto) }}" alt="{{ $produk->nama_item }}">
                    @else
                        <i class="fas fa-image"></i>
                    @endif
                </div>
 
                <div class="room-main">
                    <div class="room-title">{{ $produk->nama_item ?? '-' }}</div>
                    <div class="room-meta">
                        <i class="fas fa-tag"></i>
                        {{ $produk?->kategoriUsaha?->nama_kategori ?? '-' }}
                    </div>
                </div>

                <div class="room-right">
                    @if ($p->unread_count > 0)
                        <span class="room-badge">{{ $p->unread_count }} Baru</span>
                    @endif

                    <i class="fas fa-chevron-right room-arrow"></i>
                </div>
            </a>
        @empty
            <div class="room-empty">
                <div class="room-empty-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h4>Belum ada chat</h4>
                <p>Mulai percakapan dari halaman produk.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateUnreadPerChat() {
        fetch("{{ route('chat.unread.list') }}")
            .then(response => response.json())
            .then(data => {
                data.forEach(item => {
                    const roomItem = document.querySelector(`.room-item[data-id="${item.id_percakapan}"]`);

                    if (!roomItem) return;

                    const roomRight = roomItem.querySelector('.room-right');
                    let badge = roomItem.querySelector('.room-badge');

                    if (item.unread_count > 0) {
                        roomItem.classList.add('has-unread');

                        if (!badge) {
                            badge = document.createElement('span');
                            badge.classList.add('room-badge');
                            roomRight.prepend(badge);
                        }

                        badge.textContent = item.unread_count + ' Baru';
                    } else {
                        roomItem.classList.remove('has-unread');

                        if (badge) {
                            badge.remove();
                        }
                    }
                });
            })
            .catch(error => console.log(error));
    }

    updateUnreadPerChat();
    setInterval(updateUnreadPerChat, 3000);
</script>
@endpush