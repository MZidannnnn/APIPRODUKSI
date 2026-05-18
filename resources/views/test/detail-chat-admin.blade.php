@extends('layouts/app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Chat - {{ $percakapan->pengguna->nama_pengguna }}</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <div id="chatMessages" class="chat-messages mb-3"></div>

        <form id="chatForm" class="d-flex gap-2">
            @csrf
            <textarea id="chatInput" name="isi_pesan" rows="2" class="form-control" placeholder="Ketik pesan..."></textarea>
            <button type="submit" class="btn btn-primary">Kirim</button>
        </form>
    </div>
</div>

<style>
.chat-messages { border: 1px solid #e5e5e5; border-radius: 8px; padding: 12px; height: 360px; overflow-y: auto; background: #fafafa; }
.chat-bubble { background: #fff; border: 1px solid #eee; border-radius: 10px; padding: 8px 10px; margin-bottom: 8px; max-width: 80%; }
.chat-bubble.me { background: #e9f6ff; border-color: #d0ecff; margin-left: auto; }
.chat-meta { font-size: 12px; color: #666; margin-bottom: 4px; }
.chat-text { font-size: 14px; }
.chat-time { font-size: 11px; color: #777; margin-top: 4px; text-align: right; }

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
    const token = document.querySelector('input[name="_token"]').value;

    const messagesUrl = "{{ route('admin.chat.messages', $percakapan->id_percakapan) }}";
    const sendUrl = "{{ route('admin.chat.send', $percakapan->id_percakapan) }}";
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
    //         <div class="chat-time">${escapeHtml(msg.created_at)}</div>`;
    //     messagesEl.appendChild(bubble);
    // }

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