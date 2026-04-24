@extends('layouts.admin.app')

@section('title', 'Manajemen Fasilitas')

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Manajemen Fasilitas</li>
@endsection

@section('page-title', 'Manajemen Fasilitas')

@section('page-actions')
<a href="{{ route('admin.fasilitas.create') }}" class="btn btn-primary">
    <i class="fas fa-plus-circle me-2"></i>Tambah Fasilitas
</a>
@endsection

@section('content')

{{-- Display Messages --}}
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Fasilitas Table -->
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 text-white"><i class="fas fa-icons me-2"></i>Daftar Fasilitas</h5>
        <span class="text-white">Total: {{ $fasilitas->total() }} fasilitas</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th width="80">Icon</th>
                        <th>Nama Fasilitas</th>
                        <th>Deskripsi</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fasilitas as $index => $item)
                    <tr>
                        <td>{{ ($fasilitas->currentPage() - 1) * $fasilitas->perPage() + $index + 1 }}</td>
                        <td>
                            @if($item->icon_fasilitas)
                            <img src="{{ asset('storage/' . $item->icon_fasilitas) }}"
                                 alt="{{ $item->nama_fasilitas }}"
                                 class="img-thumbnail"
                                 width="50"
                                 style="max-height: 50px; object-fit: contain;"
                                 onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'text-center text-muted\'><i class=\'fas fa-image fa-2x\'></i></div>';">
                            @else
                            <div class="text-center text-muted">
                                <i class="fas fa-image fa-2x"></i>
                            </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $item->nama_fasilitas }}</strong>
                        </td>
                        <td>
                            {{ Str::limit($item->deskripsi, 100) ?: '-' }}
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.fasilitas.update', $item->fasilitas_id) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger delete-btn"
                                    data-id="{{ $item->fasilitas_id }}"
                                    data-name="{{ $item->nama_fasilitas }}"
                                    data-bs-toggle="tooltip"
                                    title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h6>Belum ada fasilitas</h6>
                                <p class="text-muted">Silakan tambahkan fasilitas baru untuk ditampilkan di sini.</p>
                                <a href="{{ route('admin.fasilitas.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-1"></i> Tambah Fasilitas
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
                Menampilkan {{ $fasilitas->firstItem() ?? 0 }} - {{ $fasilitas->lastItem() ?? 0 }} dari {{ $fasilitas->total() }} data
            </div>
            <div>
                {{ $fasilitas->links() }}
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
                <p>Apakah Anda yakin ingin menghapus fasilitas <strong id="deleteFasilitasName"></strong>?</p>
                <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
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
        /* .btn {
        min-width: 100px;
        white-space: nowrap;
    } */
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

    .empty-state h6 {
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
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Handle delete button
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteForm = document.getElementById('deleteForm');
        const deleteFasilitasName = document.getElementById('deleteFasilitasName');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                deleteForm.action = `/admin/fasilitas/${id}`;
                deleteFasilitasName.textContent = name;

                const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            });
        });
    });
</script>
@endpush
