@extends('layouts.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@600&family=Space+Grotesk:wght@400;600&display=swap');

:root {
  --st-ink: #1f2937;
  --st-muted: #6b7280;
  --st-accent: #ef6c00;
  --st-accent-2: #1c7c78;
  --st-paper: #fff7ed;
  --st-bg: #f5f7fb;
  --st-card: #ffffff;
  --st-border: #e5e7eb;
}

.status-page { font-family: "Space Grotesk", "Nunito", sans-serif; color: var(--st-ink); }
.status-hero {
  position: relative;
  padding: 26px 28px;
  border-radius: 18px;
  background:
    radial-gradient(120% 120% at 10% 0%, rgba(28,124,120,0.12), transparent 45%),
    linear-gradient(135deg, #fff2e5 0%, #e6f7f4 100%);
  border: 1px solid rgba(0,0,0,0.06);
  overflow: hidden;
  animation: fadeUp .5s ease both;
}
.status-hero:after {
  content: "";
  position: absolute;
  right: -70px;
  top: -60px;
  width: 220px;
  height: 220px;
  background: radial-gradient(circle at 30% 30%, rgba(239,108,0,0.28), transparent 60%);
}
.status-eyebrow { font-size: 12px; letter-spacing: .12em; text-transform: uppercase; color: #8b6b55; }
.status-title { font-family: "Fraunces", serif; font-size: 28px; margin: 4px 0 6px; }
.status-subtitle { margin: 0; color: #4b5563; max-width: 720px; }
.status-hero__meta { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 14px; }
.status-pill {
  background: #ffffff;
  border: 1px dashed rgba(0,0,0,0.12);
  border-radius: 999px;
  padding: 6px 12px;
  font-weight: 600;
}

.status-card {
  border: 0;
  border-radius: 16px;
  box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
  background: var(--st-card);
  animation: fadeUp .6s ease .08s both;
}
.status-card__header { background: #ffffff; border-bottom: 1px solid #eef2f7; }

.status-grid {
  display: grid;
  gap: 14px;
}
@media (min-width: 992px) {
  .status-grid {
    grid-template-columns: 1.2fr 1fr;
  }
}

.status-info {
  background: #fffdf8;
  border: 1px solid var(--st-border);
  border-radius: 14px;
  padding: 16px;
}
.status-info h6 { font-weight: 700; margin-bottom: 12px; }
.status-row { display: flex; justify-content: space-between; gap: 10px; font-size: 14px; margin-bottom: 8px; }
.status-row span { color: var(--st-muted); }
.status-row strong { font-weight: 600; }

.status-form {
  background: #ffffff;
  border: 1px solid var(--st-border);
  border-radius: 14px;
  padding: 16px;
}
.status-form label { font-weight: 600; font-size: 14px; }
.status-help { font-size: 12px; color: var(--st-muted); }

.status-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 999px;
  font-weight: 600;
  font-size: 12px;
  background: #eef2ff;
  color: #3730a3;
  border: 1px solid #c7d2fe;
}

.status-actions { display: flex; gap: 10px; flex-wrap: wrap; }

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}
@media (prefers-reduced-motion: reduce) {
  .status-hero, .status-card { animation: none; }
}
</style>

@php
  $statusNow = $pesanan->statusPesanan->nama_status_pesanan ?? '-';
  $kategori = $pesanan->detailProduk?->itemProduksi?->kategoriUsaha?->nama_kategori ?? '-';
  $item = $pesanan->detailProduk?->itemProduksi?->nama_item ?? '-';
@endphp

<div class="status-page">
  <div class="status-hero mb-4">
    <div class="status-eyebrow">Admin</div>
    <h1 class="status-title">Perbarui Status Pesanan</h1>
    <p class="status-subtitle">Pastikan status pesanan sesuai proses produksi dan hanya untuk kategori admin Anda.</p>
    <div class="status-hero__meta">
      <div class="status-pill">ID Pesanan: {{ $pesanan->id_pesanan }}</div>
      <div class="status-pill">Kategori: {{ $kategori }}</div>
      <div class="status-pill">Status Saat Ini: <span class="status-badge">{{ $statusNow }}</span></div>
    </div>
  </div>

  <div class="card status-card">
    <div class="card-header status-card__header d-flex align-items-center justify-content-between">
      <div>
        <h6 class="m-0">Detail Pesanan</h6>
        <small class="text-muted">Cek data sebelum memperbarui status</small>
      </div>
      <div class="status-actions">
        <a href="{{ route('admin.tampilPesanan') }}" class="btn btn-outline-secondary btn-sm">
          <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
      </div>
    </div>

    <div class="card-body">
      <div class="status-grid">
        <div class="status-info">
          <h6>Ringkasan Pesanan</h6>
          <div class="status-row">
            <span>Penerima</span>
            <strong>{{ $pesanan->nama_penerima }}</strong>
          </div>
          <div class="status-row">
            <span>No HP</span>
            <strong>{{ $pesanan->No_hp_penerima }}</strong>
          </div>
          <div class="status-row">
            <span>Tanggal Pesan</span>
            <strong>{{ optional($pesanan->tanggal_pesan)->format('d M Y') }}</strong>
          </div>
          <div class="status-row">
            <span>Total Harga</span>
            <strong>Rp {{ number_format($pesanan->total_harga ?? 0, 0, ',', '.') }}</strong>
          </div>
          <hr>
          <div class="status-row">
            <span>Produk</span>
            <strong>{{ $item }}</strong>
          </div>
          <div class="status-row">
            <span>Ukuran</span>
            <strong>{{ $pesanan->detailProduk->ukuran ?? '-' }}</strong>
          </div>
          <div class="status-row">
            <span>Harga Dasar</span>
            <strong>Rp {{ number_format($pesanan->detailProduk->harga_dasar ?? 0, 0, ',', '.') }}</strong>
          </div>
        </div>

        <div class="status-form">
          <h6>Update Status</h6>
          <p class="status-help">Hanya status yang valid dari tabel status_pesanan.</p>

          @if ($errors->any())
            <div class="alert alert-danger">
              <strong>Validasi gagal:</strong> mohon cek kembali input.
            </div>
          @endif

          <form method="POST" action="{{ route('admin.updateStatusPesanan', $pesanan) }}">
            @csrf
            @method('PATCH')

            <div class="form-group">
              <label for="id_status_pesanan">Status Pesanan</label>
              <select
                id="id_status_pesanan"
                name="id_status_pesanan"
                class="form-control @error('id_status_pesanan') is-invalid @enderror">
                <option value="">-- Pilih Status --</option>
                @foreach ($statusPesanan as $status)
                  <option value="{{ $status->id_status_pesanan }}"
                    {{ (int) old('id_status_pesanan', $pesanan->id_status_pesanan) === (int) $status->id_status_pesanan ? 'selected' : '' }}>
                    {{ $status->nama_status_pesanan }}
                  </option>
                @endforeach
              </select>
              @error('id_status_pesanan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-warning">
                <i class="fas fa-save mr-1"></i> Simpan Perubahan
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection