@extends('klien.layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('fe-klien/detail-produk.css') }}?v={{ time() }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.fullscreen@1.6.0/Control.FullScreen.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
<style>
    #map { height: 250px; width: 100%; border-radius: 8px; z-index: 1; }
    .search-map-container { position: relative; margin-bottom: 10px; }
    #searchAlamat { width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; }
    #btnSearchAlamat { position: absolute; right: 0; top: 0; height: 100%; border: none; background: #007bff; color: white; padding: 0 15px; border-radius: 0 4px 4px 0; }
    
    #search-suggestions {
        position: absolute;
        top: 100%; 
        left: 0;
        width: 100%;
        background-color: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        max-height: 250px;
        overflow-y: auto;
        z-index: 9999;
        margin-top: 4px;
        padding: 0;
        list-style: none;
        display: none;
    }
    #search-suggestions button {
        display: block;
        width: 100%;
        text-align: left;
        padding: 12px 16px;
        background: transparent;
        border: none;
        border-bottom: 1px solid #f0f0f0;
        font-size: 14px;
        color: #333;
        cursor: pointer;
        transition: background-color 0.2s ease;
        line-height: 1.4;
    }
    #search-suggestions button:last-child {
        border-bottom: none;
    }
    #search-suggestions button:hover {
        background-color: #f8f9fa;
        color: #0056b3;
    }
</style>
@endpush

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.fullscreen@1.6.0/Control.FullScreen.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
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
        
        $minDate = now()->toDateString();
        if ($itemProduksi->konfigurasiBiaya && $itemProduksi->konfigurasiBiaya->tipe_penentuan_waktu === 'tanggal' && $itemProduksi->konfigurasiBiaya->batas_hari_zona_merah !== null) {
            $minDate = now()->addDays((int) $itemProduksi->konfigurasiBiaya->batas_hari_zona_merah)->toDateString();
        }
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
                            data-harga="{{ $detail->getRawOriginal('harga_dasar') ?? $detail->harga_dasar }}">
                            {{ $detail->ukuran ?? '-' }}
                        </button>
                    @empty
                        <p class="text-muted mb-0">Ukuran belum tersedia.</p>
                    @endforelse
                </div>
            </div>

            <input type="hidden" name="id_detail_produk" id="idDetailProduk" value="{{ $detailPertama->id_detail_produk ?? '' }}">
            <input type="hidden" id="raw-harga-dasar" value="{{ $detailPertama ? ($detailPertama->getRawOriginal('harga_dasar') ?? $detailPertama->harga_dasar) : 0 }}">
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
            </div>

            <div class="rincian-harga-wrapper">
                <div class="rincian-harga-card">
                    <div class="rincian-harga-header">
                        <i class="fas fa-receipt"></i>
                        <span>Rincian Biaya</span>
                    </div>

                    <div class="rincian-harga-body">
                        <div class="rincian-harga-item">
                            <span class="rincian-label">
                                <i class="fas fa-cube"></i> Subtotal Produk
                            </span>
                            <span id="rincian-produk" class="rincian-value">Belum dihitung</span>
                        </div>

                        <div class="rincian-harga-item" id="rincian-transport-row">
                            <span class="rincian-label">
                                <i class="fas fa-truck"></i> Biaya Transportasi
                            </span>
                            <span id="rincian-transport" class="rincian-value">Belum dihitung</span>
                        </div>

                        <div class="rincian-harga-item" id="rincian-waktu-row" style="display: none !important;">
                            <span class="rincian-label">
                                <i class="fas fa-bolt"></i> Layanan Prioritas
                            </span>
                            <span id="rincian-waktu" class="rincian-value rincian-value--prioritas">Rp 0</span>
                        </div>
                    </div>

                    <div class="rincian-harga-divider"></div>

                    <div class="rincian-harga-total">
                        <span class="rincian-total-label">Grand Total</span>
                        <span id="subtotalTextTop" class="rincian-total-value">Rp 0</span>
                    </div>

                    {{-- DP Payment Info Section --}}
                    <div id="dp-info-section" class="rincian-dp-section" style="display: none;">
                        <div class="rincian-harga-divider"></div>
                        <div class="rincian-dp-body">
                            <div class="rincian-harga-item">
                                <span class="rincian-label">
                                    <i class="fas fa-wallet"></i> Bayar Sekarang (DP 50%)
                                </span>
                                <span id="dp-bayar-sekarang" class="rincian-value rincian-value--dp">Rp 0</span>
                            </div>
                            <div class="rincian-harga-item">
                                <span class="rincian-label">
                                    <i class="fas fa-clock-rotate-left"></i> Sisa Pelunasan
                                </span>
                                <span id="dp-sisa-pelunasan" class="rincian-value rincian-value--sisa">Rp 0</span>
                            </div>
                        </div>
                        <div class="rincian-dp-note">
                            <i class="fas fa-info-circle"></i>
                            <span>Anda cukup membayar <strong>50%</strong> dari total harga terlebih dahulu. Sisa pelunasan akan dibayarkan setelah pesanan selesai dikerjakan.</span>
                        </div>
                    </div>
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
                    <label>Alamat Lengkap (Tujuan / Pemasangan)</label>

                    <textarea
                        name="alamat_penerima"
                        class="input-pesan textarea-pesan @error('alamat_penerima') input-error @enderror"
                        rows="2">{{ old('alamat_penerima') }}</textarea>

                    @error('alamat_penerima')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                @if($itemProduksi->konfigurasiBiaya && $itemProduksi->konfigurasiBiaya->is_biaya_jarak_aktif)
                <div class="form-group">
                    <label>Pin Lokasi Anda (Untuk Hitung Tambahan Biaya Jarak)</label>
                    <div class="search-map-container">
                        <input type="text" id="searchAlamat" placeholder="Cari daerah / jalan...">
                        <button type="button" id="btnSearchAlamat"><i class="fas fa-search"></i></button>
                        <div id="search-suggestions"></div>
                    </div>
                    <div id="info-jarak" class="info-card-biaya">
                        <div class="info-card-biaya-row">
                            <span class="info-card-biaya-icon"><i class="fas fa-tags"></i></span>
                            <span class="info-card-biaya-label">Ketentuan Tarif</span>
                            <span class="info-card-biaya-value" style="font-size: 0.9em; text-align: right;">
                                <strong>Gratis</strong> (≤ 45 km) <br>
                                Rp {{ number_format($itemProduksi->konfigurasiBiaya->getRawOriginal('tarif_per_km') ?? $itemProduksi->konfigurasiBiaya->tarif_per_km, 0, ',', '.') }} / km (> 45 km)
                            </span>
                        </div>
                        <div class="info-card-biaya-row">
                            <span class="info-card-biaya-icon"><i class="fas fa-route"></i></span>
                            <span class="info-card-biaya-label">Jarak dari Workshop</span>
                            <span class="info-card-biaya-value"><span id="display-jarak">0</span> km</span>
                        </div>
                        <div class="info-card-biaya-row">
                            <span class="info-card-biaya-icon"><i class="fas fa-truck"></i></span>
                            <span class="info-card-biaya-label">Est. Biaya Transport</span>
                            <span class="info-card-biaya-value info-card-biaya-value--highlight" id="display-biaya-jarak">Rp 0</span>
                        </div>
                    </div>
                    <div id="map"></div>
                    <small class="text-muted mt-1 d-block">Penting: Geser pin untuk penyesuaian letak yang paling presisi.</small>
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <input type="hidden" name="alamat_lengkap" id="alamat_lengkap">
                </div>
                @endif

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
                        @php
                            $prefixWaktu = ($itemProduksi->konfigurasiBiaya && $itemProduksi->konfigurasiBiaya->tipe_penentuan_waktu === 'estimasi') ? 'Estimasi' : 'Pilih Tanggal';
                        @endphp
                        @if ($isSablon)
                            {{ $prefixWaktu }} Pengambilan
                        @elseif ($isInterior)
                            {{ $prefixWaktu }} Pengantaran
                        @else
                            {{ $prefixWaktu }} Pemasangan
                        @endif
                    </small>

                    @if($itemProduksi->konfigurasiBiaya && $itemProduksi->konfigurasiBiaya->tipe_penentuan_waktu === 'estimasi')
                        <div class="info-card-biaya info-card-biaya--waktu">
                            <div class="info-card-biaya-row">
                                <span class="info-card-biaya-icon"><i class="fas fa-calendar-alt"></i></span>
                                <span class="info-card-biaya-label">Estimasi Pengerjaan</span>
                                <span class="info-card-biaya-value text-success font-weight-bold">{{ $itemProduksi->konfigurasiBiaya->estimasi_pengerjaan }}</span>
                            </div>
                        </div>
                    @endif


                    @if(!$itemProduksi->konfigurasiBiaya || $itemProduksi->konfigurasiBiaya->tipe_penentuan_waktu === 'tanggal')
                        <input
                            type="date"
                            name="jadwal_pemasangan"
                            id="jadwalPemasangan"
                            value="{{ old('jadwal_pemasangan') }}"
                            min="{{ $minDate }}"
                            class="input-pesan date-input @error('jadwal_pemasangan') input-error @enderror">

                        @error('jadwal_pemasangan')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    @endif
                </div>

                @if ($bolehDp)
                    <div class="form-group">
                        <label>Pilihan Pembayaran</label>

                        <div class="payment-toggle-group">
                            <label class="payment-toggle-option active" data-payment="DP">
                                <input type="radio" name="tipe_pembayaran" value="DP" checked>
                                <div class="payment-toggle-content">
                                    <div class="payment-toggle-icon">
                                        <i class="fas fa-hand-holding-dollar"></i>
                                    </div>
                                    <div class="payment-toggle-info">
                                        <span class="payment-toggle-title">DP 50%</span>
                                        <span class="payment-toggle-desc">Bayar separuh dulu</span>
                                    </div>
                                </div>
                                <span class="payment-toggle-check"><i class="fas fa-check-circle"></i></span>
                            </label>

                            <label class="payment-toggle-option" data-payment="Full">
                                <input type="radio" name="tipe_pembayaran" value="Full">
                                <div class="payment-toggle-content">
                                    <div class="payment-toggle-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="payment-toggle-info">
                                        <span class="payment-toggle-title">Full 100%</span>
                                        <span class="payment-toggle-desc">Bayar lunas langsung</span>
                                    </div>
                                </div>
                                <span class="payment-toggle-check"><i class="fas fa-check-circle"></i></span>
                            </label>
                        </div>
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

    // hargaAktif dihapus, menggunakan input hidden #raw-harga-dasar

    const configBiaya = {
        is_jarak: {{ ($itemProduksi->konfigurasiBiaya && $itemProduksi->konfigurasiBiaya->is_biaya_jarak_aktif) ? 'true' : 'false' }},
        tarif_per_km: parseFloat("{{ $itemProduksi->konfigurasiBiaya ? ($itemProduksi->konfigurasiBiaya->getRawOriginal('tarif_per_km') ?? $itemProduksi->konfigurasiBiaya->tarif_per_km) : 0 }}"),
        workshop_lat: parseFloat("{{ $workshopCoord ? $workshopCoord->latitude : '-3.2994' }}"),
        workshop_lon: parseFloat("{{ $workshopCoord ? $workshopCoord->longitude : '114.5933' }}")
    };

    function formatRupiah(angka) {
        const safeNumber = Math.round(Number(angka)) || 0;
        return 'Rp ' + safeNumber.toLocaleString('id-ID');
    }

    function hitungJarakHaversine(lat1, lon1, lat2, lon2) {
        const R = 6371; 
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = 
            Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
            Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
        return R * c;
    }

    let currentRoutingDistance = 0;

    function hitungSubtotal(fallbackQty = null) {
        if (!qtyInput || !subtotalInput) return;

        let hargaDasar = parseFloat(document.getElementById('raw-harga-dasar')?.value) || 0;
        
        // Hindari penangkapan Event object saat dipanggil lewat EventListener
        const isValidQty = fallbackQty !== null && typeof fallbackQty !== 'object';
        const qty = isValidQty ? parseFloat(fallbackQty) : parseFloat(qtyInput.value || 1);
        
        let subtotal = hargaDasar * qty;
        
        let globalBiayaJarak = 0;
        if (configBiaya.is_jarak) {
            let jarakKm = parseFloat(currentRoutingDistance || 0);

            if (jarakKm <= 45) {
                globalBiayaJarak = 0;
            } else {
                globalBiayaJarak = jarakKm * configBiaya.tarif_per_km;
            }

            const elJarak = document.getElementById('display-jarak');
            const elBiayaJarak = document.getElementById('display-biaya-jarak');
            if (elJarak) elJarak.textContent = jarakKm.toFixed(2);
            if (elBiayaJarak) {
                if (jarakKm <= 45) {
                    elBiayaJarak.textContent = 'Gratis';
                    elBiayaJarak.style.color = '#28a745';
                } else {
                    elBiayaJarak.textContent = formatRupiah(globalBiayaJarak);
                    elBiayaJarak.style.color = '';
                }
            }
        }

        const grandTotal = Math.round(subtotal + globalBiayaJarak);
        const formattedSubtotal = formatRupiah(grandTotal);

        if (subtotalTextTop) subtotalTextTop.textContent = formattedSubtotal;
        if (subtotalTextBottom) subtotalTextBottom.textContent = formattedSubtotal;

        // Update Rincian Biaya
        const elRincianProduk = document.getElementById('rincian-produk');
        const elRincianTransport = document.getElementById('rincian-transport');
        const elRincianTransportRow = document.getElementById('rincian-transport-row');
        const elRincianWaktu = document.getElementById('rincian-waktu');
        const elRincianWaktuRow = document.getElementById('rincian-waktu-row');
        
        if (elRincianProduk) elRincianProduk.textContent = formatRupiah(subtotal);
        
        if (elRincianTransport && elRincianTransportRow) {
            if (!configBiaya.is_jarak || (currentRoutingDistance > 0 && globalBiayaJarak === 0)) {
                elRincianTransportRow.style.setProperty('display', 'none', 'important');
            } else {
                elRincianTransportRow.style.setProperty('display', 'flex', 'important');
                if (currentRoutingDistance > 0) {
                    elRincianTransport.textContent = formatRupiah(globalBiayaJarak);
                } else {
                    elRincianTransport.textContent = 'Belum dihitung';
                }
            }
        }
        
        if (elRincianWaktuRow) elRincianWaktuRow.style.setProperty('display', 'none', 'important');

        // Update DP Info Section
        const dpSection = document.getElementById('dp-info-section');
        const dpBayarSekarang = document.getElementById('dp-bayar-sekarang');
        const dpSisaPelunasan = document.getElementById('dp-sisa-pelunasan');
        const tipePembayaranEl = document.querySelector('input[name="tipe_pembayaran"]:checked');
        
        if (dpSection && tipePembayaranEl) {
            if (tipePembayaranEl.value === 'DP') {
                const dpAmount = Math.ceil(grandTotal * 0.5);
                const sisaAmount = grandTotal - dpAmount;
                dpSection.style.display = 'block';
                if (dpBayarSekarang) dpBayarSekarang.textContent = formatRupiah(dpAmount);
                if (dpSisaPelunasan) dpSisaPelunasan.textContent = formatRupiah(sisaAmount);
            } else {
                dpSection.style.display = 'none';
            }
        }

        subtotalInput.value = subtotal;
    }

    const jadwalInputEl = document.getElementById('jadwalPemasangan');
    if (jadwalInputEl) {
        jadwalInputEl.addEventListener('change', function() {
            hitungSubtotal();
        });
    }

    // Payment Toggle (DP / Full) Event Listeners
    const paymentOptions = document.querySelectorAll('.payment-toggle-option');
    paymentOptions.forEach(function(option) {
        option.addEventListener('click', function() {
            paymentOptions.forEach(function(opt) {
                opt.classList.remove('active');
            });
            this.classList.add('active');
            hitungSubtotal();
        });
    });

    sizeButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            sizeButtons.forEach(function (item) {
                item.classList.remove('active');
            });

            this.classList.add('active');

            let hargaDasarBaru = parseFloat(this.dataset.harga || 0);
            if (document.getElementById('raw-harga-dasar')) {
                document.getElementById('raw-harga-dasar').value = hargaDasarBaru;
            }

            if (idDetailProduk) {
                idDetailProduk.value = this.dataset.idDetailProduk || '';
            }

            if (displayHarga) {
                displayHarga.textContent = formatRupiah(hargaDasarBaru);
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

    let initHarga = parseFloat(document.getElementById('raw-harga-dasar')?.value) || 0;
    if (displayHarga && initHarga > 0) {
        displayHarga.textContent = formatRupiah(initHarga);
    }

    hitungSubtotal();

    const pesanForm = document.getElementById('pesanForm');
    const togglePesan = document.getElementById('togglePesan');
    const btnBeliAwal = document.getElementById('btnBeliAwal');
    const actionBeforeForm = document.getElementById('actionBeforeForm');
    const mobileBackdrop = document.getElementById('mobileBackdrop');
    const checkoutForm = document.getElementById('checkoutForm');

    let mapInitialized = false;
    let map, marker, workshopMarker, routingControl;

    function initMap() {
        if (mapInitialized || !document.getElementById('map')) return;
        
        let initialLat = configBiaya.workshop_lat;
        let initialLon = configBiaya.workshop_lon;

        map = L.map('map', {
            fullscreenControl: true,
            fullscreenControlOptions: { position: 'topleft' }
        }).setView([initialLat, initialLon], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        map.on('enterFullscreen', function(){
            setTimeout(function(){
                map.invalidateSize();
            }, 200);
        });

        map.on('exitFullscreen', function(){
            setTimeout(function(){
                map.invalidateSize();
            }, 200);
        });

        // Marker Workshop (Origin)
        var workshopIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        workshopMarker = L.marker([initialLat, initialLon], {icon: workshopIcon}).addTo(map)
            .bindPopup('<b>Workshop Kami</b><br>Titik awal perusahaan.').openPopup();

        // Marker Klien (Tujuan)
        let startKlienLat = initialLat - 0.005;
        let startKlienLon = initialLon;
        marker = L.marker([startKlienLat, startKlienLon], {draggable: true}).addTo(map)
            .bindPopup('<b>Lokasi Anda</b><br>Geser pin ini.').openPopup();
        
        // Rute Jalan Raya (Routing)
        routingControl = L.Routing.control({
            waypoints: [
                L.latLng(initialLat, initialLon),
                L.latLng(startKlienLat, startKlienLon)
            ],
            createMarker: function() { return null; },
            addWaypoints: false,
            routeWhileDragging: true,
            show: false,
            lineOptions: {
                styles: [{color: '#007bff', weight: 4}]
            }
        }).addTo(map);

        routingControl.on('routesfound', function(e) {
            var routes = e.routes;
            var summary = routes[0].summary;
            currentRoutingDistance = summary.totalDistance / 1000;
            hitungSubtotal();
        });

        // Inisialisasi value
        document.getElementById('latitude').value = startKlienLat;
        document.getElementById('longitude').value = startKlienLon;

        marker.on('dragend', function (e) {
            updateMarkerAndLine(marker.getLatLng().lat, marker.getLatLng().lng);
        });

        let searchTimeoutId;
        const searchInput = document.getElementById('searchAlamat');
        const suggestionsBox = document.getElementById('search-suggestions');

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeoutId);
            const query = this.value;
            
            if(!query) {
                suggestionsBox.style.display = 'none';
                return;
            }

            searchTimeoutId = setTimeout(() => {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&countrycodes=id&limit=5`)
                .then(response => response.json())
                .then(data => {
                    suggestionsBox.innerHTML = '';
                    if(data.length > 0) {
                        data.forEach(item => {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.textContent = item.display_name;
                            btn.addEventListener('click', function() {
                                searchInput.value = item.display_name;
                                suggestionsBox.style.display = 'none';
                                const lat = parseFloat(item.lat);
                                const lon = parseFloat(item.lon);
                                map.setView([lat, lon], 15);
                                updateMarkerAndLine(lat, lon);
                                document.getElementById('alamat_lengkap').value = item.display_name;
                            });
                            suggestionsBox.appendChild(btn);
                        });
                        suggestionsBox.style.display = 'block';
                    } else {
                        suggestionsBox.style.display = 'none';
                    }
                });
            }, 500);
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.style.display = 'none';
            }
        });

        document.getElementById('btnSearchAlamat').addEventListener('click', function() {
            const query = searchInput.value;
            if(!query) return;
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&countrycodes=id&limit=1`)
            .then(response => response.json())
            .then(data => {
                if(data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    map.setView([lat, lon], 15);
                    updateMarkerAndLine(lat, lon);
                    document.getElementById('alamat_lengkap').value = data[0].display_name;
                    suggestionsBox.style.display = 'none';
                } else {
                    alert('Alamat tidak ditemukan');
                }
            });
        });

        mapInitialized = true;
        
        // Hitung jarak saat map pertama kali diload
        setTimeout(hitungSubtotal, 500);
    }

    function updateMarkerAndLine(lat, lon) {
        marker.setLatLng([lat, lon]);
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lon;
        
        if (routingControl) {
            routingControl.setWaypoints([
                L.latLng(configBiaya.workshop_lat, configBiaya.workshop_lon),
                L.latLng(lat, lon)
            ]);
        }
    }

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

        setTimeout(() => {
            initMap();
            if (map) map.invalidateSize();
        }, 300);
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