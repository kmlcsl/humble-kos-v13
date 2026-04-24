@extends('layouts.admin.app')

@section('title', 'Pembayaran Sukses')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.pembayaran.index') }}">Manajemen Pembayaran</a></li>
    <li class="breadcrumb-item active">Pembayaran Sukses</li>
@endsection

@section('page-title', 'Pembayaran Sukses')

@section('content')
    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 col-6 mb-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Sukses</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['total_successful']) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-6 mb-3">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sukses Hari Ini</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['successful_today'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-day fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-6 mb-3">
                <div class="card border-left-secondary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Sukses Kemarin
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['successful_yesterday'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-minus fa-2x text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-6 mb-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pendapatan Bulan Ini
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp
                                    {{ number_format($stats['revenue_this_month'], 0, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white"><i class="fas fa-filter me-2"></i>Filter Pembayaran Sukses</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.pembayaran.successful') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Metode Pembayaran</label>
                        <select name="metode" class="form-select">
                            <option value="all">Semua Metode</option>
                            <option value="dana" {{ request('metode') == 'dana' ? 'selected' : '' }}>DANA</option>
                            <option value="midtrans" {{ request('metode') == 'midtrans' ? 'selected' : '' }}>Midtrans
                            </option>
                            <option value="manual" {{ request('metode') == 'manual' ? 'selected' : '' }}>Manual</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="date_start" value="{{ request('date_start') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Tanggal Akhir</label>
                        <input type="date" class="form-control" name="date_end" value="{{ request('date_end') }}">
                    </div>
                    <div class="col-12 col-md-auto">
                        <div class="d-flex gap-2 flex-wrap flex-md-nowrap">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i> Filter</button>
                            <a href="{{ route('admin.pembayaran.successful') }}" class="btn btn-outline-secondary"><i
                                    class="fas fa-redo me-1"></i> Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold"><i class="fas fa-check-circle me-2"></i>Daftar Pembayaran Sukses
                        </h5>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.pembayaran.export.excel', request()->all()) }}"
                                class="btn btn-success">
                                <i class="fas fa-file-excel me-1"></i> Export Excel
                            </a>
                            <a href="{{ route('admin.pembayaran.export.pdf', request()->all()) }}" class="btn btn-danger">
                                <i class="fas fa-file-pdf me-1"></i> Export PDF
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Kode Booking</th>
                                        <th>Pengguna</th>
                                        <th>Metode</th>
                                        <th>Jumlah</th>
                                        <th>Tanggal Bayar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pembayaran as $payment)
                                        <tr>
                                            <td>{{ $payment->pembayaran_id }}</td>
                                            <td>{{ $payment->booking->kode_booking ?? '-' }}</td>
                                            <td>
                                                @if ($payment->booking && $payment->booking->user)
                                                    {{ $payment->booking->user->nama_lengkap }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $payment->method_display_name }}</td>
                                            <td>{{ $payment->formatted_jumlah }}</td>
                                            <td>{{ $payment->updated_at->format('d M Y, H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.pembayaran.show', $payment->pembayaran_id) }}"
                                                    class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                                                <h5 class="text-dark">Belum Ada Pembayaran Sukses</h5>
                                                <p class="text-muted">Tidak ada data pembayaran dengan status sukses.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($pembayaran->hasPages())
                        <div class="card-footer">
                            {{ $pembayaran->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Button Filter dan Reset dalam satu baris */
        .flex-md-nowrap {
            flex-wrap: wrap;
        }

        @media (min-width: 768px) {
            .flex-md-nowrap {
                flex-wrap: nowrap;
            }
        }

        /* Memastikan button alignment */
        .d-flex.gap-2 > * {
            height: fit-content;
        }

        /* Align button dengan form elements */
        .card-body .row.g-3 {
            align-items: flex-end;
        }
    </style>
@endpush
