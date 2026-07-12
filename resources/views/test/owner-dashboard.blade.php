@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-crown text-warning mr-2"></i>{{ $title }}
            </h1>
            <p class="text-muted small mb-0">Ringkasan bisnis secara menyeluruh — real time</p>
        </div>
        <div>
            <a href="{{ route('laporan.penjualan.index') }}" class="btn btn-primary btn-sm shadow-sm mr-1">
                <i class="fas fa-file-alt fa-sm text-white-50 mr-1"></i> Laporan Penjualan
            </a>
            <a href="{{ route('laporan.penjualan.excel') }}" class="btn btn-success btn-sm shadow-sm mr-1">
                <i class="fas fa-file-excel fa-sm text-white-50 mr-1"></i> Export Excel
            </a>
            <a href="{{ route('laporan.penjualan.pdf') }}" class="btn btn-danger btn-sm shadow-sm">
                <i class="fas fa-file-pdf fa-sm text-white-50 mr-1"></i> Export PDF
            </a>
        </div>
    </div>

    {{-- ===== ROW 1: KARTU RINGKASAN KPI ===== --}}
    <div class="row">

        {{-- Pendapatan Bulan Ini --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pendapatan Bulan Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp{{ number_format($totalPendapatanBulanIni, 0, ',', '.') }}
                            </div>
                            @php
                                $diff = $totalPendapatanBulanIni - $totalPendapatanBulanLalu;
                                $pct  = $totalPendapatanBulanLalu > 0
                                    ? round(($diff / $totalPendapatanBulanLalu) * 100, 1)
                                    : 0;
                            @endphp
                            <small class="{{ $diff >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-{{ $diff >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ abs($pct) }}% vs bulan lalu
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Pendapatan Keseluruhan --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pendapatan (All Time)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp{{ number_format($totalPendapatan, 0, ',', '.') }}
                            </div>
                            <small class="text-muted">AOV: Rp{{ number_format($avgOrderValue, 0, ',', '.') }}</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Pesanan --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Pesanan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalPesanan) }}</div>
                            <small class="text-muted">
                                ✅ {{ $totalPesananSelesai }} selesai &nbsp;|&nbsp; ❌ {{ $totalPesananBatal }} batal
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Klien --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Klien Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalKlienAktif) }}</div>
                            <small class="text-muted">{{ number_format($totalProdukAktif) }} produk aktif</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== ROW 2: GRAFIK PENJUALAN 12 BULAN ===== --}}
    <div class="row">
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area mr-1"></i>Tren Pendapatan — 12 Bulan Terakhir
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height:280px;">
                        <canvas id="chartPenjualan12Bulan"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pesanan Per Status (Donut) --}}
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie mr-1"></i>Distribusi Status Pesanan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2" style="height:220px;">
                        <canvas id="chartStatusPesanan"></canvas>
                    </div>
                    <div class="mt-3 small" id="legendStatusPesanan"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== ROW 3: PENDAPATAN PER KATEGORI + KLIEN BARU ===== --}}
    <div class="row">
        {{-- Pendapatan per Kategori --}}
        <div class="col-xl-5 col-lg-5 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tags mr-1"></i>Pendapatan per Kategori
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($pendapatanPerKategori as $kat)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small font-weight-bold">{{ $kat->nama_kategori }}</span>
                            <span class="small text-muted">Rp{{ number_format($kat->total_pendapatan, 0, ',', '.') }}</span>
                        </div>
                        @php
                            $maxPend = $pendapatanPerKategori->max('total_pendapatan');
                            $persen = $maxPend > 0 ? ($kat->total_pendapatan / $maxPend) * 100 : 0;
                        @endphp
                        <div class="progress" style="height:10px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ $persen }}%"
                                aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted">{{ $kat->total_produk }} produk aktif</small>
                    </div>
                    @empty
                    <p class="text-center text-muted">Belum ada data kategori.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Pertumbuhan Klien Baru --}}
        <div class="col-xl-4 col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-plus mr-1"></i>Klien Baru (6 Bulan)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="height:230px;">
                        <canvas id="chartKlienBaru"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Metode Pembayaran --}}
        <div class="col-xl-3 col-lg-3 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-credit-card mr-1"></i>Metode Pembayaran
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="small">Metode</th>
                                    <th class="small text-right">Jml</th>
                                    <th class="small text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($metodePembayaran as $m)
                                <tr>
                                    <td class="small font-weight-bold">{{ strtoupper($m->payment_type ?? '-') }}</td>
                                    <td class="small text-right">{{ $m->jumlah }}</td>
                                    <td class="small text-right text-success">Rp{{ number_format($m->total, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">Belum ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== ROW 4: PRODUK TERLARIS ===== --}}
    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-fire mr-1"></i>Produk Terlaris (Top 10)
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-borderless mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="small">#</th>
                                    <th class="small">Nama Produk</th>
                                    <th class="small">Kategori</th>
                                    <th class="small text-right">Terjual</th>
                                    <th class="small text-right">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produkTerlaris as $i => $p)
                                <tr>
                                    <td class="small align-middle">
                                        @if($i < 3)
                                            <span class="badge badge-{{ ['warning', 'secondary', 'danger'][$i] }}">{{ $i + 1 }}</span>
                                        @else
                                            {{ $i + 1 }}
                                        @endif
                                    </td>
                                    <td class="small font-weight-bold align-middle">{{ $p->nama_item }}</td>
                                    <td class="small align-middle">
                                        <span class="badge badge-info">{{ $p->nama_kategori }}</span>
                                    </td>
                                    <td class="small text-right align-middle">{{ number_format($p->total_terjual) }} pcs</td>
                                    <td class="small text-right align-middle text-success">
                                        Rp{{ number_format($p->total_pendapatan, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data produk terlaris.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5 Pesanan Terbaru --}}
        <div class="col-lg-5 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock mr-1"></i>5 Pesanan Terbaru
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="small">Klien</th>
                                    <th class="small">Status</th>
                                    <th class="small text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pesananTerbaru as $p)
                                <tr>
                                    <td class="small align-middle">
                                        {{ $p->pengguna?->nama_pengguna ?? '-' }}
                                        <br><span class="text-muted" style="font-size:10px;">{{ optional($p->created_at)->format('d M Y') }}</span>
                                    </td>
                                    <td class="small align-middle">
                                        <span class="badge badge-{{ match($p->statusPesanan?->nama_status_pesanan) {
                                            'Pesanan Selesai' => 'success',
                                            'Pesanan Dibatalkan' => 'danger',
                                            'Belum Bayar' => 'warning',
                                            default => 'info'
                                        } }}">
                                            {{ $p->statusPesanan?->nama_status_pesanan ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="small text-right align-middle text-success">
                                        Rp{{ number_format($p->total_harga, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/chart.js/Chart.min.js') }}"></script>

<script>
// ─── CHART 1: PENJUALAN 12 BULAN ─────────────────────────────────────────────
var ctxPenjualan = document.getElementById('chartPenjualan12Bulan');
var chartPenjualan = new Chart(ctxPenjualan, {
    type: 'line',
    data: {
        labels: {!! json_encode($labelBulanan) !!},
        datasets: [
            {
                label: 'Pendapatan (Rp)',
                lineTension: 0.3,
                backgroundColor: 'rgba(28, 200, 138, 0.08)',
                borderColor: 'rgba(28, 200, 138, 1)',
                pointRadius: 4,
                pointBackgroundColor: 'rgba(28, 200, 138, 1)',
                pointHoverRadius: 6,
                data: {!! json_encode($dataBulanan) !!},
                yAxisID: 'y-axis-1',
            },
            {
                label: 'Jumlah Transaksi',
                type: 'bar',
                backgroundColor: 'rgba(78, 115, 223, 0.3)',
                borderColor: 'rgba(78, 115, 223, 1)',
                data: {!! json_encode($dataTrxBulanan) !!},
                yAxisID: 'y-axis-2',
            }
        ],
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            xAxes: [{ gridLines: { display: false } }],
            yAxes: [
                {
                    id: 'y-axis-1',
                    type: 'linear',
                    position: 'left',
                    ticks: {
                        callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); },
                        maxTicksLimit: 5
                    }
                },
                {
                    id: 'y-axis-2',
                    type: 'linear',
                    position: 'right',
                    gridLines: { drawOnChartArea: false },
                    ticks: { maxTicksLimit: 5, stepSize: 1 }
                }
            ],
        },
        legend: { display: true },
        tooltips: {
            mode: 'index', intersect: false,
            callbacks: {
                label: function(item, data) {
                    var ds = data.datasets[item.datasetIndex];
                    if (item.datasetIndex === 0) return ds.label + ': Rp ' + item.yLabel.toLocaleString('id-ID');
                    return ds.label + ': ' + item.yLabel;
                }
            }
        }
    }
});

// ─── CHART 2: STATUS PESANAN (DONUT) ─────────────────────────────────────────
var statusLabels = {!! json_encode($pesananPerStatus->pluck('status')->values()) !!};
var statusData   = {!! json_encode($pesananPerStatus->pluck('jumlah')->values()) !!};
var poolWarna = ['#1cc88a', '#e74a3b', '#f6c23e', '#4e73df', '#36b9cc', '#858796'];
var statusColors = statusLabels.map(function(_, i) { return poolWarna[i % poolWarna.length]; });

var ctxStatus = document.getElementById('chartStatusPesanan');
new Chart(ctxStatus, {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{ data: statusData, backgroundColor: statusColors, hoverBorderColor: 'rgba(234,236,244,1)' }]
    },
    options: {
        maintainAspectRatio: false,
        legend: { display: false },
        cutoutPercentage: 65,
        tooltips: {
            backgroundColor: 'rgb(255,255,255)',
            bodyFontColor: '#858796',
            borderColor: '#dddfeb',
            borderWidth: 1,
        }
    }
});

// Custom legend
var legendHtml = '';
statusLabels.forEach(function(label, i) {
    legendHtml += '<span class="mr-2 d-inline-block text-nowrap"><i class="fas fa-circle" style="color:' + statusColors[i] + '"></i> ' + label + ' (' + statusData[i] + ')</span> ';
});
document.getElementById('legendStatusPesanan').innerHTML = legendHtml;

// ─── CHART 3: KLIEN BARU ─────────────────────────────────────────────────────
var ctxKlien = document.getElementById('chartKlienBaru');
new Chart(ctxKlien, {
    type: 'bar',
    data: {
        labels: {!! json_encode($labelKlienBaru) !!},
        datasets: [{
            label: 'Klien Baru',
            backgroundColor: 'rgba(78, 115, 223, 0.7)',
            borderColor: 'rgba(78, 115, 223, 1)',
            data: {!! json_encode($dataKlienBaru) !!},
        }]
    },
    options: {
        maintainAspectRatio: false,
        legend: { display: false },
        scales: {
            xAxes: [{ gridLines: { display: false } }],
            yAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }]
        }
    }
});
</script>
@endpush
