@extends('klien.layouts.app')

@section('content')
<div class="chat-wrapper">
    <div class="chat-header">Chat Admin</div>

    <div id="chatMessages" class="chat-messages"></div>

    <form id="chatForm" class="chat-form">
        @csrf
        <textarea id="chatInput" name="isi_pesan" rows="2" placeholder="Ketik pesan..."></textarea>
        <button type="submit">Kirim</button>
    </form>
</div>

<style>
.chat-wrapper { max-width: 720px; margin: 0 auto; }
.chat-header { font-weight: 700; margin-bottom: 12px; }
.chat-messages { border: 1px solid #ddd; border-radius: 8px; padding: 12px; height: 360px; overflow-y: auto; background: #fafafa; }
.chat-bubble { background: #ffffff; border: 1px solid #eee; border-radius: 10px; padding: 8px 10px; margin-bottom: 8px; max-width: 80%; }
.chat-bubble.me { background: #e9f6ff; border-color: #d0ecff; margin-left: auto; }
.chat-meta { font-size: 12px; color: #666; margin-bottom: 4px; }
.chat-form { display: flex; gap: 8px; margin-top: 10px; }
.chat-form textarea { flex: 1; border-radius: 8px; border: 1px solid #ddd; padding: 8px; }
.chat-form button { border: none; border-radius: 8px; padding: 8px 16px; background: #0d6efd; color: #fff; }
</style>

<script>
(function () {
    const messagesEl = document.getElementById('chatMessages');
    const form = document.getElementById('chatForm');
    const input = document.getElementById('chatInput');
    const token = document.querySelector('input[name="_token"]').value;

    const messagesUrl = "{{ route('chat.messages') }}";
    const sendUrl = "{{ route('chat.send') }}";
    const userId = {{ (int) $userId }};
    let lastId = 0;
    let isLoading = false;

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function renderMessage(msg) {
        const bubble = document.createElement('div');
        bubble.className = msg.sender_id === userId ? 'chat-bubble me' : 'chat-bubble';
        bubble.innerHTML = `
            <div class="chat-meta">${escapeHtml(msg.sender_name)} - ${escapeHtml(msg.created_at)}</div>
            <div class="chat-text">${escapeHtml(msg.text)}</div>
        `;
        messagesEl.appendChild(bubble);
    }

    async function fetchMessages() {
        if (isLoading) return;
        isLoading = true;

        const url = new URL(messagesUrl, window.location.origin);
        if (lastId > 0) url.searchParams.set('after_id', String(lastId));

        const res = await fetch(url.toString(), {
            headers: { 'Accept': 'application/json' }
        });

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
                'X-CSRF-TOKEN': token,
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