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
        <div class="chat-attachment-row">
        <input id="chatAttachment" type="file"
            accept="image/jpeg,image/png,image/gif,image/webp,application/pdf,application/zip,application/x-rar-compressed,application/vnd.rar,image/vnd.adobe.photoshop,application/postscript,application/vnd.adobe.illustrator">
        <div id="attachmentPreview" class="chat-attachment-preview"></div>
    </div>
        <div id="chatError" class="chat-error"></div>
    </div>
</div>

<style>
/* css lampiran file pada chat */
.chat-attachment-row { display: flex; gap: 10px; align-items: center; margin-top: 8px; }
.chat-attachment-preview { font-size: 12px; color: #666; }
.chat-error { font-size: 12px; color: #b42318; margin-top: 6px; }

.chat-attachments { margin-top: 6px; display: grid; gap: 8px; }
.chat-attachment-image { max-width: 260px; border: 1px solid #ddd; border-radius: 8px; }
.chat-attachment-file { display: inline-flex; gap: 6px; align-items: center; padding: 6px 8px; background: #f2f2f2; border-radius: 8px; font-size: 12px; color: #222; text-decoration: none; }
/* end of css lampiran file pada chat */

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
     const fileInput = document.getElementById('chatAttachment');
    const previewEl = document.getElementById('attachmentPreview');
    const errorEl = document.getElementById('chatError');
    const token = document.querySelector('input[name="_token"]').value;

    const messagesUrl = "{{ route('admin.chat.messages', $percakapan->id_percakapan) }}";
    const sendUrl = "{{ route('admin.chat.send', $percakapan->id_percakapan) }}";
    const userId = {{ (int) $userId }};
    let lastId = 0;
    let isLoading = false;
    let dividerRendered = false;

    function escapeHtml(text) {
        return String(text ?? '')
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function formatSize(bytes) {
        if (!bytes) return '0 B';
            const units = ['B', 'KB', 'MB', 'GB'];
            let i = 0;
            let size = bytes;
        while (size >= 1024 && i < units.length - 1) {
            size /= 1024;
            i++;
        }
        return `${size.toFixed(1)} ${units[i]}`;
    }

function renderDivider(label) {
  const div = document.createElement('div');
  div.className = 'chat-divider';
  div.innerHTML = `<span>${escapeHtml(label)}</span>`;
  messagesEl.appendChild(div);
}

    function renderAttachments(attachments, bubble) {
        if (!Array.isArray(attachments) || attachments.length === 0) return;

        const wrap = document.createElement('div');
        wrap.className = 'chat-attachments';

        attachments.forEach(att => {
            if (att.type === 'image' && att.preview_url) {
                const img = document.createElement('img');
                img.src = att.preview_url;
                img.alt = att.name || 'Lampiran gambar';
                img.loading = 'lazy';
                img.className = 'chat-attachment-image';
                wrap.appendChild(img);
                return;
            }

            if (att.download_url) {
                const link = document.createElement('a');
                link.href = att.download_url;
                link.className = 'chat-attachment-file';
                link.textContent = att.name || 'Lampiran file';
                link.setAttribute('download', '');
                wrap.appendChild(link);
            }
        });

        bubble.appendChild(wrap);
    }

    function renderMessage(msg) {
        if (!dividerRendered && msg.show_divider_before) {
            renderDivider('Pesan belum dibaca');
            dividerRendered = true;
        }
        const bubble = document.createElement('div');
        bubble.className = msg.sender_id === userId ? 'chat-bubble me' : 'chat-bubble';

        if (msg.text) {
                const textEl = document.createElement('div');
                textEl.className = 'chat-text';
                textEl.textContent = msg.text;
                bubble.appendChild(textEl);
            }

            renderAttachments(msg.attachments, bubble);

            const timeEl = document.createElement('div');
            timeEl.className = 'chat-time';
            timeEl.textContent = msg.created_at;
            bubble.appendChild(timeEl);
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

    function setError(msg) {
        errorEl.textContent = msg || '';
    }

    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (!file) {
            previewEl.textContent = '';
            return;
        }
        previewEl.textContent = `${file.name} (${formatSize(file.size)})`;
    });

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        setError('');

        const text = input.value.trim();
        const file = fileInput.files[0];

        if (!text && !file) return;

        const formData = new FormData();
        if (text) formData.append('isi_pesan', text);
        if (file) formData.append('lampiran', file);

        const res = await fetch(sendUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                // 'Content-Type': 'application/json'
            },
            body: formData
        });

        if (res.status === 422) {
            const data = await res.json();
            const err = data?.errors?.lampiran?.[0] || data?.errors?.isi_pesan?.[0] || 'Validasi gagal.';
            setError(err);
            return;
        }

        if (res.ok) {
            input.value = '';
            fileInput.value = '';
            previewEl.textContent = '';
            await fetchMessages();
        }
    });

    fetchMessages();
    setInterval(fetchMessages, 3000);
})();
</script>
@endsection