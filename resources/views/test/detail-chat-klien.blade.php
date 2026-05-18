@extends('klien.layouts.app')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="chat-page">
    <div class="chat-top">
        @php $itemId = $percakapan->id_item_produksi; @endphp
        @if ($itemId)
            <a class="back-link" href="{{ route('pesanan.detail', $itemId) }}">Kembali ke produk</a>
        @endif
        <div class="chat-title">Chat Admin</div>
        <div class="chat-subtitle">
            {{ $percakapan->itemProduksi?->nama_item ?? 'Item' }}
            @if ($percakapan->itemProduksi?->kategoriUsaha?->nama_kategori)
                - {{ $percakapan->itemProduksi?->kategoriUsaha?->nama_kategori }}
            @endif
        </div>
    </div>

    <div class="chat-card">
        <div id="chatMessages" class="chat-messages"></div>

        <form id="chatForm" class="chat-form">
            @csrf
            <textarea id="chatInput" name="isi_pesan" rows="2" placeholder="Ketik pesan..."></textarea>
            <button type="submit" class="btn">Kirim</button>
        </form>
    </div>
</div>

<style>
.chat-page { max-width: 900px; margin: 0 auto; }
.chat-top { margin-bottom: 16px; }
.back-link { display: inline-block; text-decoration: none; color: #006b3c; font-weight: 600; margin-bottom: 8px; }
.chat-title { font-size: 22px; font-weight: 700; }
.chat-subtitle { font-size: 13px; color: #555; margin-top: 4px; }

.chat-card { background: #fff; border: 1px solid #ddd; border-radius: 10px; padding: 18px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
.chat-messages { border: 1px solid #e5e5e5; border-radius: 8px; padding: 12px; height: 420px; overflow-y: auto; background: #fafafa; }

.chat-bubble { background: #ffffff; border: 1px solid #eee; border-radius: 10px; padding: 8px 10px; margin-bottom: 8px; max-width: 80%; }
.chat-bubble.me { background: #e9f6ff; border-color: #d0ecff; margin-left: auto; }
.chat-text { font-size: 14px; }
.chat-time { font-size: 11px; color: #777; margin-top: 4px; text-align: right; }

.chat-form { display: flex; gap: 8px; margin-top: 10px; }
.chat-form textarea { flex: 1; border-radius: 8px; border: 1px solid #ddd; padding: 8px; }

.chat-divider {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 12px 0;
  color: #777;
  font-size: 12px;
}
.chat-divider::before,
.chat-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #ddd;
}
.chat-divider span {
  background: #fafafa;
  padding: 0 8px;
  border-radius: 999px;
}
</style>

<script>
(function () {
    const messagesEl = document.getElementById('chatMessages');
    const form = document.getElementById('chatForm');
    const input = document.getElementById('chatInput');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const messagesUrl = "{{ route('chat.messages', $percakapan->id_percakapan) }}";
    const sendUrl = "{{ route('chat.send', $percakapan->id_percakapan) }}";
    const userId = {{ (int) $userId }};
    let lastId = 0;
    let isLoading = false;
    let dividerRendered = false;

   function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }


    function renderDivider(label) {
        const div = document.createElement('div');
        div.className = 'chat-divider';
        div.innerHTML = `<span>${escapeHtml(label)}</span>`;
        messagesEl.appendChild(div);
    }

    function renderMessage(msg) {
    if (!dividerRendered && msg.show_divider_before) {
        renderDivider('Pesan belum dibaca');
        dividerRendered = true;
    }
    const bubble = document.createElement('div');
    bubble.className = msg.sender_id === userId ? 'chat-bubble me' : 'chat-bubble';
    bubble.innerHTML = `
        <div class="chat-text">${escapeHtml(msg.text)}</div>
        <div class="chat-time">${escapeHtml(msg.created_at)}</div>`;
    messagesEl.appendChild(bubble);
    }

 

    // function renderMessage(msg) {
    //     const bubble = document.createElement('div');
    //     bubble.className = msg.sender_id === userId ? 'chat-bubble me' : 'chat-bubble';
    //     bubble.innerHTML = `
    //         <div class="chat-text">${escapeHtml(msg.text)}</div>
    //         <div class="chat-time">${escapeHtml(msg.created_at)}</div>
    //     `;
    //     messagesEl.appendChild(bubble);
    // }

    async function fetchMessages() {
        if (isLoading) return;
        isLoading = true;

        const url = new URL(messagesUrl, window.location.origin);
        if (lastId > 0) url.searchParams.set('after_id', String(lastId));

        const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
        if (res.ok) {
            const data = await res.json();
            if (Array.isArray(data.messages)) {
                data.messages.forEach(renderMessage);
                if (data.last_id) lastId = data.last_id;
                if (data.messages.length > 0) {
                    messagesEl.scrollTop = messagesEl.scrollHeight;
                }
            }
        }

        isLoading = false;
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const text = input.value.trim();
        if (!text) return;

        const res = await fetch(sendUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ isi_pesan: text })
        });

        if (res.ok) {
            input.value = '';
            await fetchMessages();
        }
    });

    fetchMessages();
    setInterval(fetchMessages, 3000);
})();
</script>
@endsection