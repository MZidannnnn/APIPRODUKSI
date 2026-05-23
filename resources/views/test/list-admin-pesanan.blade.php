@extends('layouts.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@600&family=Space+Grotesk:wght@400;600&display=swap');

:root {
  --lp-ink: #1f2937;
  --lp-muted: #6b7280;
  --lp-accent: #ef6c00;
  --lp-accent-2: #1c7c78;
  --lp-card: #ffffff;
  --lp-border: #e5e7eb;
}

.lp-page { font-family: "Space Grotesk", "Nunito", sans-serif; color: var(--lp-ink); }
.lp-hero {
  position: relative;
  padding: 26px 28px;
  border-radius: 18px;
  background:
    radial-gradient(120% 120% at 10% 0%, rgba(28,124,120,0.12), transparent 45%),
    linear-gradient(135deg, #fff2e5 0%, #e6f7f4 100%);
  border: 1px solid rgba(0,0,0,0.06);
  overflow: hidden;
}
.lp-title { font-family: "Fraunces", serif; font-size: 28px; margin: 4px 0 6px; }
.lp-subtitle { margin: 0; color: #4b5563; }

.lp-card {
  border: 0;
  border-radius: 16px;
  box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
  background: var(--lp-card);
}

.lp-table thead th {
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .08em;
  color: #6b7280;
  border-top: 0;
}
.lp-row { cursor: pointer; }
.lp-row:hover { background: #fff7ed; }

.lp-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 999px;
  font-weight: 600;
  font-size: 12px;
  background: #eef2ff;
  color: #3730a3;
  border: 1px solid #c7d2fe;
}
.lp-mobile { border: 1px solid var(--lp-border); border-radius: 14px; }
.lp-mobile__row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px; }
</style>

<div class="lp-page">
  <div class="lp-hero mb-4">
    <div class="lp-title">Daftar Pesanan</div>
    <p class="lp-subtitle">Hanya pesanan dari kategori admin Anda yang ditampilkan.</p>
  </div>

  <div class="card lp-card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <div>
        <h6 class="m-0">Data Pesanan</h6>
        <small class="text-muted">Klik baris untuk mengubah status</small>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive d-none d-md-block">
        <table class="table table-hover mb-0 lp-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Penerima</th>
              <th>Produk</th>
              <th>Kategori</th>
              <th>Status</th>
              <th>Tanggal</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pesanan as $row)
              <tr class="lp-row"
                  data-href="{{ route('admin.editStatusPesanan', $row) }}"
                  role="button"
                  tabindex="0">
                <td>{{ $row->id_pesanan }}</td>
                <td>{{ $row->nama_penerima }}</td>
                <td>{{ $row->detailProduk?->itemProduksi?->nama_item ?? '-' }}</td>
                <td>{{ $row->detailProduk?->itemProduksi?->kategoriUsaha?->nama_kategori ?? '-' }}</td>
                <td><span class="lp-badge">{{ $row->statusPesanan?->nama_status_pesanan ?? '-' }}</span></td>
                <td>{{ optional($row->tanggal_pesan)->format('d M Y') }}</td>
                <td>Rp {{ number_format($row->total_harga ?? 0, 0, ',', '.') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">Belum ada pesanan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="d-md-none p-3">
        @forelse($pesanan as $row)
          <div class="lp-mobile card mb-3 lp-row" data-href="{{ route('admin.editStatusPesanan', $row) }}" role="button" tabindex="0">
            <div class="card-body">
              <div class="lp-mobile__row"><strong>ID</strong><span>{{ $row->id_pesanan }}</span></div>
              <div class="lp-mobile__row"><strong>Penerima</strong><span>{{ $row->nama_penerima }}</span></div>
              <div class="lp-mobile__row"><strong>Produk</strong><span>{{ $row->detailProduk?->itemProduksi?->nama_item ?? '-' }}</span></div>
              <div class="lp-mobile__row"><strong>Status</strong><span>{{ $row->statusPesanan?->nama_status_pesanan ?? '-' }}</span></div>
              <div class="lp-mobile__row"><strong>Total</strong><span>Rp {{ number_format($row->total_harga ?? 0, 0, ',', '.') }}</span></div>
            </div>
          </div>
        @empty
          <div class="text-center text-muted py-4">Belum ada pesanan.</div>
        @endforelse
      </div>

      <div class="p-3">
        {{ $pesanan->links() }}
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('click', function (e) {
  const row = e.target.closest('.lp-row');
  if (!row) return;
  const href = row.getAttribute('data-href');
  if (href) window.location.href = href;
});
document.addEventListener('keydown', function (e) {
  if (e.key !== 'Enter' && e.key !== ' ') return;
  const row = e.target.closest('.lp-row');
  if (!row) return;
  const href = row.getAttribute('data-href');
  if (href) window.location.href = href;
});
</script>
@endsection