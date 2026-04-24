@extends('layouts.pemilik.app')

@section('title', 'Manajemen Kamar')

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Manajemen Kamar</li>
@endsection

@section('page-title', 'Manajemen Kamar')

@section('page-actions')
<div class="btn-group" role="group">
    <a href="{{ route('pemilik.kamar.create') }}" class="btn btn-primary">
        <i class="fas fa-plus-circle me-1"></i>Tambah Kamar Baru
    </a>
</div>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 col-6 mb-3">
        <div class="card border-left-primary shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Kamar</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['total_kamar'] ?? 0 }}</div>
                        <div class="text-xs text-muted mt-1">Seluruh data kamar</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-door-open fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6 mb-3">
        <div class="card border-left-success shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Kamar Tersedia</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['kamar_tersedia'] ?? 0 }}</div>
                        <div class="text-xs text-muted mt-1">Siap untuk booking</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6 mb-3">
        <div class="card border-left-warning shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Kamar Terisi</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['kamar_terisi'] ?? 0 }}</div>
                        <div class="text-xs text-muted mt-1">Sedang dihuni</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-bed fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6 mb-3">
        <div class="card border-left-danger shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pemeliharaan</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['kamar_pemeliharaan'] ?? 0 }}</div>
                        <div class="text-xs text-muted mt-1">Kamar dalam perbaikan</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tools fa-2x text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Search -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter & Pencarian</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('pemilik.kamar.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label for="search" class="form-label small fw-bold">Cari Nomor</label>
                <input type="text" class="form-control" id="search" name="nomor_kamar" value="{{ request('nomor_kamar') }}" placeholder="Nomor...">
            </div>

            <div class="col-md-2">
                <label for="id_kosan" class="form-label small fw-bold">Kosan</label>
                <select class="form-select" id="id_kosan" name="id_kosan">
                    <option value="">Semua Kosan</option>
                    @foreach($kosans as $kosan)
                        <option value="{{ $kosan->kosan_id }}" {{ request('id_kosan') == $kosan->kosan_id ? 'selected' : '' }}>
                            {{ $kosan->nama_kosan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="status" class="form-label small fw-bold">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="terisi" {{ request('status') == 'terisi' ? 'selected' : '' }}>Terisi</option>
                    <option value="pemeliharaan" {{ request('status') == 'pemeliharaan' ? 'selected' : '' }}>Pemeliharaan</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="sortBy" class="form-label small fw-bold">Urutkan</label>
                <select class="form-select" id="sortBy" name="sort">
                    <option value="nomor_kamar" {{ request('sort') == 'nomor_kamar' ? 'selected' : '' }}>Nomor</option>
                    <option value="harga_per_bulan" {{ request('sort') == 'harga_per_bulan' ? 'selected' : '' }}>Harga</option>
                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Baru</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="sortDirection" class="form-label small fw-bold">Arah</label>
                <select class="form-select" id="sortDirection" name="direction">
                    <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Menaik</option>
                    <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Menurun</option>
                </select>
            </div>

            <div class="col-12 col-md-auto">
                <div class="d-flex gap-2 flex-wrap flex-md-nowrap">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('pemilik.kamar.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-sync-alt me-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Kamar Table -->
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 text-white"><i class="fas fa-bed me-2"></i>Daftar Kamar</h5>
        <span class="text-white">Total: {{ $kamars->total() }} data</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table data-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th>Nomor Kamar</th>
                        <th>Kosan</th>
                        <th>Ukuran</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kamars as $index => $kamar)
                    <tr>
                        <td>{{ ($kamars->currentPage() - 1) * $kamars->perPage() + $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="kamar-image me-3">
                                    @if($kamar->fotoUtama)
                                    <img src="{{ asset('storage/' . $kamar->fotoUtama->path_gambar) }}" alt="Kamar {{ $kamar->nomor_kamar }}" class="img-thumbnail" width="60" height="60">
                                    @else
                                    <div class="no-image">
                                        <i class="fas fa-door-open text-muted"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="kamar-info">
                                    <h6 class="mb-0">{{ $kamar->nomor_kamar }}</h6>
                                    <div class="small text-muted">
                                        ID: {{ $kamar->kamar_id }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($kamar->kosan)
                                <a href="{{ route('pemilik.kamar.by-kosan', $kamar->kosan->kosan_id) }}" class="text-decoration-none">
                                    {{ $kamar->kosan->nama_kosan }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $kamar->ukuran_kamar }}</td>
                        <td>
                            <div class="price-info">
                                <div class="fw-semibold">Rp {{ number_format($kamar->harga_per_bulan, 0, ',', '.') }}</div>
                                <div class="small text-muted">Per bulan</div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="status-indicator {{ $kamar->status_kamar }}"></span>
                                <span class="ms-2 status-text {{ $kamar->status_kamar }}">
                                    {{ ucfirst($kamar->status_kamar) }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-2" role="group">
                                <a href="{{ route('pemilik.kamar.show', $kamar->kamar_id) }}" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('pemilik.kamar.update', $kamar->kamar_id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                    data-id="{{ $kamar->kamar_id }}"
                                    data-name="{{ $kamar->nomor_kamar }}"
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
                                <h5 class="text-dark">Tidak Ada Data Kamar</h5>
                                <p class="text-muted mb-4">Belum ada kamar yang ditambahkan atau hasil pencarian tidak ditemukan.<br>Silakan tambahkan kamar baru atau ubah filter pencarian.</p>
                                <a href="{{ route('pemilik.kamar.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-1"></i> Tambah Kamar Baru
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
                Menampilkan {{ $kamars->firstItem() ?? 0 }} - {{ $kamars->lastItem() ?? 0 }} dari {{ $kamars->total() }} data
            </div>
            <div>
                {{ $kamars->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kamar <strong id="delete-kamar-name"></strong>?</p>
                <p class="text-danger">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait kamar ini, termasuk foto.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="delete-form" action="" method="POST">
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

    /* Kamar list styles */
    .kamar-image {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .kamar-image img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
    }

    .no-image {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }

    .no-image i {
        font-size: 24px;
        color: #adb5bd;
    }

    /* Status indicators */
    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-indicator.tersedia {
        background-color: #10b981;
    }

    .status-indicator.terisi {
        background-color: #3b82f6;
    }

    .status-indicator.pemeliharaan {
        background-color: #f59e0b;
    }

    .status-text.tersedia {
        color: #10b981;
    }

    .status-text.terisi {
        color: #3b82f6;
    }

    .status-text.pemeliharaan {
        color: #f59e0b;
    }

    /* Empty state */
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
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete confirmation modal
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteForm = document.getElementById('delete-form');
        const deleteKamarName = document.getElementById('delete-kamar-name');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const kamarId = this.getAttribute('data-id');
                const kamarName = this.getAttribute('data-name');

                deleteForm.action = "{{ url('/admin/manajemen-kamar') }}" + "/" + kamarId;
                deleteKamarName.textContent = kamarName;

                const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            });
        });

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Filter form enhancements
        const filterForm = document.querySelector('form[action="{{ route('pemilik.kamar.index') }}"]');
        const kosanSelect = document.getElementById('id_kosan');

        if (filterForm && kosanSelect) {
            // Auto-submit when kosan changes
            kosanSelect.addEventListener('change', function() {
                filterForm.submit();
            });
        }
    });
</script>
@endpush
