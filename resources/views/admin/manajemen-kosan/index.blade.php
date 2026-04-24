@extends('layouts.admin.app')

@section('title', 'Manajemen Kosan')

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Manajemen Kosan</li>
@endsection

@section('page-title', 'Manajemen Kosan')

@section('page-actions')
<a href="{{ route('admin.manajemen-kosan.create') }}" class="btn btn-primary">
    <i class="fas fa-plus-circle me-2"></i>Tambah Kosan Baru
</a>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 col-6 mb-3">
        <div class="card border-left-primary shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center w-100">
                    <div class="grow me-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Kosan</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['total_kosan'] ?? 0 }}</div>
                        <div class="text-xs text-muted mt-1">Seluruh data kosan</div>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Kosan Disetujui</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['kosan_approved'] ?? 0 }}</div>
                        <div class="text-xs text-muted mt-1">Kosan dengan status approved</div>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Kamar Tersedia</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['kamar_tersedia'] ?? 0 }}</div>
                        <div class="text-xs text-muted mt-1">Dari {{ $stats['total_kamar'] ?? 0 }} total kamar</div>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded ms-auto">
                        <i class="fas fa-bed fs-4 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6 mb-3">
        <div class="card border-left-danger shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center w-100">
                    <div class="grow me-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pending</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['kosan_pending'] ?? 0 }}</div>
                        <div class="text-xs text-muted mt-1">Menunggu persetujuan</div>
                    </div>
                    <div class="bg-danger bg-opacity-10 p-3 rounded ms-auto">
                        <i class="fas fa-clock fs-4 text-danger"></i>
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
        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="true" aria-controls="filterCollapse" id="toggleFilterBtn">
            <i class="fas fa-chevron-up me-1"></i> <span id="filterBtnText">Sembunyikan</span>
        </button>
    </div>
    <div class="collapse show" id="filterCollapse">
        <div class="card-body">
            <form action="{{ route('admin.manajemen-kosan.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label for="search" class="form-label small fw-bold">Cari</label>
                    <input type="text" class="form-control form-control-sm" id="search" name="search" placeholder="Nama..." value="{{ request('search') }}">
                </div>
                <div class="col-md-1">
                    <label for="status" class="form-label small fw-bold">Status</label>
                    <select class="form-select form-select-sm" id="status" name="status">
                        <option value="all">Semua</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Ok</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Wait</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label for="jenis_kos" class="form-label small fw-bold">Jenis</label>
                    <select class="form-select form-select-sm" id="jenis_kos" name="jenis_kos">
                        <option value="all">All</option>
                        <option value="putra" {{ request('jenis_kos') == 'putra' ? 'selected' : '' }}>Pa</option>
                        <option value="putri" {{ request('jenis_kos') == 'putri' ? 'selected' : '' }}>Pi</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sort" class="form-label small fw-bold">Urut</label>
                    <select class="form-select form-select-sm" id="sort" name="sort">
                        <option value="created_at">Tgl</option>
                        <option value="nama_kosan">Nama</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label for="per_page" class="form-label small fw-bold">Show</label>
                    <select class="form-select form-select-sm" id="per_page" name="per_page">
                        <option value="10">10</option>
                        <option value="25">25</option>
                    </select>
                </div>
                <div class="col-md-auto d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary" title="Filter"><i class="fas fa-search"></i></button>
                    <a href="{{ route('admin.manajemen-kosan.index') }}" class="btn btn-sm btn-secondary" title="Reset"><i class="fas fa-undo"></i></a>
                    <a href="{{ route('admin.manajemenkosan.export') }}{{ !empty(request()->all()) ? '?' . http_build_query(request()->all()) : '' }}" class="btn btn-sm btn-success" title="Export"><i class="fas fa-file-excel"></i></a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Kosan Table -->
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 text-white"><i class="fas fa-home me-2"></i>Daftar Kosan</h5>
        <span class="text-white">Total: {{ $kosans->total() }} kosan</span>
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
                        <th>Pemilik</th>
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
                                    @if($kosan->foto_kosan)
                                    <img src="{{ asset('storage/' . $kosan->foto_kosan) }}"
                                         alt="{{ $kosan->nama_kosan }}"
                                         class="img-thumbnail"
                                         width="60"
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
                                        <span class="badge {{ $kosan->tipe_kosan == 'putra' ? 'bg-primary' : ($kosan->tipe_kosan == 'putri' ? 'bg-danger' : 'bg-success') }}">
                                            {{ ucfirst($kosan->tipe_kosan) }}
                                        </span>
                                        <span class="text-muted">ID: {{ $kosan->kosan_id }}</span>
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
                            <div class="rating-info">
                                <div class="fw-semibold">
                                    <i class="fas fa-star text-warning"></i>
                                    {{ number_format($kosan->rating_rata ?? 0, 1) }}
                                </div>
                                <div class="small text-muted">Rating</div>
                            </div>
                        </td>
                        <td>
                            <div class="owner-info">
                                <div class="fw-semibold">{{ $kosan->pemilik->nama_lengkap ?? '-' }}</div>
                                <div class="small text-muted">ID: {{ $kosan->owner_id ?? '-' }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input status-toggle" type="checkbox"
                                    data-id="{{ $kosan->kosan_id }}"
                                    data-url="{{ route('admin.manajemenkosan.toggle-status', $kosan->kosan_id) }}"
                                    {{ $kosan->status_validasi == 'approved' ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    <span class="badge {{ $kosan->status_validasi == 'approved' ? 'bg-success' : ($kosan->status_validasi == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                        {{ ucfirst($kosan->status_validasi ?? 'pending') }}
                                    </span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-2" role="group">
                                <a href="{{ route('admin.manajemen-kosan.show', $kosan->kosan_id) }}" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.manajemen-kosan.update', $kosan->kosan_id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                    data-id="{{ $kosan->kosan_id }}"
                                    data-name="{{ $kosan->nama_kosan }}"
                                    data-bs-toggle="tooltip"
                                    title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                <h5 class="text-dark">Tidak Ada Data Kosan</h5>
                                <p class="text-muted mb-4">Belum ada kosan yang ditambahkan atau hasil pencarian tidak ditemukan.<br>Silakan tambahkan kosan baru atau ubah filter pencarian.</p>
                                <a href="{{ route('admin.manajemen-kosan.create') }}" class="btn btn-primary">
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
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                Menampilkan {{ $kosans->firstItem() ?? 0 }} - {{ $kosans->lastItem() ?? 0 }} dari {{ $kosans->total() }} data kosan
            </div>
            <div>
                {{ $kosans->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>
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
                <p class="text-danger">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait kosan ini.</p>
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

    .btn {
        min-width: 100px;
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
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .empty-state h5 {
        margin-top: 1rem;
        font-weight: 600;
    }

    .empty-state p {
        max-width: 500px;
        margin: 0 auto;
    }

    /* Status Toggle Switch */
    .status-toggle {
        cursor: pointer;
        width: 3rem;
        height: 1.5rem;
    }

    .status-toggle:checked {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }

    .status-toggle:not(:checked) {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
    }

    .status-toggle:focus {
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    /* Table responsive fix */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Handle Filter Collapse Toggle
        const filterCollapse = document.getElementById('filterCollapse');
        const filterBtnText = document.getElementById('filterBtnText');
        const toggleFilterBtn = document.getElementById('toggleFilterBtn');

        if (filterCollapse && filterBtnText && toggleFilterBtn) {
            filterCollapse.addEventListener('show.bs.collapse', function () {
                filterBtnText.textContent = 'Sembunyikan';
                toggleFilterBtn.querySelector('i').className = 'fas fa-chevron-up me-1';
            });

            filterCollapse.addEventListener('hide.bs.collapse', function () {
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
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                deleteForm.action = `/admin/manajemenkosan/${id}`;
                deleteKosanName.textContent = name;

                const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            });
        });

        // Handle status toggle
        const statusToggles = document.querySelectorAll('.status-toggle');

        statusToggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const id = this.getAttribute('data-id');
                const url = this.getAttribute('data-url');

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Refresh the page or update the UI
                        location.reload();
                    } else {
                        // Revert the toggle if there was an error
                        this.checked = !this.checked;
                        alert('Gagal mengubah status: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.checked = !this.checked;
                    alert('Terjadi kesalahan saat mengubah status');
                });
            });
        });
    });
</script>
@endpush
