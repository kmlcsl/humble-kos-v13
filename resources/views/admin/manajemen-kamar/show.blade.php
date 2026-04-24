@extends('layouts.admin.app')

@section('title', 'Detail Kamar')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.manajemen-kamar.index') }}">Manajemen Kamar</a></li>
<li class="breadcrumb-item active" aria-current="page">Detail Kamar</li>
@endsection

@section('page-title', 'Detail Kamar')

@section('page-actions')
<div class="d-flex justify-content-end gap-2" role="group">
    <a href="{{ route('admin.manajemen-kamar.update', $kamars->kamar_id) }}" class="btn btn-primary">
        <i class="fas fa-edit me-1"></i>Edit
    </a>
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
        <i class="fas fa-trash-alt me-1"></i>Hapus
    </button>
    <a href="{{ route('admin.manajemen-kamar.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Kolom Kiri - Detail Kamar -->
    <div class="col-xl-8">
        <!-- Galeri Foto -->
        <div class="admin-pemilik-card border-left-primary mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-images me-2"></i>Galeri Foto</h5>
            </div>
            <div class="card-body">
                <div class="kamar-gallery">
                    <div class="main-image mb-3">
                        @if ($kamars->foto_kamar)
                            <img id="main-display-image" src="{{ asset('storage/' . $kamars->foto_kamar) }}"
                                alt="Kamar {{ $kamars->nomor_kamar }}" class="img-fluid main-gallery-image rounded">
                        @else
                            <div class="no-image d-flex align-items-center justify-content-center">
                                <i class="fas fa-door-open me-2"></i> Tidak ada foto
                            </div>
                        @endif
                    </div>

                    @if ($kamars->fotoTambahan && $kamars->fotoTambahan->count() > 0)
                        <div class="thumbnail-gallery row g-2">
                            <div class="col-3">
                                <img src="{{ asset('storage/' . $kamars->foto_kamar) }}"
                                    class="img-fluid thumbnail-item active rounded cursor-pointer"
                                    onclick="changeImage(this, '{{ asset('storage/' . $kamars->foto_kamar) }}')"
                                    alt="Foto Utama">
                            </div>
                            @foreach ($kamars->fotoTambahan as $foto)
                                <div class="col-3">
                                    <img src="{{ asset('storage/' . $foto->path_foto) }}"
                                        class="img-fluid thumbnail-item rounded cursor-pointer"
                                        onclick="changeImage(this, '{{ asset('storage/' . $foto->path_foto) }}')"
                                        alt="Foto Kamar">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informasi Umum -->
        <div class="admin-pemilik-card border-left-success mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Kamar</h5>
                <div>
                    <span class="badge bg-primary text-white">
                        {{ ucfirst($kamars->status_kamar) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Nomor Kamar</dt>
                    <dd class="col-sm-8">{{ $kamars->nomor_kamar }}</dd>

                    <dt class="col-sm-4">Kosan</dt>
                    <dd class="col-sm-8">
                        @php
                            $kosanRel = $kamars->kosan ?? $kamars->kosanById;
                        @endphp
                        @if($kosanRel)
                            @php
                                $kosanId = $kosanRel->kosan_id ?? $kosanRel->id ?? null;
                                $kosanNama = $kosanRel->nama_kosan ?? $kosanRel->nama_kos ?? null;
                            @endphp
                            @if($kosanId && $kosanNama)
                                <a href="{{ route('admin.manajemen-kosan.show', $kosanId) }}" class="text-white">
                                    {{ $kosanNama }}
                                </a>
                            @elseif($kosanNama)
                                <span>{{ $kosanNama }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">Ukuran</dt>
                    <dd class="col-sm-8">{{ $kamars->ukuran_kamar }}</dd>

                    <dt class="col-sm-4">Tanggal Dibuat</dt>
                    <dd class="col-sm-8">{{ $kamars->created_at->format('d F Y, H:i') }}</dd>

                    <dt class="col-sm-4">Terakhir Diperbarui</dt>
                    <dd class="col-sm-8">{{ $kamars->updated_at->format('d F Y, H:i') }}</dd>

                    @if($kamars->deskripsi)
                    <dt class="col-sm-4">Deskripsi</dt>
                    <dd class="col-sm-8">{{ $kamars->deskripsi }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Harga Kamar -->
        <div class="admin-pemilik-card border-left-warning mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-dollar-sign me-2"></i>Informasi Harga</h5>
            </div>
            <div class="card-body">
                @php
                    $bulanan = $kamars->harga_per_kamar ?? $kamars->harga_per_bulan ?? 0;
                @endphp
                <dl class="row">
                    <dt class="col-sm-4">Harga Bulanan</dt>
                    <dd class="col-sm-8 fw-bold">Rp {{ number_format($bulanan, 0, ',', '.') }}</dd>

                    <dt class="col-sm-4">Harga Tahunan</dt>
                    <dd class="col-sm-8 fw-bold">Rp {{ number_format($bulanan * 12, 0, ',', '.') }}</dd>
                </dl>
            </div>
        </div>

        <!-- Fasilitas Kamar -->
        <div class="admin-pemilik-card border-left-info mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-couch me-2"></i>Fasilitas Kamar</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($kamars->fasilitas as $fasilitas)
                        <div class="col-md-4 col-6 mb-3">
                            <div class="facility-item active">
                                <i class="{{ $fasilitas->icon_fasilitas ?? 'fas fa-check' }} me-2"></i>{{ $fasilitas->nama_fasilitas }}
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted">Tidak ada fasilitas untuk kamar ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan - Sidebox -->
    <div class="col-xl-4">
        <!-- Status Kamar -->
        <div class="admin-pemilik-card border-left-secondary mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-toggle-on me-2"></i>Status Kamar</h5>
            </div>
            <div class="card-body">
                <div class="status-info mb-4">
                    <div class="current-status text-center">
                        <div class="status-badge status-{{ $kamars->status_kamar }}">
                            {{ ucfirst($kamars->status_kamar) }}
                        </div>
                        <p class="status-description mt-2">
                            @if($kamars->status_kamar == 'tersedia')
                                Kamar ini kosong dan siap untuk dipesan atau dihuni.
                            @elseif($kamars->status_kamar == 'terisi')
                                Kamar ini sedang dihuni dan tidak tersedia untuk dipesan.
                            @elseif($kamars->status_kamar == 'maintenance' || $kamars->status_kamar == 'pemeliharaan')
                                Kamar ini sedang dalam perbaikan atau pemeliharaan.
                            @endif
                        </p>
                    </div>
                </div>

                <form action="{{ route('admin.manajemen-kamar.change-status', $kamars->kamar_id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="status" class="form-label">Ubah Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="tersedia" {{ $kamars->status_kamar == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="terisi" {{ $kamars->status_kamar == 'terisi' ? 'selected' : '' }}>Terisi</option>
                            <option value="maintenance" {{ $kamars->status_kamar == 'maintenance' ? 'selected' : '' }}>Pemeliharaan</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-1"></i>Perbarui Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Tindakan -->
        <div class="admin-pemilik-card border-left-danger mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Tindakan</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <a href="{{ route('admin.manajemen-kamar.update', $kamars->kamar_id) }}" class="btn btn-primary w-100">
                            <i class="fas fa-edit me-1"></i>Edit Kamar
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash-alt me-1"></i>Hapus Kamar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kosan Info -->
        @if($kamars->kosan)
        <div class="admin-pemilik-card border-left-dark mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-building me-2"></i>Informasi Kosan</h5>
            </div>
            <div class="card-body">
                <div class="kosan-card">
                    @php
                        $kosanRel = $kamars->kosan ?? $kamars->kosanById;
                    @endphp
                    @if($kosanRel && $kosanRel->foto_kamar)
                        <img src="{{ asset('storage/' . $kosanRel->foto_kamar) }}" alt="{{ ($kosanRel->nama_kosan ?? $kosanRel->nama_kos) }}" class="img-fluid rounded mb-3">
                    @endif

                    <h5 class="kosan-name">{{ ($kosanRel->nama_kosan ?? $kosanRel->nama_kos) }}</h5>

                    <div class="kosan-badges mb-2">
                        @php
                            $val = $kosanRel->status_aktif ?? null;
                            $str = is_string($val) ? strtolower($val) : $val;
                            $aktif = ($str === 'aktif' || $str === 'active' || $str === 'ya' || $str === '1' || $val === 1 || $val === true);
                        @endphp
                        <span class="badge bg-primary">{{ ucfirst($kosanRel->jenis_kos) }}</span>
                        @if($kosanRel->kos_unggulan)
                            <span class="badge bg-warning text-dark">Unggulan</span>
                        @endif
                        <span class="badge {{ $aktif ? 'bg-success' : 'bg-danger' }}">
                            {{ $aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    @if($kosanRel)
                    <div class="kosan-location mb-3">
                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                        {{ $kosanRel->alamat }}, {{ $kosanRel->kecamatan }}, {{ $kosanRel->kota }}
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">Jumlah Kamar</div>
                        <div>{{ $kosanRel->jumlah_kamar }} ({{ $kosanRel->kamar_tersedia }} tersedia)</div>
                    </div>

                    @php
                        $kosanId = $kosanRel->kosan_id ?? $kosanRel->id ?? null;
                    @endphp
                    @if($kosanId)
                    <a href="{{ route('admin.manajemen-kosan.show', $kosanId) }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-building me-1"></i>Lihat Detail Kosan
                    </a>
                    @endif
                    @endif
                </div>
            </div>
        </div>
        @endif
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
                <p>Apakah Anda yakin ingin menghapus kamar <strong>{{ $kamars->nomor_kamar }}</strong>?</p>
                <p class="text-danger">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait kamar ini, termasuk foto.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.manajemen-kamar.delete', ['id' => $kamars->kamar_id]) }}" method="POST">
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
/* Gallery Styles */
.main-gallery-image {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 8px;
}

.no-image {
    width: 100%;
    height: 400px;
    background-color: #f8f9fa;
    border-radius: 8px;
    border: 1px dashed #dee2e6;
    color: #adb5bd;
    font-size: 18px;
}

.thumbnail-item {
    cursor: pointer;
    position: relative;
    transition: all 0.2s;
    border-radius: 8px;
    overflow: hidden;
}

.thumbnail-item img {
    width: 100%;
    height: 80px;
    object-fit: cover;
}

.thumbnail-item.active {
    border: 2px solid var(--primary);
}

.badge-main {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: var(--primary);
    color: white;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 4px;
}

/* Facility Styles */
.facility-item {
    padding: 10px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    font-size: 14px;
}

.facility-item.active {
    background-color: rgba(59, 130, 246, 0.1);
    color: var(--primary);
}

.facility-item.inactive {
    background-color: var(--gray-200);
    color: var(--gray-600);
}

/* Status Styles */
.bg-status-tersedia {
    background-color: #10b981;
}

.bg-status-terisi {
    background-color: #3b82f6;
}

.bg-status-pemeliharaan {
    background-color: #f59e0b;
}

.status-badge {
    display: inline-block;
    padding: 10px 20px;
    font-size: 18px;
    font-weight: 700;
    border-radius: 50px;
    color: white;
    margin-bottom: 10px;
}

.status-tersedia {
    background-color: #10b981;
}

.status-terisi {
    background-color: #3b82f6;
}

.status-pemeliharaan {
    background-color: #f59e0b;
}

.status-description {
    font-size: 14px;
    color: var(--gray-600);
}

/* Kosan card */
.kosan-card {
    font-size: 14px;
}

.kosan-name {
    font-weight: 600;
    margin-bottom: 10px;
}

.kosan-location {
    font-size: 13px;
    color: var(--gray-700);
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .main-gallery-image {
        height: 300px;
    }
}

@media (max-width: 768px) {
    .main-gallery-image {
        height: 250px;
    }

    .thumbnail-item img {
        height: 60px;
    }
}

</style>
@endpush

@push('scripts')
<script>
    function changeImage(element, src) {
        // Update main image
        document.getElementById('main-display-image').src = src;
        
        // Update active class for thumbnails
        const thumbnails = document.querySelectorAll('.thumbnail-item');
        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        element.classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Confirmation for delete
        const deleteForm = document.querySelector('#deleteModal form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                if (!confirm('Apakah Anda benar-benar yakin ingin menghapus kamar ini? Tindakan ini tidak dapat dibatalkan.')) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endpush
