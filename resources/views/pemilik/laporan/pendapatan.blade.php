@extends('layouts.pemilik.app')

@section('title', 'Laporan Pendapatan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pemilik.laporan.index') }}">Laporan</a></li>
    <li class="breadcrumb-item active">Pendapatan</li>
@endsection

@section('page-title', 'Laporan Pendapatan')

@section('page-actions')
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('pemilik.laporan.export.pendapatan_excel', request()->query()) }}" class="btn btn-success">
            <i class="fas fa-file-excel me-1"></i> Export Excel
        </a>
        <a href="{{ route('pemilik.laporan.export.pendapatan_pdf', request()->query()) }}" class="btn btn-danger">
            <i class="fas fa-file-pdf me-1"></i> Export PDF
        </a>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Stat Cards -->
        <div class="row">
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pendapatan
                                    (Terfilter)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp
                                    {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-success"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Transaksi Sukses
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTransaksi }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-receipt fa-2x text-info"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="row mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Filter Laporan</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('pemilik.laporan.pendapatan') }}" method="GET">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="periode" class="form-label">Periode</label>
                                <select id="periode" name="periode" class="form-select">
                                    <option value="bulan" @if ($periode == 'bulan') selected @endif>Per Bulan
                                    </option>
                                    <option value="tahun" @if ($periode == 'tahun') selected @endif>Per Tahun
                                    </option>
                                    <option value="custom" @if ($periode == 'custom') selected @endif>Custom</option>
                                </select>
                            </div>
                            <div class="col-md-2" id="bulan-group">
                                <label for="bulan" class="form-label">Bulan</label>
                                <select id="bulan" name="bulan" class="form-select">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}"
                                            @if ($bulan == $m) selected @endif>
                                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2" id="tahun-group">
                                <label for="tahun" class="form-label">Tahun</label>
                                <select id="tahun" name="tahun" class="form-select">
                                    @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}"
                                            @if ($tahun == $y) selected @endif>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2" id="custom-group-start">
                                <label for="tanggal_dari" class="form-label">Dari Tanggal</label>
                                <input type="date" id="tanggal_dari" name="tanggal_dari" class="form-control"
                                    value="{{ $tanggalDari }}">
                            </div>
                            <div class="col-md-2" id="custom-group-end">
                                <label for="tanggal_sampai" class="form-label">Sampai Tanggal</label>
                                <input type="date" id="tanggal_sampai" name="tanggal_sampai" class="form-control"
                                    value="{{ $tanggalSampai }}">
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h6 class="m-0 font-weight-bold">Grafik Pendapatan per Bulan (Tahun {{ $tahun }})</h6>
                    </div>
                    <div class="card-body">
                        @if ($chartData)
                            <canvas id="revenueChart"></canvas>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-chart-bar fa-3x text-gray-300 mb-3"></i>
                                <p class="text-muted">Pilih filter 'Per Tahun' untuk melihat grafik bulanan.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h6 class="m-0 font-weight-bold">Rincian Pendapatan per Kosan</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="bg-primary-subtle">
                                    <tr>
                                        <th>Nama Kosan</th>
                                        <th class="text-end">Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendapatanPerKosan as $data)
                                        <tr>
                                            <td><strong>{{ $data['kosan']->nama_kosan }}</strong></td>
                                            <td class="text-end">Rp
                                                {{ number_format($data['total_pendapatan'], 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted py-4">Tidak ada data.</td>
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

@push('styles')
    <style>
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }
    </style>
@endpush


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const periodeSelect = document.getElementById('periode');
            const bulanGroup = document.getElementById('bulan-group');
            const tahunGroup = document.getElementById('tahun-group');
            const customGroupStart = document.getElementById('custom-group-start');
            const customGroupEnd = document.getElementById('custom-group-end');

            function toggleFilterInputs() {
                const selected = periodeSelect.value;
                bulanGroup.style.display = 'none';
                tahunGroup.style.display = 'none';
                customGroupStart.style.display = 'none';
                customGroupEnd.style.display = 'none';

                if (selected === 'bulan') {
                    bulanGroup.style.display = 'block';
                    tahunGroup.style.display = 'block';
                } else if (selected === 'tahun') {
                    tahunGroup.style.display = 'block';
                } else if (selected === 'custom') {
                    customGroupStart.style.display = 'block';
                    customGroupEnd.style.display = 'block';
                }
            }

            periodeSelect.addEventListener('change', toggleFilterInputs);
            toggleFilterInputs(); // Initial call

            @if ($chartData)
                const ctx = document.getElementById('revenueChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($chartData['labels']) !!},
                        datasets: [{
                            label: 'Pendapatan',
                            data: {!! json_encode($chartData['data']) !!},
                            backgroundColor: 'rgba(78, 115, 223, 0.8)',
                            borderColor: 'rgba(78, 115, 223, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value, index, values) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('id-ID', {
                                                style: 'currency',
                                                currency: 'IDR'
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            @endif
        });
    </script>
@endpush
