@extends('klien.layouts.app')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('fe-klien/chat-detail.css') }}">
@endpush

@section('content')
<div class="chat-room-page">

    <div class="chat-room-card">

        <div class="chat-room-header">
            <a href="{{ route('chat.index') }}" class="btn-kembali">
                <i class="fas fa-chevron-left"></i>
                Kembali
            </a>

            <div class="chat-product-info">
                <div class="chat-avatar">
                    <i class="fas fa-user"></i>
                </div>

                <div>
                    <h2>Chat Admin</h2>
                    <p>
                        {{ $percakapan->itemProduksi?->nama_item ?? 'Item' }}

                        @if ($percakapan->itemProduksi?->kategoriUsaha?->nama_kategori)
                            • {{ $percakapan->itemProduksi?->kategoriUsaha?->nama_kategori }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div id="chatMessages" class="chat-messages"></div>

        <div class="chat-attach-panel">
            <label id="chatDropzone" class="chat-dropzone">
                <input id="chatAttachment" type="file" multiple
                    accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.zip,.rar,.psd,.ai,.eps">

                <span class="dz-title">
                    <i class="fas fa-paperclip"></i>
                    Tambahkan lampiran
                </span>

                <span class="dz-sub">
                    Klik atau drop file di sini. Maksimal 5 file.
                </span>
            </label>

            <ul id="attachmentList" class="attach-list"></ul>
            <div id="chatError" class="chat-error"></div>
        </div>

        <form id="chatForm" class="chat-form">
            @csrf

            <textarea id="chatInput" name="isi_pesan" rows="1" placeholder="Ketik pesan..."></textarea>

            <button type="submit" class="btn-send">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>

    </div>

    <div id="imageModal" class="img-modal" aria-hidden="true">
        <div class="img-modal-backdrop" data-close></div>

        <div class="img-modal-dialog" role="dialog" aria-modal="true">
            <button type="button" class="img-modal-close" data-close>
                <i class="fas fa-times"></i>
            </button>

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
@endsection

@push('scripts')
<script>
    (function() {
        const messagesEl = document.getElementById('chatMessages');
        const form = document.getElementById('chatForm');
        const input = document.getElementById('chatInput');
        const errorEl = document.getElementById('chatError');
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

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

        const messagesUrl = "{{ route('chat.messages', $percakapan->id_percakapan) }}";
        const sendUrl = "{{ route('chat.send', $percakapan->id_percakapan) }}";
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
@endpush