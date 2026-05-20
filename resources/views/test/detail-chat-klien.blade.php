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
        <div class="chat-attachment-row">
            <div>
                <label>Gambar & PDF (multi)</label><br>
                <input id="chatAttachmentSafe" type="file" multiple
                accept=".jpg,.jpeg,.png,.gif,.webp,.pdf">
            </div>
            <div>
                <label>File proyek (single)</label><br>
                <input id="chatAttachmentProject" type="file"
                accept=".zip,.rar,.psd,.ai,.eps">
            </div>
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
    // const fileInput = document.getElementById('chatAttachment');
    const previewEl = document.getElementById('attachmentPreview');
    const errorEl = document.getElementById('chatError');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const messagesUrl = "{{ route('chat.messages', $percakapan->id_percakapan) }}";
    const sendUrl = "{{ route('chat.send', $percakapan->id_percakapan) }}";
    const userId = {{ (int) $userId }};

    // validasi lampiran file max 5 sekaligus dan max total 100 mb
    const safeInput = document.getElementById('chatAttachmentSafe');
    const projectInput = document.getElementById('chatAttachmentProject');
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

    function collectFiles() {
        const safeFiles = Array.from(safeInput.files || []);
        const projectFiles = Array.from(projectInput.files || []);

        const all = [...safeFiles, ...projectFiles];
        return { all, safeFiles, projectFiles };
    }

    function updatePreview() {
        const { all } = collectFiles();
        if (all.length === 0) {
            previewEl.textContent = '';
            return;
        }
        const total = all.reduce((sum, f) => sum + (f.size || 0), 0);
        previewEl.textContent = `${all.length} file, total ${formatSize(total)}`;
    }

    function validateClientSide() {
        const { all, projectFiles } = collectFiles();

        if (all.length > 5) {
            setError('Maksimal 5 lampiran per pengiriman.');
            return false;
        }

        const total = all.reduce((sum, f) => sum + (f.size || 0), 0);
        if (total > 100 * 1024 * 1024) {
            setError('Total ukuran lampiran maksimal 100 MB per pengiriman.');
            return false;
        }

        if (projectFiles.length > 1 || (projectFiles.length === 1 && all.length > 1)) {
            setError('File proyek hanya boleh 1 per pengiriman.');
            return false;
        }

        return true;
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

    function setError(msg) {
        errorEl.textContent = msg || '';
    }

    // fileInput.addEventListener('change', () => {
    //     const file = fileInput.files[0];
    //     if (!file) {
    //         previewEl.textContent = '';
    //         return;
    //     }
    //     previewEl.textContent = `${file.name} (${formatSize(file.size)})`;
    // });

    safeInput.addEventListener('change', () => {
        setError('');
        updatePreview();
    });

    projectInput.addEventListener('change', () => {
        setError('');
        updatePreview();
    });

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        setError('');

        const text = input.value.trim();
        const { all } = collectFiles();

        if (!text && all.length === 0) return;
        if (!validateClientSide()) return;

        const formData = new FormData();
        if (text) formData.append('isi_pesan', text);
        all.forEach(file => formData.append('lampiran[]', file));

        const res = await fetch(sendUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
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
            safeInput.value = '';
            projectInput.value = '';
            previewEl.textContent = '';
            await fetchMessages();
        }

        if (res.status === 419) {
            setError('Sesi berakhir. Silakan refresh halaman.');
            return;
        }
    });

    fetchMessages();
    setInterval(fetchMessages, 3000);
})();
</script>
@endsection