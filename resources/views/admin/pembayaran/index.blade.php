@extends('layouts.admin.app')

@section('title', 'Manajemen Pembayaran')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Manajemen Pembayaran</li>
@endsection

@section('page-title', 'Manajemen Pembayaran')

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 col-6 mb-3">
                <div class="card border-left-primary h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pembayaran
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['total_pembayaran']) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill-wave fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-6 mb-3">
                <div class="card border-left-success h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pembayaran Sukses
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['successful_pembayaran']) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-6 mb-3">
                <div class="card border-left-warning h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu Verifikasi
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['processing_pembayaran']) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-6 mb-3">
                <div class="card border-left-info h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Pendapatan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp
                                    {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white"><i class="fas fa-filter me-2"></i>Filter</h5>
                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse"
                    data-bs-target="#filterCollapse" aria-expanded="true" aria-controls="filterCollapse"
                    id="toggleFilterBtn">
                    <i class="fas fa-chevron-up me-1"></i><span id="filterBtnText">Sembunyikan</span>
                </button>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="card-body">
                    <form action="{{ route('admin.pembayaran.index') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Status</label>
                            <select name="status" class="form-select">
                                <option value="all">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>
                                    Menunggu
                                    Verifikasi</option>
                                <option value="successful" {{ request('status') == 'successful' ? 'selected' : '' }}>Sukses
                                </option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Kadaluarsa
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Metode Pembayaran</label>
                            <select name="metode" class="form-select">
                                <option value="all">Semua Metode</option>
                                <option value="dana" {{ request('metode') == 'dana' ? 'selected' : '' }}>DANA</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="date_start"
                                value="{{ request('date_start') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tanggal Akhir</label>
                            <input type="date" class="form-control" name="date_end" value="{{ request('date_end') }}">
                        </div>
                        <div class="col-12 col-md-auto">
                            <div class="d-flex gap-2 flex-wrap flex-md-nowrap">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>
                                    Filter</button>
                                <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-outline-secondary"><i
                                        class="fas fa-redo me-1"></i> Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Pembayaran Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white">Daftar Pembayaran</h5>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.pembayaran.export.excel', request()->all()) }}" class="btn btn-success border border-white">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </a>
                    <a href="{{ route('admin.pembayaran.export.pdf', request()->all()) }}" class="btn btn-danger border border-white">
                        <i class="fas fa-file-pdf me-1"></i> Export PDF
                    </a>
                </div>
            </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kode Pembayaran</th>
                                <th>Booking</th>
                                <th>User</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pembayaran as $payment)
                                <tr>
                                    <td>{{ $payment->pembayaran_id }}</td>
                                    <td>{{ $payment->transaction_id ?? $payment->kode_pembayaran }}</td>
                                    <td>
                                        @if ($payment->booking)
                                            <a href="{{ route('admin.pembayaran.show', $payment->pembayaran_id) }}">
                                                {{ $payment->booking->booking_id }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($payment->booking && $payment->booking->user)
                                            {{ $payment->booking->user->nama_lengkap }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $payment->formatted_jumlah }}</td>
                                    <td>{{ $payment->method_display_name }}</td>
                                    <td>
                                        {!! $payment->status_badge !!}
                                    </td>
                                    <td>{{ $payment->created_at->format('d M Y, H:i') }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('admin.pembayaran.show', $payment->pembayaran_id) }}"
                                                class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($payment->status_pembayaran == 'pending' && $payment->is_manual)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-success open-approve-modal"
                                                    data-approve-url="{{ route('admin.pembayaran.approve', $payment->pembayaran_id) }}"
                                                    data-payment-id="{{ $payment->pembayaran_id }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger open-reject-modal"
                                                    data-reject-url="{{ route('admin.pembayaran.reject', $payment->pembayaran_id) }}"
                                                    data-payment-id="{{ $payment->pembayaran_id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>

                                        <!-- Per-row modals dihapus: gunakan modal reusable di bawah -->
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                                            <h5>Tidak ada data pembayaran</h5>
                                            <p class="text-muted">Belum ada transaksi pembayaran yang tersedia.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        Menampilkan {{ $pembayaran->firstItem() ?? 0 }} - {{ $pembayaran->lastItem() ?? 0 }} dari
                        {{ $pembayaran->total() }} data
                    </div>
                    <div>
                        {{ $pembayaran->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table th,
        .table td {
            vertical-align: middle;
        }

        .pagination {
            margin-bottom: 0;
        }

        .badge {
            font-weight: 500;
        }

        /* Filter Collapse */
        #filterCollapse {
            transition: all 0.3s ease-in-out;
        }

        #filterCollapse .card-body {
            display: block !important;
            visibility: visible !important;
        }

        #filterCollapse.collapsing {
            overflow: hidden;
        }

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

@push('scripts')
    <script src="{{ asset('admin/js/status-pembayaran.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const approveModalEl = document.getElementById('approveModalGlobal');
            const rejectModalEl = document.getElementById('rejectModalGlobal');
            const approveForm = document.getElementById('approveForm');
            const rejectForm = document.getElementById('rejectForm');
            const approveModal = approveModalEl ? new bootstrap.Modal(approveModalEl, {
                backdrop: 'static',
                keyboard: false
            }) : null;
            const rejectModal = rejectModalEl ? new bootstrap.Modal(rejectModalEl, {
                backdrop: 'static',
                keyboard: false
            }) : null;

            // Handle Filter Collapse Toggle
            const filterCollapse = document.getElementById('filterCollapse');
            const filterBtnText = document.getElementById('filterBtnText');
            const toggleFilterBtn = document.getElementById('toggleFilterBtn');

            if (filterCollapse && filterBtnText && toggleFilterBtn) {
                filterCollapse.addEventListener('show.bs.collapse', function() {
                    filterBtnText.textContent = 'Sembunyikan';
                    toggleFilterBtn.querySelector('i').className = 'fas fa-chevron-up me-1';
                });

                filterCollapse.addEventListener('hide.bs.collapse', function() {
                    filterBtnText.textContent = 'Tampilkan';
                    toggleFilterBtn.querySelector('i').className = 'fas fa-chevron-down me-1';
                });
            }

            document.querySelectorAll('.open-approve-modal').forEach(btn => {
                btn.addEventListener('click', function() {
                    const url = this.getAttribute('data-approve-url');
                    if (approveForm && url) {
                        approveForm.setAttribute('action', url);
                        approveModal && approveModal.show();
                    }
                });
            });

            document.querySelectorAll('.open-reject-modal').forEach(btn => {
                btn.addEventListener('click', function() {
                    const url = this.getAttribute('data-reject-url');
                    if (rejectForm && url) {
                        rejectForm.setAttribute('action', url);
                        rejectModal && rejectModal.show();
                    }
                });
            });
        });
    </script>
@endpush

<!-- Approve Modal Reusable -->
<div class="modal fade" id="approveModalGlobal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Persetujuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menyetujui pembayaran ini?</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Pembayaran akan diubah statusnya menjadi "Sukses" dan
                    booking akan dikonfirmasi.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="approveForm" action="#" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success approve-btn">Setujui</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal Reusable -->
<div class="modal fade" id="rejectModalGlobal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Penolakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menolak pembayaran ini?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i> Pembayaran akan diubah statusnya menjadi "Gagal".
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="rejectForm" action="#" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger reject-btn">Tolak</button>
                </form>
            </div>
        </div>
    </div>
</div>
