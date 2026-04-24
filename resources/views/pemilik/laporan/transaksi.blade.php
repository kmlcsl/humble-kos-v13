@extends('layouts.pemilik.app')

@section('title', 'Riwayat Transaksi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pemilik.laporan.index') }}">Laporan</a></li>
    <li class="breadcrumb-item active">Transaksi</li>
@endsection

@section('page-title', 'Riwayat Transaksi')

@section('page-actions')
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('pemilik.laporan.export.transaksi_excel', request()->query()) }}" class="btn btn-success">
            <i class="fas fa-file-excel me-1"></i> Export Excel
        </a>
        <a href="{{ route('pemilik.laporan.export.transaksi_pdf', request()->query()) }}" class="btn btn-danger">
            <i class="fas fa-file-pdf me-1"></i> Export PDF
        </a>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Stat Cards -->
        <div class="row">
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Transaksi</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTransaksi }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-list fa-2x text-primary"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Transaksi Sukses
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $transaksiSukses }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-check fa-2x text-success"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="row mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Filter Transaksi</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('pemilik.laporan.transaksi') }}" method="GET">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Search</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="ID Booking/Transaksi..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="paid" @if (request('status') == 'paid') selected @endif>Sukses</option>
                                    <option value="pending" @if (request('status') == 'pending') selected @endif>Pending
                                    </option>
                                    <option value="failed" @if (request('status') == 'failed') selected @endif>Gagal</option>
                                    <option value="expired" @if (request('status') == 'expired') selected @endif>Kadaluarsa
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Dari Tanggal</label>
                                <input type="date" name="tanggal_dari" class="form-control"
                                    value="{{ request('tanggal_dari') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Sampai Tanggal</label>
                                <input type="date" name="tanggal_sampai" class="form-control"
                                    value="{{ request('tanggal_sampai') }}">
                            </div>
                            <div class="col-12 col-md-auto">
                                <div class="d-flex gap-2 flex-wrap flex-md-nowrap">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('pemilik.laporan.transaksi') }}"
                                        class="btn btn-outline-secondary text-nowrap">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Daftar Transaksi</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="bg-info-subtle">
                            <tr>
                                <th>Tanggal</th>
                                <th>Booking ID</th>
                                <th>Pengguna</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksiList as $transaksi)
                                <tr>
                                    <td>{{ $transaksi->created_at->format('d M Y, H:i') }}</td>
                                    <td>
                                        <strong>{{ $transaksi->booking->kode_booking ?? $transaksi->booking_id }}</strong>
                                    </td>
                                    <td>{{ $transaksi->booking->user->name ?? '-' }}</td>
                                    <td><span
                                            class="badge bg-secondary">{{ strtoupper($transaksi->metode_pembayaran) }}</span>
                                    </td>
                                    <td>
                                        @if ($transaksi->status_pembayaran == 'paid')
                                            <span class="badge bg-success">Sukses</span>
                                        @elseif($transaksi->status_pembayaran == 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($transaksi->status_pembayaran == 'failed')
                                            <span class="badge bg-danger">Gagal</span>
                                        @elseif($transaksi->status_pembayaran == 'expired')
                                            <span class="badge bg-dark">Kadaluarsa</span>
                                        @else
                                            <span
                                                class="badge bg-light text-dark">{{ $transaksi->status_pembayaran }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <h5>Tidak ada transaksi ditemukan.</h5>
                                        <p>Coba sesuaikan filter pencarian Anda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $transaksiList->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
