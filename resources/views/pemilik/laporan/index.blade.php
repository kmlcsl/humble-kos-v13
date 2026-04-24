@extends('layouts.pemilik.app')

@section('title', 'Laporan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Laporan</li>
@endsection

@section('page-title', 'Laporan & Statistik')

@section('content')
<div class="container-fluid">
    <!-- Export Buttons -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-end">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('pemilik.laporan.export.excel') }}" class="btn btn-success">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </a>
                <a href="{{ route('pemilik.laporan.export.pdf') }}" class="btn btn-danger">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 col-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Booking</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBookings }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pendapatan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Kosan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalKosan }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-home fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Pengguna</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Status -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Status Booking</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="border-left-warning p-3">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $pendingBookings }}</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border-left-success p-3">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Dikonfirmasi</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $confirmedBookings }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border-left-danger p-3">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Dibatalkan</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $cancelledBookings }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border-left-info p-3">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pembayaran Pending</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $pendingPayments }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">Pendapatan 6 Bulan Terakhir</h6>
                </div>
                <div class="card-body">
                    @if($monthlyRevenue->count() > 0)
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyRevenue as $revenue)
                                <tr>
                                    <td>{{ \Carbon\Carbon::create($revenue->year, $revenue->month)->format('F Y') }}</td>
                                    <td class="text-end">Rp {{ number_format($revenue->total, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">Belum ada data pendapatan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Booking Terbaru</h6>
                </div>
                <div class="card-body p-0">
                    @if($recentBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Pengguna</th>
                                        <th>Kosan</th>
                                        <th>Kamar</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBookings as $booking)
                                    <tr>
                                        <td><strong>{{ $booking->booking_id }}</strong></td>
                                        <td>{{ $booking->user->name ?? '-' }}</td>
                                        <td>{{ $booking->kosan->nama_kosan ?? '-' }}</td>
                                        <td>{{ $booking->kamar->nomor_kamar ?? '-' }}</td>
                                        <td>
                                            @if($booking->status_booking == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($booking->status_booking == 'confirmed')
                                                <span class="badge bg-success">Dikonfirmasi</span>
                                            @else
                                                <span class="badge bg-danger">Dibatalkan</span>
                                            @endif
                                        </td>
                                        <td>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                                        <td>{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Belum ada booking terbaru.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
