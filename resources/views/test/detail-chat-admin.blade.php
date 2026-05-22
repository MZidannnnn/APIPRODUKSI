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
        <div class="chat-attach-panel">
            <label id="chatDropzone" class="chat-dropzone">
                <input id="chatAttachment" type="file" multiple
                    accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.zip,.rar,.psd,.ai,.eps">
                <span class="dz-title">Drop file di sini atau klik untuk memilih</span>
                <span class="dz-sub">Maks 5 file, total 100 MB per pengiriman</span>
                <span class="dz-hint">Format: JPG, PNG, GIF, WebP, PDF, ZIP, RAR, PSD, AI, EPS</span>
            </label>

            <ul id="attachmentList" class="attach-list"></ul>
            <div id="chatError" class="chat-error"></div>
        </div>
        <div id="imageModal" class="img-modal" aria-hidden="true">
            <div class="img-modal-backdrop" data-close></div>
            <div class="img-modal-dialog" role="dialog" aria-modal="true" aria-label="Preview gambar">
                <button type="button" class="img-modal-close" data-close>X</button>
                <div class="img-modal-toolbar">
                    <button type="button" id="zoomOut">-</button>
                    <button type="button" id="zoomReset">Reset</button>
                    <button type="button" id="zoomIn">+</button>
                    <a id="imageDownload" class="img-modal-download" href="#" download>Download</a>
                </div>
                <div class="img-modal-body">
                     <img id="imagePreview" alt="Preview lampiran">
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* css lampiran file pada chat */
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

/* css lampiran terbaru */
:root {
  --chat-bg: #f6f2ea;
  --chat-ink: #1f2a2e;
  --chat-accent: #1b7f6e;
  --chat-accent-2: #ffb74d;
  --chat-border: #d9d4c8;
}

