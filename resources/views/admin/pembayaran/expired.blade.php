@extends('layouts.admin.app')

@section('title', 'Pembayaran Kadaluarsa')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.pembayaran.index') }}">Manajemen Pembayaran</a></li>
    <li class="breadcrumb-item active">Pembayaran Kadaluarsa</li>
@endsection

@section('page-title', 'Pembayaran Kadaluarsa')

@section('content')
    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 col-6 mb-3">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Kadaluarsa</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pembayaran->total() }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-6 mb-3">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Halaman Ini</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pembayaran->count() }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-list fa-2x text-warning"></i>
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
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Halaman</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pembayaran->currentPage() }} /
                                    {{ $pembayaran->lastPage() }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-alt fa-2x text-info"></i>
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
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Per Halaman</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pembayaran->perPage() }} data</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-database fa-2x text-secondary"></i>
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
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white">Daftar Pembayaran</h5>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.pembayaran.export.excel', request()->all()) }}"
                                class="btn btn-success border border-white">
                                <i class="fas fa-file-excel me-1"></i> Export Excel
                            </a>
                            <a href="{{ route('admin.pembayaran.export.pdf', request()->all()) }}"
                                class="btn btn-danger border border-white">
                                <i class="fas fa-file-pdf me-1"></i> Export PDF
                            </a>

                        </div>
                    </div>
                    <div class="card-body">
                        @if ($pembayaran->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" width="100%">
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pembayaran as $index => $item)
                                            <tr>
                                                <td>{{ $pembayaran->firstItem() + $index }}</td>
                                                <td><strong>{{ $item->booking_id }}</strong></td>
                                                <td>{{ $item->booking->user->nama_lengkap ?? '-' }}</td>
                                                <td>
                                                    @if ($item->tipe_pembayaran == 'manual')
                                                        <span class="badge bg-info">Manual</span>
                                                    @else
                                                        <span class="badge bg-primary">Gateway</span>
                                                    @endif
                                                </td>
                                                <td>{{ ucfirst(str_replace('_', ' ', $item->metode_pembayaran)) }}</td>
                                                <td><strong>Rp
                                                        {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</strong></td>
                                                <td>
                                                    <span class="badge bg-danger">Kadaluarsa</span>
                                                </td>
                                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
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
                                <i class="fas fa-info-circle me-2"></i>Tidak ada pembayaran yang kadaluarsa.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
