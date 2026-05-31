@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    </div>

    <div class="row">

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Admin</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($jumlahAdmin) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Klien (User)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($jumlahKlien) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Produk Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($jumlahProduk) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pendapatan (Bulan Ini)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp{{ number_format($totalPenjualanBulanIni, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">

        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Grafik Tren Penjualan (6 Bulan Terakhir)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="chartPenjualanSuperAdmin"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Sebaran Produk Per Kategori</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="chartKategoriSuperAdmin"></canvas>
                    </div>
                    <div class="text-center small mt-4" id="js-legend-container">
                        </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Produk Baru Ditambahkan</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-borderless mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-dark">Nama Produk</th>
                                    <th class="text-dark">Kategori</th>
                                    <th class="text-dark">Tgl Input</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produkTerbaru as $produk)
                                    <tr>
                                        <td class="font-weight-bold align-middle">
                                            {{ $produk->nama_item ?? '-' }}
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge badge-info px-2 py-1">
                                                {{ $produk->kategoriUsaha?->nama_kategori ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-muted small align-middle">
                                            {{ optional($produk->created_at)->format('d M Y') ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            Belum ada produk baru yang diinput.
                                        </td>
                                    </tr>
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
    // --- 1. SETTING CHART GRAFIK PENJUALAN ---
    var ctxPenjualan = document.getElementById("chartPenjualanSuperAdmin");
    var chartPenjualan = new Chart(ctxPenjualan, {
        type: 'line',
        data: {
            labels: {!! json_encode($labelPenjualan) !!},
            datasets: [{
                label: "Total Omset",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: {!! json_encode($dataPenjualan) !!},
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
            scales: {
                xAxes: [{ gridLines: { display: false, drawBorder: false } }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value, index, values) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    },
                    gridLines: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] }
                }],
            },
            legend: { display: false },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleFontColor: "#6e707e",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': Rp ' + tooltipItem.yLabel.toLocaleString('id-ID');
                    }
                }
            }
        }
    });

    // --- 2. SETTING CHART KATEGORI (DONUT) ---
    var ctxKategori = document.getElementById("chartKategoriSuperAdmin");
    var labelKategoriArray = {!! json_encode($labelKategori) !!};
    var dataKategoriArray = {!! json_encode($dataKategori) !!};

    // Generate random / preset warna agar bervariasi mengikuti jumlah kategori
    var poolWarna = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'];
    var warnaGaris = [];
    for(var k=0; k < labelKategoriArray.length; k++) {
        warnaGaris.push(poolWarna[k % poolWarna.length]);
    }

    var chartKategori = new Chart(ctxKategori, {
        type: 'doughnut',
        data: {
            labels: labelKategoriArray,
            datasets: [{
                data: dataKategoriArray,
                backgroundColor: warnaGaris,
                hoverBackgroundColor: warnaGaris,
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: true,
                caretPadding: 10
            },
            legend: { display: false },
            cutoutPercentage: 70,
        },
    });

    // Generate custom legend HTML di bawah chart donut agar rapi
    var legendContainer = document.getElementById("js-legend-container");
    var legendHtml = "";
    for (var i = 0; i < labelKategoriArray.length; i++) {
        legendHtml += `<span class="mr-2 d-inline-block text-nowrap"><i class="fas fa-circle" style="color:${warnaGaris[i]}"></i> ${labelKategoriArray[i]} (${dataKategoriArray[i]})</span> `;
    }
    legendContainer.innerHTML = legendHtml;
</script>
@endpush