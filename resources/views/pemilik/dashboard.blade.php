@extends('layouts.pemilik.app')

@section('title', 'Dashboard Pemilik Kos')

@section('page-title', 'Dashboard')

@section('breadcrumb')

@section('content')
    <!-- Welcome Card -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-1">Selamat Datang, {{ Auth::user()->nama_lengkap ?? Auth::user()->name }}! 👋</h4>
                    <p class="text-muted mb-0">Berikut adalah ringkasan bisnis kos Anda hari ini.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('pemilik.kosan.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Kos Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Kos -->
        <div class="col-xl-3 col-md-6 col-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex align-items-center w-100">
                        <div class="grow me-2">
                            <p class="text-muted mb-1 small">Total Kos</p>
                            <h3 class="mb-0 fw-bold">{{ $totalKos }}</h3>
                            <small class="text-success">
                                <i class="fas fa-check-circle"></i> {{ $kosDisetujui }} Aktif
                            </small>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded ms-auto">
                            <i class="fas fa-building text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Kamar -->
        <div class="col-xl-3 col-md-6 col-6">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex align-items-center w-100">
                        <div class="grow me-2">
                            <p class="text-muted mb-1 small">Total Kamar</p>
                            <h3 class="mb-0 fw-bold">{{ $totalKamar }}</h3>
                            <small class="text-info">
                                <i class="fas fa-door-open"></i> {{ $kamarKosong }} Tersedia
                            </small>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded ms-auto">
                            <i class="fas fa-door-open text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tingkat Okupansi -->
        <div class="col-xl-3 col-md-6 col-6">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex align-items-center w-100">
                        <div class="grow me-2">
                            <p class="text-muted mb-1 small">Tingkat Okupansi</p>
                            <h3 class="mb-0 fw-bold">{{ $tingkatOkupansi }}%</h3>
                            <small class="text-warning">
                                <i class="fas fa-users"></i> {{ $kamarTerisi }}/{{ $totalKamar }} Terisi
                            </small>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded ms-auto">
                            <i class="fas fa-percentage text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="col-xl-3 col-md-6 col-6">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex align-items-center w-100">
                        <div class="grow me-2">
                            <p class="text-muted mb-1 small">Total Pendapatan</p>
                            <h3 class="mb-0 fw-bold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                            <small class="text-danger">
                                <i class="fas fa-arrow-up"></i> Bulan ini: Rp
                                {{ number_format($pendapatanBulanIni, 0, ',', '.') }}
                            </small>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded ms-auto">
                            <i class="fas fa-money-bill-wave text-danger fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking & Payment Stats -->
    <div class="row g-3 mb-4">
        <!-- Booking Stats -->
        <div class="col-xl-6">
            <div class="pemilik-card">
                <div class="pemilik-card-header">
                    <h6 class="mb-0 fw-bold">Statistik Booking</h6>
                    <a href="{{ route('pemilik.bookings.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="pemilik-card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="p-3 border rounded">
                                <h4 class="mb-0 text-primary">{{ $totalBooking }}</h4>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-3 border rounded">
                                <h4 class="mb-0 text-warning">{{ $bookingPending }}</h4>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-3 border rounded">
                                <h4 class="mb-0 text-success">{{ $bookingConfirmed }}</h4>
                                <small class="text-muted">Confirmed</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-3 border rounded">
                                <h4 class="mb-0 text-danger">{{ $bookingCancelled }}</h4>
                                <small class="text-muted">Cancelled</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Stats -->
        <div class="col-xl-6">
            <div class="pemilik-card">
                <div class="pemilik-card-header">
                    <h6 class="mb-0 fw-bold">Statistik Pembayaran</h6>
                    <a href="{{ route('pemilik.pembayaran.index') }}" class="btn btn-sm btn-outline-primary">Lihat
                        Semua</a>
                </div>
                <div class="pemilik-card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="p-3 border rounded">
                                <h4 class="mb-0 text-primary">{{ $totalPembayaran }}</h4>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-3 border rounded">
                                <h4 class="mb-0 text-warning">{{ $pembayaranPending }}</h4>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-3 border rounded">
                                <h4 class="mb-0 text-success">{{ $pembayaranSukses }}</h4>
                                <small class="text-muted">Sukses</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-3 border rounded">
                                <h4 class="mb-0 text-danger">{{ $pembayaranGagal }}</h4>
                                <small class="text-muted">Gagal</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart & Status Validasi -->
    <div class="row g-3 mb-4">
        <!-- Revenue Chart -->
        <div class="col-xl-8">
            <div class="pemilik-card">
                <div class="pemilik-card-header">
                    <h6 class="mb-0 fw-bold">Grafik Pendapatan (6 Bulan Terakhir)</h6>
                </div>
                <div class="pemilik-card-body">
                    <canvas id="revenueChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Validasi & Quick Actions -->
        <div class="col-xl-4">
            <div class="pemilik-card">
                <div class="pemilik-card-header">
                    <h6 class="mb-0 fw-bold">Status Validasi Kos</h6>
                </div>
                <div class="pemilik-card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <i class="fas fa-clock text-warning me-2"></i>
                            <span>Menunggu Validasi</span>
                        </div>
                        <h5 class="mb-0">{{ $kosMenungguValidasi }}</h5>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Disetujui</span>
                        </div>
                        <h5 class="mb-0">{{ $kosDisetujui }}</h5>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-times-circle text-danger me-2"></i>
                            <span>Ditolak</span>
                        </div>
                        <h5 class="mb-0">{{ $kosDitolak }}</h5>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="pemilik-card mt-4">
                <div class="pemilik-card-header">
                    <h6 class="mb-0 fw-bold">Aksi Cepat</h6>
                </div>
                <div class="pemilik-card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('pemilik.kosan.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Tambah Kos
                        </a>
                        <a href="{{ route('pemilik.kamar.create') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-door-open me-2"></i>Tambah Kamar
                        </a>
                        <a href="{{ route('pemilik.laporan.index') }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-chart-bar me-2"></i>Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="pemilik-card">
        <div class="pemilik-card-header">
            <h6 class="mb-0 fw-bold">Booking Terbaru</h6>
            <a href="{{ route('pemilik.bookings.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="pemilik-card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kode Booking</th>
                            <th>Penyewa</th>
                            <th>Kos</th>
                            <th>Kamar</th>
                            <th>Check-in</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $booking)
                            <tr>
                                <td>
                                    <a href="{{ route('pemilik.bookings.show', $booking->booking_id) }}"
                                        class="text-decoration-none">
                                        {{ $booking->kode_booking }}
                                    </a>
                                </td>
                                <td>{{ $booking->pengguna->nama_lengkap ?? 'N/A' }}</td>
                                <td>{{ $booking->kamar->kosan->nama_kosan ?? 'N/A' }}</td>
                                <td>{{ $booking->kamar->nomor_kamar ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->tanggal_checkin)->format('d M Y') }}</td>
                                <td>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $booking->status_booking == 'pending' ? 'warning' : ($booking->status_booking == 'confirmed' ? 'success' : 'danger') }}">
                                        {{ $booking->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Belum ada booking
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Custom Admin Card Styles */
        .pemilik-card {
            background-color: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            box-shadow: 0 0.1rem 1rem 0 rgba(58, 59, 69, 0.1);
            display: flex;
            flex-direction: column;
        }

        .pemilik-card-header {
            padding: 0.75rem 1.25rem;
            margin-bottom: 0;
            background-color: #e6e9ee;
            border-bottom: 1px solid #e3e6f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top-left-radius: 0.35rem;
            border-top-right-radius: 0.35rem;
        }

        .pemilik-card-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #4e73df;
        }

        .pemilik-card-body {
            padding: 1.25rem;
            flex: 1 1 auto;
        }

        .pemilik-card-footer {
            padding: 0.75rem 1.25rem;
            background-color: #f8f9fc;
            border-top: 1px solid #e3e6f0;
            border-bottom-left-radius: 0.35rem;
            border-bottom-right-radius: 0.35rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: @json($chartData['data']),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush
