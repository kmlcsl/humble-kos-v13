@extends('layouts.pemilik.app')

@section('title', 'Manajemen Kosan')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Manajemen Kosan</li>
@endsection

@section('page-title', 'Manajemen Kosan Saya')

@section('page-actions')
    <a href="{{ route('pemilik.kosan.create') }}" class="btn btn-primary">
        <i class="fas fa-plus-circle me-2"></i>Tambah Kosan Baru
    </a>
@endsection

@section('content')
    <!-- Alert Info -->
    <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Informasi:</strong> Kosan yang Anda tambahkan akan melalui proses verifikasi oleh admin sebelum dapat
        ditampilkan kepada pengguna.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 col-6 mb-3">
            <div class="card border-left-primary shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center w-100">
                        <div class="grow me-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Kosan</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['total_kosan'] ?? 0 }}</div>
                            <div class="text-xs text-muted mt-1">Seluruh kosan Anda</div>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded ms-auto">
                            <i class="fas fa-home fs-4 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-6 mb-3">
            <div class="card border-left-success shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center w-100">
                        <div class="grow me-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Kosan Aktif</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['kosan_approved'] ?? 0 }}</div>
                            <div class="text-xs text-muted mt-1">Sudah disetujui admin</div>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded ms-auto">
                            <i class="fas fa-check-circle fs-4 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-6 mb-3">
            <div class="card border-left-warning shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center w-100">
                        <div class="grow me-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu Verifikasi</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['kosan_pending'] ?? 0 }}</div>
                            <div class="text-xs text-muted mt-1">Sedang ditinjau admin</div>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded ms-auto">
                            <i class="fas fa-clock fs-4 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-6 mb-3">
            <div class="card border-left-info shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center w-100">
                        <div class="grow me-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Kamar</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['total_kamar'] ?? 0 }}</div>
                            <div class="text-xs text-muted mt-1">{{ $stats['kamar_tersedia'] ?? 0 }} tersedia</div>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded ms-auto">
                            <i class="fas fa-bed fs-4 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-white"><i class="fas fa-filter me-2"></i>Filter & Pencarian</h5>
            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse"
                aria-expanded="true" aria-controls="filterCollapse" id="toggleFilterBtn">
                <i class="fas fa-chevron-up me-1"></i> <span id="filterBtnText">Sembunyikan</span>
            </button>
        </div>
        <div class="collapse show" id="filterCollapse">
            <div class="card-body">
                <form action="{{ route('pemilik.kosan.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label for="search" class="form-label small fw-bold">Cari Nama</label>
                        <input type="text" class="form-control" id="search" name="search"
                            placeholder="Cari..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label small fw-bold">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui
                            </option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="jenis_kos" class="form-label small fw-bold">Jenis</label>
                        <select class="form-select" id="jenis_kos" name="jenis_kos">
                            <option value="all" {{ request('jenis_kos') == 'all' ? 'selected' : '' }}>Semua
                            </option>
                            <option value="putra" {{ request('jenis_kos') == 'putra' ? 'selected' : '' }}>Putra</option>
                            <option value="putri" {{ request('jenis_kos') == 'putri' ? 'selected' : '' }}>Putri</option>
                            <option value="campur" {{ request('jenis_kos') == 'campur' ? 'selected' : '' }}>Campur
                            </option>
                        </select>
                    </div>
                    <div class="col-12 col-md-auto">
                        <div class="d-flex gap-2 flex-wrap flex-md-nowrap">
                            <button type="submit" class="btn btn-primary" title="Filter">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('pemilik.kosan.index') }}" class="btn btn-secondary" title="Reset">
                                <i class="fas fa-undo me-1"></i> Reset
                            </a>
                            <a href="{{ route('pemilik.kosan.export') }}{{ !empty(request()->all()) ? '?' . http_build_query(request()->all()) : '' }}"
                                class="btn btn-success" title="Export">
                                <i class="fas fa-file-excel me-1"></i> Export
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Kosan Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-white"><i class="fas fa-home me-2"></i>Daftar Kosan Saya</h5>
            <span class="text-white">Total: {{ $kosans->total() }} data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table data-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="60">#</th>
                            <th>Kosan</th>
                            <th>Lokasi</th>
                            <th>Rating</th>
                            <th>Kamar</th>
                            <th>Status Validasi</th>
                            <th width="140">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kosans as $index => $kosan)
                            <tr>
                                <td>{{ ($kosans->currentPage() - 1) * $kosans->perPage() + $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="kosan-image me-3">
                                            @if ($kosan->foto_kosan)
                                                <img src="{{ asset('storage/' . $kosan->foto_kosan) }}"
                                                    alt="{{ $kosan->nama_kosan }}" class="img-thumbnail" width="60"
                                                    onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'no-image\'><i class=\'fas fa-home text-muted\'></i></div>';">
                                            @else
                                                <div class="no-image">
                                                    <i class="fas fa-home text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="kosan-info">
                                            <h6 class="mb-0">{{ $kosan->nama_kosan }}</h6>
                                            <div class="small text-muted">
                                                <span
                                                    class="badge {{ $kosan->tipe_kosan == 'putra' ? 'bg-primary' : ($kosan->tipe_kosan == 'putri' ? 'bg-danger' : 'bg-success') }}">
                                                    {{ ucfirst($kosan->tipe_kosan) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="location-info">
                                        <div class="fw-semibold">{{ $kosan->kota }}</div>
                                        <div class="small text-muted">{{ Str::limit($kosan->alamat, 30) }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="rating-info text-center">
                                        <div class="fw-semibold">
                                            <i class="fas fa-star text-warning"></i>
                                            {{ number_format($kosan->rating_rata ?? 0, 1) }}
                                        </div>
                                        <div class="small text-muted">Rating</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <div class="fw-semibold">{{ $kosan->kamar_count ?? 0 }}</div>
                                        <div class="small text-muted">Kamar</div>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $kosan->status_validasi == 'approved' ? 'bg-success' : ($kosan->status_validasi == 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $kosan->status_validasi == 'approved' ? 'Disetujui' : ($kosan->status_validasi == 'pending' ? 'Menunggu Verifikasi' : 'Ditolak') }}
                                    </span>
                                    @if ($kosan->status_validasi == 'rejected')
                                        <div class="small text-danger mt-1">
                                            <i class="fas fa-info-circle"></i> Hubungi admin
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2" role="group">
                                        <a href="{{ route('pemilik.kosan.show', $kosan->kosan_id) }}"
                                            class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip"
                                            title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('pemilik.kosan.update', $kosan->kosan_id) }}"
                                            class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if ($kosan->status_validasi != 'approved')
                                            <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                data-id="{{ $kosan->kosan_id }}" data-name="{{ $kosan->nama_kosan }}"
                                                data-bs-toggle="tooltip" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                        <h5 class="text-dark">Belum Ada Kosan</h5>
                                        <p class="text-muted mb-4">Anda belum memiliki kosan yang terdaftar.<br>Mulai
                                            tambahkan kosan pertama Anda sekarang!</p>
                                        <a href="{{ route('pemilik.kosan.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus-circle me-1"></i> Tambah Kosan Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($kosans->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Menampilkan {{ $kosans->firstItem() ?? 0 }} - {{ $kosans->lastItem() ?? 0 }} dari
                        {{ $kosans->total() }} data
                    </div>
                    <div>
                        {{ $kosans->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus kosan <strong id="deleteKosanName"></strong>?</p>
                    <p class="text-danger small">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait
                        kosan ini.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Stats Card Styling */
        .text-xs {
            font-size: 0.75rem;
        }

        .font-weight-bold {
            font-weight: 700 !important;
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

        /* Button spacing and sizing */
        .gap-2 {
            gap: 0.5rem !important;
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

        .btn {
            min-width: auto;
            white-space: nowrap;
        }

        /* Kosan Image Styles */
        .kosan-image img {
            width: 60px;
            height: 60px;
            object-fit: cover;
        }

        .no-image {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .no-image i {
            font-size: 24px;
        }

        /* Empty State */
        .empty-state {
            padding: 60px 30px;
            text-align: center;
        }

        .empty-state i {
            opacity: 0.5;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .empty-state h5 {
            margin-top: 1rem;
            font-weight: 600;
        }

        .empty-state p {
            max-width: 500px;
            margin: 0 auto;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

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

            // Handle delete button
            const deleteButtons = document.querySelectorAll('.delete-btn');
            const deleteForm = document.getElementById('deleteForm');
            const deleteKosanName = document.getElementById('deleteKosanName');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const kosan_id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');

                    deleteForm.action = `/pemilik/kosan/${kosan_id}`;
                    deleteKosanName.textContent = name;

                    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                    deleteModal.show();
                });
            });
        });
    </script>
@endpush
