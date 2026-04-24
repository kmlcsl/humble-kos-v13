@extends('layouts.admin.app')

@section('title', 'Pembayaran Menunggu Verifikasi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.pembayaran.index') }}">Manajemen Pembayaran</a></li>
    <li class="breadcrumb-item active">Menunggu Verifikasi</li>
@endsection

@section('page-title', 'Pembayaran Menunggu Verifikasi')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_processing'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-warning"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['processing_today'] }}</div>
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
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Kemarin</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['processing_yesterday'] }}</div>
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
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Bulan Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['processing_this_month'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold"><i class="fas fa-hourglass-half me-2"></i>Daftar Pembayaran Menunggu Verifikasi</h5>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.pembayaran.export.excel', array_merge(request()->all(), ['status' => 'pending'])) }}"
                            class="btn btn-success border border-white">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </a>
                        <a href="{{ route('admin.pembayaran.export.pdf', array_merge(request()->all(), ['status' => 'pending'])) }}" class="btn btn-danger border border-white">
                            <i class="fas fa-file-pdf me-1"></i> Export PDF
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($pembayaran->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Booking ID</th>
                                        <th>Pengguna</th>
                                        <th>Tipe</th>
                                        <th>Metode</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pembayaran as $index => $item)
                                    <tr>
                                        <td>{{ $pembayaran->firstItem() + $index }}</td>
                                        <td><strong>{{ $item->booking_id }}</strong></td>
                                        <td>{{ $item->booking->user->nama_lengkap ?? '-' }}</td>
                                        <td>
                                            @if($item->tipe_pembayaran == 'manual')
                                                <span class="badge bg-info">Manual</span>
                                            @else
                                                <span class="badge bg-primary">Gateway</span>
                                            @endif
                                        </td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $item->metode_pembayaran)) }}</td>
                                        <td><strong>Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</strong></td>
                                        <td>
                                            <span class="badge bg-warning text-dark">Menunggu Verifikasi</span>
                                        </td>
                                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.pembayaran.show', $item->pembayaran_id) }}"
                                               class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.pembayaran.approve', $item->pembayaran_id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success"
                                                        onclick="return confirm('Setujui pembayaran ini?')"
                                                        title="Setujui">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.pembayaran.reject', $item->pembayaran_id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Tolak pembayaran ini?')"
                                                        title="Tolak">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $pembayaran->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Tidak ada pembayaran yang menunggu verifikasi.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
