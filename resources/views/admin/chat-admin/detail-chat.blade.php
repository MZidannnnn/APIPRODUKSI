@extends('layouts/app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <a href="{{ route('admin.chat.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i>
            Kembali
        </a>
    </div>

    <div class="card shadow mb-4">

        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary text-center">
                <i class="fas fa-comments mr-1"></i>
                Chat - {{ $percakapan->pengguna->nama_pengguna }}
            </h6>
        </div>

        <div class="card-body bg-light" id="chatMessages" style="height: 430px; overflow-y: auto;">
            {{-- Pesan akan dimuat lewat JavaScript --}}
        </div>

        <div class="card-footer bg-white">

            <form id="chatForm">
                @csrf

                <div class="input-group">
                    <textarea id="chatInput"
                              name="isi_pesan"
                              rows="2"
                              class="form-control"
                              placeholder="Ketik pesan..."></textarea>

                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane mr-1"></i>
                            Kirim
                        </button>
                    </div>
                </div>

                <div class="mt-3">
                    <label id="chatDropzone" class="btn btn-outline-secondary btn-sm mb-2">
                        <i class="fas fa-paperclip mr-1"></i>
                        Pilih Lampiran
                        <input id="chatAttachment"
                               type="file"
                               multiple
                               class="d-none"
                               accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.zip,.rar,.psd,.ai,.eps">
                    </label>

                    <small class="text-muted d-block">
                        Maksimal 5 file, total 100 MB. Format: JPG, PNG, GIF, WebP, PDF, ZIP, RAR, PSD, AI, EPS.
                    </small>

                    <ul id="attachmentList" class="list-group mt-2"></ul>

                    <div id="chatError" class="text-danger small mt-2"></div>
                </div>
            </form>

        </div>
    </div>

</div>

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h6 class="modal-title font-weight-bold text-primary">
                    Preview Lampiran
                </h6>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body text-center bg-light">
                <img id="imagePreview" src="" alt="Preview lampiran" class="img-fluid rounded">
            </div>

            <div class="modal-footer">
                <button type="button" id="zoomOut" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-search-minus"></i>
                </button>

                <button type="button" id="zoomReset" class="btn btn-sm btn-outline-secondary">
                    Reset
                </button>

                <button type="button" id="zoomIn" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-search-plus"></i>
                </button>

                <a id="imageDownload" href="#" download class="btn btn-sm btn-primary">
                    <i class="fas fa-download mr-1"></i>
                    Download
                </a>
            </div>

        </div>
    </div>
</div>

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

    const imageModal = document.getElementById('imageModal');
    const imagePreview = document.getElementById('imagePreview');
    const imageDownload = document.getElementById('imageDownload');
    const zoomInBtn = document.getElementById('zoomIn');
    const zoomOutBtn = document.getElementById('zoomOut');
    const zoomResetBtn = document.getElementById('zoomReset');

    const maxFiles = 5;
    const maxBytes = 100 * 1024 * 1024;
    const allowedExt = ['jpg','jpeg','png','gif','webp','pdf','zip','rar','psd','ai','eps'];

    const messagesUrl = "{{ route('admin.chat.messages', $percakapan->id_percakapan) }}";
    const sendUrl = "{{ route('admin.chat.send', $percakapan->id_percakapan) }}";
    const userId = {{ (int) $userId }};

    let zoom = 1;
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

    function setError(message) {
        errorEl.textContent = message || '';
    }

    function formatSize(bytes) {
        if (!bytes) return '0 B';

        const units = ['B', 'KB', 'MB', 'GB'];
        let size = bytes;
        let index = 0;

        while (size >= 1024 && index < units.length - 1) {
            size /= 1024;
            index++;
        }

        return `${size.toFixed(1)} ${units[index]}`;
    }

    function isAllowed(file) {
        const ext = (file.name.split('.').pop() || '').toLowerCase();
        return allowedExt.includes(ext);
    }

    function applyZoom() {
        imagePreview.style.transform = `scale(${zoom})`;
    }

    function openImageModal(attachment) {
        zoom = 1;
        imagePreview.src = attachment.preview_url;
        imageDownload.href = attachment.download_url || '#';
        applyZoom();

        $('#imageModal').modal('show');
    }

    function closeImageModal() {
        $('#imageModal').modal('hide');
        imagePreview.src = '';
    }

    function renderDivider(label) {
        const divider = document.createElement('div');
        divider.className = 'text-center my-3';

        divider.innerHTML = `
            <span class="badge badge-secondary px-3 py-2">
                ${escapeHtml(label)}
            </span>
        `;

        messagesEl.appendChild(divider);
    }

    function renderList() {
        listEl.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const item = document.createElement('li');
            item.className = 'list-group-item d-flex justify-content-between align-items-center py-2';

            item.innerHTML = `
                <div>
                    <div class="small font-weight-bold text-gray-800">
                        ${escapeHtml(file.name)}
                    </div>
                    <div class="small text-muted">
                        ${formatSize(file.size)} • ${escapeHtml(file.type || 'unknown')}
                    </div>
                </div>

                <button type="button" class="btn btn-sm btn-danger">
                    Hapus
                </button>
            `;

            item.querySelector('button').addEventListener('click', () => {
                selectedFiles.splice(index, 1);
                renderList();
                setError('');
            });

            listEl.appendChild(item);
        });
    }

    function addFiles(files) {
        const incomingFiles = Array.from(files || []);

        if (incomingFiles.length === 0) return;

        incomingFiles.forEach(file => {
            if (!isAllowed(file)) {
                setError('Ekstensi file tidak didukung.');
                return;
            }

            selectedFiles.push(file);
        });

        if (selectedFiles.length > maxFiles) {
            selectedFiles = selectedFiles.slice(0, maxFiles);
            setError('Maksimal 5 lampiran per pengiriman.');
        }

        const totalSize = selectedFiles.reduce((total, file) => total + (file.size || 0), 0);

        if (totalSize > maxBytes) {
            setError('Total ukuran lampiran maksimal 100 MB per pengiriman.');
        }

        renderList();
        fileInput.value = '';
    }

    function renderAttachments(attachments, bubble) {
        if (!Array.isArray(attachments) || attachments.length === 0) return;

        const wrapper = document.createElement('div');
        wrapper.className = 'mt-2';

        attachments.forEach(attachment => {
            if (attachment.type === 'image' && attachment.preview_url) {
                const image = document.createElement('img');
                image.src = attachment.preview_url;
                image.alt = attachment.name || 'Lampiran gambar';
                image.loading = 'lazy';
                image.className = 'img-fluid rounded border mb-2 d-block';
                image.style.maxWidth = '240px';
                image.style.cursor = 'pointer';

                image.addEventListener('click', () => openImageModal(attachment));

                wrapper.appendChild(image);
                return;
            }

            if (attachment.download_url) {
                const link = document.createElement('a');
                link.href = attachment.download_url;
                link.className = 'btn btn-sm btn-light border mb-2 d-inline-block';
                link.setAttribute('download', '');
                link.innerHTML = `
                    <i class="fas fa-file-download mr-1"></i>
                    ${escapeHtml(attachment.name || 'Lampiran file')}
                `;

                wrapper.appendChild(link);
            }
        });

        bubble.appendChild(wrapper);
    }

    function renderMessage(message) {
        if (!dividerRendered && message.show_divider_before) {
            renderDivider('Pesan belum dibaca');
            dividerRendered = true;
        }

        const isMine = message.sender_id === userId;

        const row = document.createElement('div');
        row.className = `d-flex mb-3 ${isMine ? 'justify-content-end' : 'justify-content-start'}`;

        const bubble = document.createElement('div');
        bubble.className = `p-3 rounded shadow-sm ${isMine ? 'bg-primary text-white' : 'bg-white text-gray-900 border'}`;
        bubble.style.maxWidth = '75%';

        if (message.text) {
            const text = document.createElement('div');
            text.className = 'font-weight-normal';
            text.textContent = message.text;
            bubble.appendChild(text);
        }

        renderAttachments(message.attachments, bubble);

        const time = document.createElement('div');
        time.className = `mt-1 ${isMine ? 'text-white-50' : 'text-muted'}`;
        time.style.fontSize = '11px';
        time.textContent = message.created_at;

        bubble.appendChild(time);
        row.appendChild(bubble);
        messagesEl.appendChild(row);
    }

    async function fetchMessages() {
        if (isLoading) return;

        isLoading = true;

        const url = new URL(messagesUrl, window.location.origin);

        if (lastId > 0) {
            url.searchParams.set('after_id', String(lastId));
        }

        const response = await fetch(url.toString(), {
            headers: {
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();

            if (Array.isArray(data.messages)) {
                data.messages.forEach(renderMessage);

                if (data.last_id) {
                    lastId = data.last_id;
                }

                if (data.messages.length > 0) {
                    messagesEl.scrollTop = messagesEl.scrollHeight;
                }
            }
        }

        isLoading = false;
    }

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

    dropzone.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropzone.classList.add('btn-primary');
        dropzone.classList.remove('btn-outline-secondary');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('btn-primary');
        dropzone.classList.add('btn-outline-secondary');
    });

    dropzone.addEventListener('drop', (event) => {
        event.preventDefault();

        dropzone.classList.remove('btn-primary');
        dropzone.classList.add('btn-outline-secondary');

        addFiles(event.dataTransfer.files);
    });

    fileInput.addEventListener('change', () => {
        setError('');
        addFiles(fileInput.files);
    });

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        setError('');

        const text = input.value.trim();

        if (!text && selectedFiles.length === 0) return;

        const totalSize = selectedFiles.reduce((total, file) => total + (file.size || 0), 0);

        if (selectedFiles.length > maxFiles) {
            setError('Maksimal 5 lampiran per pengiriman.');
            return;
        }

        if (totalSize > maxBytes) {
            setError('Total ukuran lampiran maksimal 100 MB per pengiriman.');
            return;
        }

        const formData = new FormData();

        if (text) {
            formData.append('isi_pesan', text);
        }

        selectedFiles.forEach(file => {
            formData.append('lampiran[]', file);
        });

        const response = await fetch(sendUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: formData
        });

        if (response.status === 422) {
            const data = await response.json();

            const errorMessage =
                data?.errors?.lampiran?.[0] ||
                data?.errors?.isi_pesan?.[0] ||
                'Validasi gagal.';

            setError(errorMessage);
            return;
        }

        if (response.status === 419) {
            setError('Sesi berakhir. Silakan refresh halaman.');
            return;
        }

        if (response.ok) {
            input.value = '';
            selectedFiles = [];
            renderList();
            closeImageModal();

            await fetchMessages();
        }
    });

    fetchMessages();
    setInterval(fetchMessages, 3000);
})();
</script>
@endsection