.chat-card, .card-body {
  background: linear-gradient(180deg, #fffaf0 0%, #f7f1e7 100%);
}

.chat-attach-panel { margin-top: 10px; }

.chat-dropzone {
  position: relative;
  display: grid;
  gap: 6px;
  padding: 14px 16px;
  border: 2px dashed var(--chat-border);
  border-radius: 14px;
  background:
    radial-gradient(80% 120% at 10% 0%, rgba(27,127,110,0.08), transparent 60%),
    linear-gradient(180deg, #fff 0%, #fff7e9 100%);
  color: var(--chat-ink);
  cursor: pointer;
  transition: border-color 120ms ease, box-shadow 120ms ease, transform 120ms ease;
}

.chat-dropzone input[type="file"] {
  position: absolute;
  inset: 0;
  opacity: 0;
  cursor: pointer;
}

.chat-dropzone.is-dragover {
  border-color: var(--chat-accent);
  box-shadow: 0 0 0 4px rgba(27,127,110,0.12);
  transform: translateY(-1px);
}

.dz-title {
  font-size: 15px;
  font-weight: 700;
}

.dz-sub {
  font-size: 12px;
  color: #5b6b6e;
}

.dz-hint {
  font-size: 11px;
  color: #7a7a7a;
}

.attach-list {
  list-style: none;
  padding: 0;
  margin: 10px 0 0;
  display: grid;
  gap: 8px;
}

.attach-item {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 8px;
  align-items: center;
  padding: 8px 10px;
  border: 1px solid #e6e0d5;
  border-radius: 10px;
  background: #fff;
}

.attach-meta {
  font-size: 12px;
  color: #4b5a5d;
}

.attach-name {
  font-size: 13px;
  font-weight: 600;
}

.attach-remove {
  border: 1px solid #e2b4a6;
  color: #a3381a;
  background: #fff0ea;
  padding: 4px 8px;
  border-radius: 8px;
  cursor: pointer;
}
/* batas css lampiran terbaru */

/* css untuk modal download gambar */
.img-modal {
  position: fixed;
  inset: 0;
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.img-modal.is-open { display: flex; }

.img-modal-backdrop {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0.55);
}

.img-modal-dialog {
  position: relative;
  width: min(92vw, 960px);
  max-height: 90vh;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  display: grid;
  grid-template-rows: auto 1fr;
  z-index: 1;
}

.img-modal-toolbar {
  display: flex;
  gap: 8px;
  align-items: center;
  padding: 10px 12px;
  border-bottom: 1px solid #eee;
}

.img-modal-toolbar button,
.img-modal-download {
  border: 1px solid #ddd;
  background: #fff;
  padding: 6px 10px;
  border-radius: 8px;
  cursor: pointer;
  text-decoration: none;
  color: #222;
  font-size: 12px;
}

.img-modal-body {
  display: grid;
  place-items: center;
  background: #fafafa;
  overflow: auto;
}

.img-modal-body img {
  max-width: 100%;
  max-height: 75vh;
  transform-origin: center center;
  transition: transform 120ms ease;
  cursor: zoom-in;
}

.img-modal-close {
  position: absolute;
  right: 10px;
  top: 8px;
  border: none;
  background: transparent;
  font-size: 14px;
  cursor: pointer;
}
/* end css modal gambar */
</style>

<script>
(function () {
    const messagesEl = document.getElementById('chatMessages');
    const form = document.getElementById('chatForm');
    const input = document.getElementById('chatInput');
    const errorEl = document.getElementById('chatError');
    const token = document.querySelector('input[name="_token"]').value;

    const dropzone = document.getElementById('chatDropzone');
    const fileInput = document.getElementById('chatAttachment');
    const listEl = document.getElementById('attachmentList');

    // modal zoom gambar
    const imageModal = document.getElementById('imageModal');
    const imagePreview = document.getElementById('imagePreview');
    const imageDownload = document.getElementById('imageDownload');
    const zoomInBtn = document.getElementById('zoomIn');
    const zoomOutBtn = document.getElementById('zoomOut');
    const zoomResetBtn = document.getElementById('zoomReset');
    // end modal zoom gambar

    const maxFiles = 5;
    const maxBytes = 100 * 1024 * 1024;
    const allowedExt = ['jpg','jpeg','png','gif','webp','pdf','zip','rar','psd','ai','eps'];

    const messagesUrl = "{{ route('admin.chat.messages', $percakapan->id_percakapan) }}";
    const sendUrl = "{{ route('admin.chat.send', $percakapan->id_percakapan) }}";
    const userId = {{ (int) $userId }};

    // zoom gambar fitur chat
    let zoom = 1;
    // end zoom gambar fitur chat
    let lastId = 0;
    let isLoading = false;
    let dividerRendered = false;
    let selectedFiles = [];

    function escapeHtml(text) {
        return String(text ?? '')
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // js untuk zoom gambar pada ruang chat
    function applyZoom() {
        imagePreview.style.transform = `scale(${zoom})`;
    }

    function openImageModal(att) {
        zoom = 1;
        imagePreview.src = att.preview_url;
        imageDownload.href = att.download_url || '#';
        applyZoom();
        imageModal.classList.add('is-open');
        imageModal.setAttribute('aria-hidden', 'false');
    }

    function closeImageModal() {
        imageModal.classList.remove('is-open');
        imageModal.setAttribute('aria-hidden', 'true');
        imagePreview.src = '';
    }
    //end js untuk zoom gambar pada ruang chat

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

    function isAllowed(file) {
        const ext = (file.name.split('.').pop() || '').toLowerCase();
        return allowedExt.includes(ext);
    }

    function renderDivider(label) {
        const div = document.createElement('div');
        div.className = 'chat-divider';
        div.innerHTML = `<span>${escapeHtml(label)}</span>`;
        messagesEl.appendChild(div);
    }

    function renderList() {
        listEl.innerHTML = '';
        selectedFiles.forEach((file, idx) => {
            const li = document.createElement('li');
            li.className = 'attach-item';

            const meta = document.createElement('div');
            meta.innerHTML = `
            <div class="attach-name"></div>
            <div class="attach-meta"></div>
            `;
            meta.querySelector('.attach-name').textContent = file.name;
            meta.querySelector('.attach-meta').textContent = `${formatSize(file.size)} • ${file.type || 'unknown'}`;

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'attach-remove';
            btn.textContent = 'Hapus';
            btn.addEventListener('click', () => {
                selectedFiles.splice(idx, 1);
                renderList();
                setError('');
            });

            li.appendChild(meta);
            li.appendChild(btn);
            listEl.appendChild(li);
        });
    }

    function addFiles(files) {
        const incoming = Array.from(files || []);
        if (incoming.length === 0) return;

        for (const f of incoming) {
            if (!isAllowed(f)) {
                setError('Ekstensi file tidak didukung.');
                continue;
            }
            selectedFiles.push(f);
        }
        if (selectedFiles.length > maxFiles) {
            selectedFiles = selectedFiles.slice(0, maxFiles);
            setError('Maksimal 5 lampiran per pengiriman.');
        }

        const total = selectedFiles.reduce((s, f) => s + (f.size || 0), 0);
        if (total > maxBytes) {
            setError('Total ukuran lampiran maksimal 100 MB per pengiriman.');
        }
        renderList();
        fileInput.value = '';
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
                img.style.cursor = 'zoom-in';
                img.addEventListener('click', () => openImageModal(att));
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

    // js untuk zoom gambar pada ruang chat
    zoomInBtn.addEventListener('click', () => {
        zoom = Math.min(4, zoom + 0.2);
        applyZoom();
    });

    zoomOutBtn.addEventListener('click', () => {
        zoom = Math.max(1, zoom - 0.2);
        applyZoom();
    });

    zoomResetBtn.addEventListener('click', () => {
        zoom = 1;
        applyZoom();
    });

    imageModal.addEventListener('click', (e) => {
        if (e.target.hasAttribute('data-close')) {
            closeImageModal();
        }
    });
    //  end js untuk zoom gambar pada ruang chat

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('is-dragover');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('is-dragover');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('is-dragover');
        addFiles(e.dataTransfer.files);
    });

    fileInput.addEventListener('change', () => {
        setError('');
        addFiles(fileInput.files);
    });

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        setError('');

        const text = input.value.trim();

        if (!text && selectedFiles.length === 0) return;

            const total = selectedFiles.reduce((s, f) => s + (f.size || 0), 0);
        if (selectedFiles.length > maxFiles) {
            setError('Maksimal 5 lampiran per pengiriman.');
            return;
        }
        if (total > maxBytes) {
            setError('Total ukuran lampiran maksimal 100 MB per pengiriman.');
            return;
        }

        const formData = new FormData();
        if (text) formData.append('isi_pesan', text);
        selectedFiles.forEach(f => formData.append('lampiran[]', f));

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
            selectedFiles = [];
            closeImageModal();
            renderList();
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