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
    @endphp
    <a href="{{ $backUrl }}" class="btn-kembali">
        <i class="fas fa-chevron-left"></i> Kembali
    </a>

    @php
        $hargaMin = $itemProduksi->detailProduk->min('harga_dasar');
        $hargaMax = $itemProduksi->detailProduk->max('harga_dasar');
        $satuan   = $itemProduksi->satuanHarga->nama_satuan ?? $item->detailProduk->first()?->satuanHarga?->nama_satuan;
    @endphp

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

        <form id="checkoutForm" action="{{ route('pesanan.beli') }}" method="POST" class="order-card" enctype="multipart/form-data">
            @csrf

            <div class="harga-row">
                <h2 id="displayHarga">
                    @if ($hargaMin == $hargaMax)
                        Rp {{ number_format($hargaMin, 0, ',', '.') }}
                    @else
                        Rp {{ number_format($hargaMin, 0, ',', '.') }} - {{ number_format($hargaMax, 0, ',', '.') }}
                    @endif
                </h2>
                @if ($satuan)
                    <span class="satuan-text">/ {{ $satuan }}</span>
                @endif
            </div>

            <br>
            
            <div class="form-group mt-3">
                <label>Ukuran</label>
                <div class="size-options">
                    @foreach ($itemProduksi->detailProduk as $index => $detail)
                        <button type="button"
                            class="size-btn {{ $index == 0 ? 'active' : '' }}"
                            data-id-detail="{{ $detail->id_detail_produk }}"
                            data-harga="{{ $detail->harga_dasar }}">
                            {{ $detail->ukuran ?? '-' }}
                        </button>
                    @endforeach
                </div>
            </div>

            <input type="hidden" name="id_detail_produk" id="idDetailProduk" value="{{ $itemProduksi->detailProduk->first()->id_detail_produk ?? '' }}">
            <input type="hidden" name="subtotal" id="subtotalInput">

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
                    <button type="submit" form="chatForm" class="btn-chat">
                        <i class="fas fa-comments"></i> Tanya Admin
                    </button>
                    <button
                        type="button"
                        class="btn-beli {{ $itemProduksi->status_aktif !== 'Aktif' ? 'btn-nonaktif' : '' }}"
                        id="btnBeliAwal"
                        {{ $itemProduksi->status_aktif !== 'Aktif' ? 'disabled' : '' }}>
                        {{ $itemProduksi->status_aktif === 'Aktif' ? 'Beli Sekarang' : 'Produk Tidak Aktif' }}
                    </button>
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
                    <input type="text" name="nama_penerima" class="input-pesan" required>
                </div>

                <div class="form-group">
                    <label>Alamat Pemesan</label>
                    <textarea
                        name="alamat_penerima"
                        class="input-pesan textarea-pesan"
                        rows="3"
                        placeholder="Masukkan alamat lengkap"
                        required></textarea>
                </div>

                <div class="form-group">
                    <label>No. HP / WhatsApp</label>
                    <input type="number" name="No_hp_penerima" class="input-pesan" required>
                </div>

                <div class="flex-row-group align-end">
                    <div class="form-group w-100">
                        <label>Jadwal Pemasangan</label>
                        <small class="helper-date">Pilih tanggal pemasangan</small>
                        <input type="date" name="jadwal_pemasangan" class="input-pesan date-input" required>
                    </div>
                    
                    <div class="subtotal-box bottom-subtotal">
                        <small>Subtotal</small>
                        <strong id="subtotalTextBottom">Rp 0</strong>
                    </div>
                </div>

                @php
                    $bolehDp = strtolower($itemProduksi->kategoriUsaha->jenisPembayaran->nama_jenis_pembayaran ?? '') === 'dp';
                @endphp

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
                    <button type="submit" form="chatForm" class="btn-chat d-md-none">
                        <i class="fas fa-comments"></i> Tanya Admin
                    </button>
                    <button
                        type="submit"
                        class="btn-beli"
                        id="btnSubmitBeli"
                        {{ $itemProduksi->status_aktif !== 'Aktif' ? 'disabled' : '' }}>
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
        /*
        |--------------------------------------------------------------------------
        | Slider Galeri Foto
        |--------------------------------------------------------------------------
        */
        const galleryTrack = document.getElementById('galleryTrack');
        const prevButton = document.querySelector('.prev-btn');
        const nextButton = document.querySelector('.next-btn');

        if (galleryTrack && prevButton && nextButton) {
            nextButton.addEventListener('click', () => {
                galleryTrack.scrollBy({ left: galleryTrack.clientWidth, behavior: 'smooth' });
            });
            prevButton.addEventListener('click', () => {
                galleryTrack.scrollBy({ left: -galleryTrack.clientWidth, behavior: 'smooth' });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | State & Elemen Harga / Kuantitas
        |--------------------------------------------------------------------------
        */
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
            const formatted = formatRupiah(subtotal);

            if (subtotalTextTop) subtotalTextTop.textContent = formatted;
            if (subtotalTextBottom) subtotalTextBottom.textContent = formatted;
            subtotalInput.value = subtotal;
        }

        sizeButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                sizeButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                hargaAktif = Number(this.dataset.harga || 0);
                
                if (idDetailProduk) idDetailProduk.value = this.dataset.idDetail || '';
                if (displayHarga) displayHarga.textContent = formatRupiah(hargaAktif);
                
                hitungSubtotal();
            });
        });

        if (minusQty && plusQty && qtyInput) {
            minusQty.addEventListener('click', () => {
                let qty = Number(qtyInput.value || 1);
                if (qty > 1) {
                    qtyInput.value = qty - 1;
                    hitungSubtotal();
                }
            });
            
            plusQty.addEventListener('click', () => {
                let qty = Number(qtyInput.value || 1);
                qtyInput.value = qty + 1;
                hitungSubtotal();
            });

            qtyInput.addEventListener('input', function() {
                let val = parseInt(this.value);
                if (isNaN(val) || val < 1) {
                    hitungSubtotal(1);
                } else {
                    hitungSubtotal();
                }
            });

            qtyInput.addEventListener('blur', function() {
                let val = parseInt(this.value);
                if (isNaN(val) || val < 1) {
                    this.value = 1;
                    hitungSubtotal();
                }
            });
        }

        // Inisialisasi awal
        if (displayHarga && hargaAktif > 0) displayHarga.textContent = formatRupiah(hargaAktif);
        hitungSubtotal();

        /*
        |--------------------------------------------------------------------------
        | Logika Form Dinamis (Desktop Dropdown & Mobile Bottom Sheet)
        |--------------------------------------------------------------------------
        */
        const pesanForm = document.getElementById('pesanForm');
        const togglePesan = document.getElementById('togglePesan');
        const btnBeliAwal = document.getElementById('btnBeliAwal');
        const actionBeforeForm = document.getElementById('actionBeforeForm');
        const mobileBackdrop = document.getElementById('mobileBackdrop');
        const checkoutForm = document.getElementById('checkoutForm');

        function bukaForm() {
            pesanForm.classList.add('show');
            
            if (window.innerWidth > 768) {
                if (togglePesan) togglePesan.style.display = 'none';
                if (actionBeforeForm) actionBeforeForm.style.display = 'none';
            } 
            else {
                document.body.classList.add('mobile-sheet-open');
                if (mobileBackdrop) mobileBackdrop.classList.add('show');
            }
        }

        function tutupFormMobile() {
            if (window.innerWidth <= 768) {
                pesanForm.classList.remove('show');
                document.body.classList.remove('mobile-sheet-open');
                if (mobileBackdrop) mobileBackdrop.classList.remove('show');
            }
        }

        if (btnBeliAwal) btnBeliAwal.addEventListener('click', bukaForm);
        if (togglePesan) togglePesan.addEventListener('click', bukaForm);

        if (mobileBackdrop) {
            mobileBackdrop.addEventListener('click', tutupFormMobile);
        }

        let startY;
        pesanForm.addEventListener('touchstart', (e) => startY = e.touches[0].clientY, {passive: true});
        pesanForm.addEventListener('touchmove', (e) => {
            if (window.innerWidth <= 768 && startY) {
                const currentY = e.touches[0].clientY;
                if (currentY - startY > 50) { 
                    tutupFormMobile();
                }
            }
        }, {passive: true});

        /*
        |--------------------------------------------------------------------------
        | Validasi saat form di-submit
        |--------------------------------------------------------------------------
        */
        checkoutForm.addEventListener('submit', function (e) {
            const requiredInputs = pesanForm.querySelectorAll('input[required]');
            let isComplete = true;

            requiredInputs.forEach(input => {
                if (!input.value.trim()) isComplete = false;
            });

            if (!pesanForm.classList.contains('show')) {
                e.preventDefault();
                bukaForm();
                return;
            }

            if (!isComplete) {
                e.preventDefault();
                bukaForm(); 
                const firstEmpty = Array.from(requiredInputs).find(input => !input.value.trim());
                if (firstEmpty) {
                    setTimeout(() => firstEmpty.focus(), 300);
                }
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Expander Deskripsi Produk
        |--------------------------------------------------------------------------
        */
        const deskripsiProduk = document.getElementById('deskripsiProduk');
        const toggleDeskripsi = document.getElementById('toggleDeskripsi');

        if (deskripsiProduk && toggleDeskripsi) {
            toggleDeskripsi.addEventListener('click', function () {
                deskripsiProduk.classList.toggle('expanded');
                this.textContent = deskripsiProduk.classList.contains('expanded') ? 'Tutup' : 'Lihat Selengkapnya';
            });
        }
    });
</script>
@endpush