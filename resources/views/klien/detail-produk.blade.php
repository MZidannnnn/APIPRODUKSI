@extends('klien.layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('fe-klien/detail-produk.css') }}?v={{ time() }}">
@endpush

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
@endpush

@section('content')
<div class="detail-produk-wrapper">

    @php
        $backUrl = match(request('from')) {
            'search' => url()->previous(),
            'dashboard' => route('dashboard'),
            default => route('dashboard'),
        };

        $detailPertama = $itemProduksi->detailProduk->first();
        $hargaMin = $itemProduksi->detailProduk->min('harga_dasar');
        $hargaMax = $itemProduksi->detailProduk->max('harga_dasar');
        $satuan = $itemProduksi->satuanHarga->nama_satuan ?? null;

        $bolehDp = strtolower($itemProduksi->kategoriUsaha->jenisPembayaran->nama_jenis_pembayaran ?? '') === 'dp';
        $isSablon = strtolower($itemProduksi->kategoriUsaha->nama_kategori ?? '') === 'sablon';
        $isInterior = strtolower($itemProduksi->kategoriUsaha->nama_kategori ?? '') === 'interior';
    @endphp

    <a href="{{ $backUrl }}" class="btn-kembali">
        <i class="fas fa-chevron-left"></i> Kembali
    </a>

    <section class="detail-produk-grid">

        <div class="kolom-kiri">
            <div class="produk-gallery">
                <button type="button" class="gallery-btn prev-btn">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <div class="gallery-track-wrapper">
                    <div class="gallery-track" id="galleryTrack">
                        @forelse ($itemProduksi->fotoProduk as $foto)
                            <div class="gallery-item">
                                <img src="{{ asset($foto->nama_foto) }}" alt="{{ $itemProduksi->nama_item }}">
                            </div>
                        @empty
                            <div class="gallery-item">
                                <img src="{{ asset('assets/images/no-image.png') }}" alt="No Image">
                            </div>
                        @endforelse
                    </div>
                </div>

                <button type="button" class="gallery-btn next-btn">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <div class="produk-description">
                <h1>{{ $itemProduksi->nama_item }}</h1>

                <p id="deskripsiProduk">
                    {{ $itemProduksi->deskripsi_item ?? 'Deskripsi produk belum tersedia.' }}
                </p>

                <button type="button" class="btn-toggle-deskripsi" id="toggleDeskripsi">
                    Lihat Selengkapnya
                </button>
            </div>
        </div>

        <form id="checkoutForm" action="{{ route('pesanan.beli') }}" method="POST" class="order-card">
            @csrf

            <div class="harga-row">
                <h2 id="displayHarga">
                    @if ($hargaMin == $hargaMax)
                        Rp {{ number_format($hargaMin, 0, ',', '.') }}
                    @else
                        Rp {{ number_format($hargaMin, 0, ',', '.') }} - Rp {{ number_format($hargaMax, 0, ',', '.') }}
                    @endif
                </h2>

                @if ($satuan)
                    <span class="satuan-text">/ {{ $satuan }}</span>
                @endif
            </div>

            <div class="form-group mt-3">
                <label>Ukuran</label>

                <div class="size-options">
                    @forelse ($itemProduksi->detailProduk as $index => $detail)
                        <button type="button"
                            class="size-btn {{ $index === 0 ? 'active' : '' }}"
                            data-id-detail-produk="{{ $detail->id_detail_produk }}"
                            data-harga="{{ $detail->harga_dasar }}">
                            {{ $detail->ukuran ?? '-' }}
                        </button>
                    @empty
                        <p class="text-muted mb-0">Ukuran belum tersedia.</p>
                    @endforelse
                </div>
            </div>

            <input type="hidden" name="id_detail_produk" id="idDetailProduk" value="{{ $detailPertama->id_detail_produk ?? '' }}">
            <input type="hidden" name="subtotal" id="subtotalInput" value="0">

            <hr class="divider">

            <div class="qty-subtotal-row">
                <div class="qty-box-wrapper">
                    <label>Kuantitas</label>

                    <div class="qty-box">
                        <button type="button" id="minusQty">-</button>
                        <input type="number" name="kuantitas" id="qtyInput" value="1" min="1">
                        <button type="button" id="plusQty">+</button>
                    </div>
                </div>

                <div class="subtotal-box main-subtotal">
                    <strong id="subtotalTextTop">Rp 0</strong>
                </div>
            </div>

            <hr class="divider">

            <div id="actionBeforeForm">
                <div class="button-action-wrapper">

                    @auth
                        <button type="submit" form="chatForm" class="btn-chat">
                            <i class="fas fa-comments"></i> Tanya Admin
                        </button>

                        <button
                            type="button"
                            class="btn-beli {{ $itemProduksi->status_aktif !== 'Aktif' ? 'btn-nonaktif' : '' }}"
                            id="btnBeliAwal"
                            {{ $itemProduksi->status_aktif !== 'Aktif' ? 'disabled' : '' }}>
                            {{ $itemProduksi->status_aktif === 'Aktif' ? 'Pesan Sekarang' : 'Produk Tidak Aktif' }}
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn-chat">
                            <i class="fas fa-comments"></i> Tanya Admin
                        </a>

                        <a href="{{ route('login') }}" class="btn-beli">
                            Pesan Sekarang
                        </a>
                    @endauth

                </div>
            </div>

            <button type="button" class="btn-dropdown-pesan" id="togglePesan">
                Pesan Sekarang <i class="fas fa-chevron-down"></i>
            </button>

            <div class="pesan-form" id="pesanForm">
                <div class="drag-handle d-md-none"></div>

                <h5 class="form-title d-md-none">Pesan Sekarang</h5>

                <div class="form-group">
                    <label>Nama Pemesan</label>

                    <input
                        type="text"
                        name="nama_penerima"
                        value="{{ old('nama_penerima') }}"
                        class="input-pesan @error('nama_penerima') input-error @enderror">

                    @error('nama_penerima')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Alamat Pemesan</label>

                    <textarea
                        name="alamat_penerima"
                        class="input-pesan textarea-pesan @error('alamat_penerima') input-error @enderror"
                        rows="3">{{ old('alamat_penerima') }}</textarea>

                    @error('alamat_penerima')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>No. WhatsApp</label>

                    <input
                        type="text"
                        name="No_hp_penerima"
                        value="{{ old('No_hp_penerima') }}"
                        class="input-pesan @error('No_hp_penerima') input-error @enderror">

                    @error('No_hp_penerima')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

               <div class="form-group w-100">
                    <label>
                        @if ($isSablon)
                            Jadwal Pengambilan
                        @elseif ($isInterior)
                            Jadwal Pengantaran
                        @else
                            Jadwal Pemasangan
                        @endif
                    </label>

                    <small class="helper-date">
                        @if ($isSablon)
                            Pilih Tanggal Pengambilan
                        @elseif ($isInterior)
                            Pilih Tanggal Pengantaran
                        @else
                            Pilih Tanggal Pemasangan
                        @endif
                    </small>

                    <input
                        type="date"
                        name="jadwal_pemasangan"
                        value="{{ old('jadwal_pemasangan') }}"
                        class="input-pesan date-input @error('jadwal_pemasangan') input-error @enderror">

                    @error('jadwal_pemasangan')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                @if ($bolehDp)
                    <div class="form-group">
                        <label>Pilihan Pembayaran</label>

                        <label>
                            <input type="radio" name="tipe_pembayaran" value="DP" checked>
                            Bayar DP 50%
                        </label>

                        <label>
                            <input type="radio" name="tipe_pembayaran" value="Full">
                            Bayar Full 100%
                        </label>
                    </div>
                @else
                    <input type="hidden" name="tipe_pembayaran" value="Full">
                @endif

                <div class="button-action-wrapper mt-3">
                    @auth
                        <button type="submit" form="chatForm" class="btn-chat d-md-none">
                            <i class="fas fa-comments"></i> Tanya Admin
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn-chat d-md-none">
                            <i class="fas fa-comments"></i> Tanya Admin
                        </a>
                    @endauth

                    <button type="submit" class="btn-beli" id="btnSubmitBeli"
                        {{ $itemProduksi->status_aktif !== 'Aktif' || !$detailPertama ? 'disabled' : '' }}>
                        Bayar
                    </button>
                </div>
            </div>
        </form>

        @auth
            <form id="chatForm" action="{{ route('chat.start', $itemProduksi->id_item_produksi) }}" method="POST">
                @csrf
            </form>
        @endauth

    </section>
</div>

<div class="mobile-backdrop" id="mobileBackdrop"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const galleryTrack = document.getElementById('galleryTrack');
    const prevButton = document.querySelector('.prev-btn');
    const nextButton = document.querySelector('.next-btn');

    if (galleryTrack && prevButton && nextButton) {
        nextButton.addEventListener('click', function () {
            galleryTrack.scrollBy({
                left: galleryTrack.clientWidth,
                behavior: 'smooth'
            });
        });

        prevButton.addEventListener('click', function () {
            galleryTrack.scrollBy({
                left: -galleryTrack.clientWidth,
                behavior: 'smooth'
            });
        });
    }

    const sizeButtons = document.querySelectorAll('.size-btn');
    const displayHarga = document.getElementById('displayHarga');
    const idDetailProduk = document.getElementById('idDetailProduk');

    const minusQty = document.getElementById('minusQty');
    const plusQty = document.getElementById('plusQty');
    const qtyInput = document.getElementById('qtyInput');

    const subtotalTextTop = document.getElementById('subtotalTextTop');
    const subtotalTextBottom = document.getElementById('subtotalTextBottom');
    const subtotalInput = document.getElementById('subtotalInput');

    let hargaAktif = Number(document.querySelector('.size-btn.active')?.dataset.harga || 0);

    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }

    function hitungSubtotal(fallbackQty = null) {
        if (!qtyInput || !subtotalInput) return;

        const qty = fallbackQty !== null ? fallbackQty : Number(qtyInput.value || 1);
        const subtotal = hargaAktif * qty;
        const formattedSubtotal = formatRupiah(subtotal);

        if (subtotalTextTop) subtotalTextTop.textContent = formattedSubtotal;
        if (subtotalTextBottom) subtotalTextBottom.textContent = formattedSubtotal;

        subtotalInput.value = subtotal;
    }

    sizeButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            sizeButtons.forEach(function (item) {
                item.classList.remove('active');
            });

            this.classList.add('active');

            hargaAktif = Number(this.dataset.harga || 0);

            if (idDetailProduk) {
                idDetailProduk.value = this.dataset.idDetailProduk || '';
            }

            if (displayHarga) {
                displayHarga.textContent = formatRupiah(hargaAktif);
            }

            hitungSubtotal();
        });
    });

    if (minusQty && plusQty && qtyInput) {
        minusQty.addEventListener('click', function () {
            let qty = Number(qtyInput.value || 1);

            if (qty > 1) {
                qtyInput.value = qty - 1;
                hitungSubtotal();
            }
        });

        plusQty.addEventListener('click', function () {
            let qty = Number(qtyInput.value || 1);

            qtyInput.value = qty + 1;
            hitungSubtotal();
        });

        qtyInput.addEventListener('input', function () {
            let qty = parseInt(this.value);

            if (isNaN(qty) || qty < 1) {
                hitungSubtotal(1);
                return;
            }

            hitungSubtotal();
        });

        qtyInput.addEventListener('blur', function () {
            let qty = parseInt(this.value);

            if (isNaN(qty) || qty < 1) {
                this.value = 1;
                hitungSubtotal();
            }
        });
    }

    if (displayHarga && hargaAktif > 0) {
        displayHarga.textContent = formatRupiah(hargaAktif);
    }

    hitungSubtotal();

    const pesanForm = document.getElementById('pesanForm');
    const togglePesan = document.getElementById('togglePesan');
    const btnBeliAwal = document.getElementById('btnBeliAwal');
    const actionBeforeForm = document.getElementById('actionBeforeForm');
    const mobileBackdrop = document.getElementById('mobileBackdrop');
    const checkoutForm = document.getElementById('checkoutForm');

    function bukaForm() {
        if (!pesanForm) return;

        pesanForm.classList.add('show');

        if (window.innerWidth > 768) {
            if (togglePesan) togglePesan.style.display = 'none';
            if (actionBeforeForm) actionBeforeForm.style.display = 'none';
        } else {
            document.body.classList.add('mobile-sheet-open');

            if (mobileBackdrop) {
                mobileBackdrop.classList.add('show');
            }
        }
    }

    function tutupFormMobile() {
        if (window.innerWidth <= 768 && pesanForm) {
            pesanForm.classList.remove('show');
            document.body.classList.remove('mobile-sheet-open');

            if (mobileBackdrop) {
                mobileBackdrop.classList.remove('show');
            }
        }
    }

    if (btnBeliAwal) {
        btnBeliAwal.addEventListener('click', bukaForm);
    }

    if (togglePesan) {
        togglePesan.addEventListener('click', bukaForm);
    }

    if (mobileBackdrop) {
        mobileBackdrop.addEventListener('click', tutupFormMobile);
    }

    let startY = null;

    if (pesanForm) {
        pesanForm.addEventListener('touchstart', function (event) {
            startY = event.touches[0].clientY;
        }, { passive: true });

        pesanForm.addEventListener('touchmove', function (event) {
            if (window.innerWidth <= 768 && startY) {
                const currentY = event.touches[0].clientY;

                if (currentY - startY > 50) {
                    tutupFormMobile();
                }
            }
        }, { passive: true });
    }

    if (checkoutForm) {
        checkoutForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            if (!pesanForm.classList.contains('show')) {
                bukaForm();
                return;
            }

            const requiredInputs = pesanForm.querySelectorAll('input[required], textarea[required], select[required]');
            let isComplete = true;

            requiredInputs.forEach(function (input) {
                if (!input.value.trim()) {
                    isComplete = false;
                }
            });

            if (!isComplete) {
                bukaForm();

                const firstEmpty = Array.from(requiredInputs).find(function (input) {
                    return !input.value.trim();
                });

                if (firstEmpty) {
                    setTimeout(function () {
                        firstEmpty.focus();
                    }, 300);
                }

                return;
            }

            const btnSubmitBeli = document.getElementById('btnSubmitBeli');
            let originalBtnText = '';
            if (btnSubmitBeli) {
                originalBtnText = btnSubmitBeli.innerHTML;
                btnSubmitBeli.disabled = true;
                btnSubmitBeli.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i>Memproses...';
            }

            try {
                const responsePesanan = await fetch(checkoutForm.action, {
                    method: 'POST',
                    body: new FormData(checkoutForm),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    }
                });

                const dataPesanan = await responsePesanan.json();

                if (responsePesanan.status === 422) {

                    // hapus error lama
                    document.querySelectorAll('.error-message').forEach(el => el.remove());
                    document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));

                    for (const field in dataPesanan.errors) {

                        const input = checkoutForm.querySelector(`[name="${field}"]`);

                        if (input) {

                            input.classList.add('input-error');

                            const error = document.createElement('span');
                            error.className = 'error-message';
                            error.textContent = dataPesanan.errors[field][0];

                            input.insertAdjacentElement('afterend', error);
                        }
                    }

                    if (btnSubmitBeli) {
                        btnSubmitBeli.disabled = false;
                        btnSubmitBeli.innerHTML = originalBtnText;
                    }

                    return;
                }

                if (!responsePesanan.ok) {
                    alert(dataPesanan.message);
                    return;
                }

                const tipePembayaranInput = checkoutForm.querySelector('input[name="tipe_pembayaran"]:checked')
                    || checkoutForm.querySelector('input[name="tipe_pembayaran"]');

                const responseBayar = await fetch("{{ route('pembayaran.midtrans') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        id_pesanan: dataPesanan.id_pesanan,
                        tipe_pembayaran: tipePembayaranInput ? tipePembayaranInput.value : 'Full'
                    })
                });

                const dataBayar = await responseBayar.json();

                if (!responseBayar.ok || !dataBayar.snap_token) {
                    alert(dataBayar.message || 'Gagal membuat pembayaran Midtrans.');
                    if (btnSubmitBeli) {
                        btnSubmitBeli.disabled = false;
                        btnSubmitBeli.innerHTML = originalBtnText;
                    }
                    return;
                }

                sessionStorage.setItem('snap_token', dataBayar.snap_token);
                sessionStorage.setItem('id_pembayaran', dataBayar.id_pembayaran);

                window.location.href = "{{ route('klien.pesanan.riwayat') }}?bayar=1";

            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan saat memproses pesanan.');
                if (btnSubmitBeli) {
                    btnSubmitBeli.disabled = false;
                    btnSubmitBeli.innerHTML = originalBtnText;
                }
            }
        });
    }

    const deskripsiProduk = document.getElementById('deskripsiProduk');
    const toggleDeskripsi = document.getElementById('toggleDeskripsi');

    if (deskripsiProduk && toggleDeskripsi) {
        toggleDeskripsi.addEventListener('click', function () {
            deskripsiProduk.classList.toggle('expanded');

            this.textContent = deskripsiProduk.classList.contains('expanded')
                ? 'Tutup'
                : 'Lihat Selengkapnya';
        });
    }
});
</script>
@endpush