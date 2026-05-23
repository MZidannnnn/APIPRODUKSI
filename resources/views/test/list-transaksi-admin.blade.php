@extends('layouts.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@600&family=Space+Grotesk:wght@400;600&display=swap');

:root {
  --trx-ink: #1f2937;
  --trx-accent: #ef6c00;
  --trx-accent-2: #1c7c78;
  --trx-paper: #fff7ed;
  --trx-bg: #f6f8fb;
}

.trx-page { font-family: "Space Grotesk", "Nunito", sans-serif; color: var(--trx-ink); }
.trx-hero {
  position: relative;
  padding: 26px 28px;
  border-radius: 18px;
  background: linear-gradient(135deg, #fff2e5 0%, #e6f7f4 100%);
  border: 1px solid rgba(0,0,0,0.06);
  overflow: hidden;
  animation: fadeUp .5s ease both;
}
.trx-hero:after {
  content: "";
  position: absolute;
  right: -60px;
  top: -50px;
  width: 200px;
  height: 200px;
  background: radial-gradient(circle at 30% 30%, rgba(239,108,0,0.28), transparent 60%);
}
.trx-eyebrow { font-size: 12px; letter-spacing: .12em; text-transform: uppercase; color: #8b6b55; }
.trx-title { font-family: "Fraunces", serif; font-size: 28px; margin: 4px 0 6px; }
.trx-subtitle { margin: 0; color: #4b5563; max-width: 620px; }
.trx-hero__stats { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 14px; }
.trx-pill {
  background: #ffffff;
  border: 1px dashed rgba(0,0,0,0.12);
  border-radius: 999px;
  padding: 6px 12px;
  font-weight: 600;
}

.trx-card {
  border: 0;
  border-radius: 16px;
  box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
  animation: fadeUp .6s ease .08s both;
}
.trx-card__header { background: #ffffff; border-bottom: 1px solid #eef2f7; }

.trx-table thead th {
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .08em;
  color: #6b7280;
  border-top: 0;
}
.trx-order { font-weight: 600; }
.trx-muted { font-size: 12px; color: #6b7280; }

.trx-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 999px;
  font-weight: 600;
  font-size: 12px;
}
.trx-badge--ok { background: #e6f4ea; color: #1b5e20; border: 1px solid #b7e1c0; }
.trx-badge--wait { background: #fff3cd; color: #7a5a00; border: 1px solid #ffe08a; }

.trx-mobile { border: 1px solid #eef2f7; border-radius: 14px; }
.trx-mobile__row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px; }

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

@php
  $total = $riwayatTransaksi->count();
  $totalLunas = $riwayatTransaksi->where('status_bayar', 'Lunas')->count();
  $totalPending = $riwayatTransaksi->where('status_bayar', 'Pending')->count();
  $totalNominal = $riwayatTransaksi->sum('jumlah_bayar');
@endphp

<div class="trx-page">
  <div class="trx-hero mb-4">
    <div class="trx-eyebrow">Admin</div>
    <h1 class="trx-title">Riwayat Transaksi</h1>
    <p class="trx-subtitle">Ringkasan pembayaran dan status transaksi untuk kategori admin Anda.</p>
    <div class="trx-hero__stats">
      <div class="trx-pill">Total: {{ $total }}</div>
      <div class="trx-pill">Lunas: {{ $totalLunas }}</div>
      <div class="trx-pill">Pending: {{ $totalPending }}</div>
      <div class="trx-pill">Nominal: Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
    </div>
  </div>

  <div class="card trx-card">
    <div class="card-header trx-card__header d-flex align-items-center justify-content-between">
      <div>
        <h6 class="m-0">Daftar Transaksi</h6>
        <small class="text-muted">Data terbaru muncul di atas</small>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive d-none d-md-block">
        <table class="table table-hover mb-0 trx-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Order</th>
              <th>Penerima</th>
              <th>Tipe</th>
              <th>Jumlah</th>
              <th>Metode</th>
              <th>Status</th>
              <th>Tanggal</th>
            </tr>
          </thead>
          <tbody>
            @forelse($riwayatTransaksi as $trx)
              @php
                $statusClass = $trx->status_bayar === 'Lunas' ? 'trx-badge--ok' : 'trx-badge--wait';
              @endphp
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                  <div class="trx-order">{{ $trx->order_id ?? '-' }}</div>
                  <div class="trx-muted">Pesanan: {{ $trx->id_pesanan }}</div>
                </td>
                <td>{{ $trx->pesanan->nama_penerima ?? '-' }}</td>
                <td>{{ $trx->tipe_pembayaran ?? '-' }}</td>
                <td>Rp {{ number_format($trx->jumlah_bayar ?? 0, 0, ',', '.') }}</td>
                <td>{{ $trx->payment_type ?? '-' }}</td>
                <td><span class="trx-badge {{ $statusClass }}">{{ $trx->status_bayar ?? '-' }}</span></td>
                <td>{{ optional($trx->created_at)->format('d M Y H:i') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-4">Belum ada transaksi.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="d-md-none p-3">
        @forelse($riwayatTransaksi as $trx)
          @php
            $statusClass = $trx->status_bayar === 'Lunas' ? 'trx-badge--ok' : 'trx-badge--wait';
          @endphp
          <div class="trx-mobile card mb-3">
            <div class="card-body">
              <div class="trx-mobile__row"><strong>Order</strong><span>{{ $trx->order_id ?? '-' }}</span></div>
              <div class="trx-mobile__row"><strong>Penerima</strong><span>{{ $trx->pesanan->nama_penerima ?? '-' }}</span></div>
              <div class="trx-mobile__row"><strong>Tipe</strong><span>{{ $trx->tipe_pembayaran ?? '-' }}</span></div>
              <div class="trx-mobile__row"><strong>Jumlah</strong><span>Rp {{ number_format($trx->jumlah_bayar ?? 0, 0, ',', '.') }}</span></div>
              <div class="trx-mobile__row"><strong>Status</strong><span class="trx-badge {{ $statusClass }}">{{ $trx->status_bayar ?? '-' }}</span></div>
              <div class="trx-mobile__row"><strong>Tanggal</strong><span>{{ optional($trx->created_at)->format('d M Y H:i') }}</span></div>
            </div>
          </div>
        @empty
          <div class="text-center text-muted py-4">Belum ada transaksi.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection