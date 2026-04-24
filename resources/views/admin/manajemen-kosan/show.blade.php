@extends('layouts.admin.app')

@section('title', 'Detail Kosan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.manajemen-kosan.index') }}">Manajemen Kosan</a></li>
<li class="breadcrumb-item active" aria-current="page">Detail Kosan</li>
@endsection

@section('page-title', 'Detail Kosan')

@section('page-actions')
<div class="d-flex justify-content-end gap-2" role="group">
    <a href="{{ route('admin.manajemen-kosan.update', $kosan->kosan_id) }}" class="btn btn-primary">
        <i class="fas fa-edit me-1"></i>Edit
    </a>
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
        <i class="fas fa-trash-alt me-1"></i>Hapus
    </button>
    <a href="{{ route('admin.manajemen-kosan.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>
@endsection

@section('content')
<!-- Flash Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <div class="col-lg-8">
        <!-- Informasi Kosan -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0 text-white">Informasi Kosan</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        @if($kosan->foto_kosan)
                            <img src="{{ asset('storage/' . $kosan->foto_kosan) }}" alt="{{ $kosan->nama_kosan }}" class="img-fluid rounded mb-3">
                        @else
                            <div class="text-center py-5 bg-light rounded mb-3">
                                <i class="fas fa-image fa-3x text-muted"></i>
                                <p class="text-muted mt-2">Tidak ada foto</p>
                            </div>
                        @endif
                    </div>
                </div>

                <table class="table table-borderless">
                    <tr>
                        <th width="200">ID Kosan</th>
                        <td>{{ $kosan->kosan_id }}</td>
                    </tr>
                    <tr>
                        <th>Nama Kosan</th>
                        <td>{{ $kosan->nama_kosan }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Kosan</th>
                        <td>
                            <span class="badge {{ $kosan->tipe_kosan == 'putra' ? 'bg-primary' : ($kosan->tipe_kosan == 'putri' ? 'bg-danger' : 'bg-success') }}">
                                {{ ucfirst($kosan->tipe_kosan) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Pemilik</th>
                        <td>
                            @if($kosan->pemilik)
                                {{ $kosan->pemilik->name }}
                                <small class="text-muted">({{ $kosan->pemilik->email }})</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $kosan->alamat }}</td>
                    </tr>
                    <tr>
                        <th>Kota</th>
                        <td>{{ $kosan->kota }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $kosan->deskripsi }}</td>
                    </tr>
                    <tr>
                        <th>Peraturan</th>
                        <td>{{ $kosan->peraturan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Rating</th>
                        <td>
                            <i class="fas fa-star text-warning"></i>
                            {{ number_format($kosan->rating_rata ?? 0, 1) }}
                        </td>
                    </tr>
                    <tr>
                        <th>Status Validasi</th>
                        <td>
                            <span class="badge {{ $kosan->status_validasi == 'approved' ? 'bg-success' : ($kosan->status_validasi == 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ $kosan->status_validasi == 'approved' ? 'Disetujui' : ($kosan->status_validasi == 'pending' ? 'Menunggu Verifikasi' : 'Ditolak') }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>{{ $kosan->created_at->format('d F Y, H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir Diperbarui</th>
                        <td>{{ $kosan->updated_at->format('d F Y, H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Peta Lokasi -->
        @if($kosan->latitude && $kosan->longitude)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0 text-white">Lokasi Kosan</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Koordinat:</strong> {{ $kosan->latitude }}, {{ $kosan->longitude }}
                    <a href="https://www.google.com/maps?q={{ $kosan->latitude }},{{ $kosan->longitude }}" class="btn btn-sm btn-outline-primary ms-2" target="_blank">
                        <i class="fas fa-map-marker-alt me-1"></i>Buka di Google Maps
                    </a>
                </div>
                <div id="map" style="height: 400px; border-radius: 8px;"></div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Statistik -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0 text-white">Statistik</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Kamar</span>
                    <strong>{{ $kosan->kamars->count() ?? 0 }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Kamar Tersedia</span>
                    <strong class="text-success">{{ $kosan->kamars->where('status_kamar', 'tersedia')->count() ?? 0 }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Kamar Terisi</span>
                    <strong class="text-danger">{{ $kosan->kamars->where('status_kamar', 'terisi')->count() ?? 0 }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Booking</span>
                    <strong>{{ $bookingStats['total_bookings'] ?? 0 }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Booking Pending</span>
                    <strong class="text-warning">{{ $bookingStats['pending'] ?? 0 }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Booking Confirmed</span>
                    <strong class="text-success">{{ $bookingStats['confirmed'] ?? 0 }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Booking Completed</span>
                    <strong class="text-info">{{ $bookingStats['completed'] ?? 0 }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Ulasan</span>
                    <strong>{{ $reviewStats['total_reviews'] ?? 0 }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Rata-rata Rating</span>
                    <strong>
                        <i class="fas fa-star text-warning"></i>
                        {{ number_format($reviewStats['avg_rating'] ?? 0, 1) }}
                    </strong>
                </div>
            </div>
        </div>

        <!-- Aksi Validasi -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0 text-white">Aksi Validasi</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <form action="{{ route('admin.manajemenkosan.toggle-status', $kosan->kosan_id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn w-100 {{ $kosan->status_validasi == 'approved' ? 'btn-danger' : 'btn-success' }}">
                            <i class="fas {{ $kosan->status_validasi == 'approved' ? 'fa-times-circle' : 'fa-check-circle' }} me-2"></i>
                            {{ $kosan->status_validasi == 'approved' ? 'Tolak Kosan' : 'Setujui Kosan' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Aksi Cepat -->
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0 text-white">Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.manajemen-kamar.by-kosan', ['id' => $kosan->kosan_id]) }}" class="btn btn-info">
                        <i class="fas fa-door-open me-2"></i>Kelola Kamar
                    </a>
                    <a href="{{ route('admin.bookings.index', ['kosan_id' => $kosan->kosan_id]) }}" class="btn btn-success">
                        <i class="fas fa-calendar-check me-2"></i>Lihat Booking
                    </a>
                    <a href="{{ route('admin.manajemen-kosan.update', $kosan->kosan_id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Kosan
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash-alt me-2"></i>Hapus Kosan
                    </button>
                </div>
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
                <p>Apakah Anda yakin ingin menghapus kosan <strong>{{ $kosan->nama_kosan }}</strong>?</p>
                <p class="text-danger">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait kosan ini, termasuk foto, ulasan, dan booking.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.manajemenkosan.delete', $kosan->kosan_id) }}" method="POST">
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
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
/* Force white text in colored card headers */
.card-header.bg-primary .card-title,
.card-header.bg-success .card-title,
.card-header.bg-info .card-title,
.card-header.bg-warning .card-title,
.card-header.bg-danger .card-title,
.card-header.bg-secondary .card-title,
.card-header.bg-dark .card-title {
    color: #ffffff !important;
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    @if($kosan->latitude && $kosan->longitude)
    document.addEventListener('DOMContentLoaded', function() {
        let map = L.map('map').setView([{{ $kosan->latitude }}, {{ $kosan->longitude }}], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([{{ $kosan->latitude }}, {{ $kosan->longitude }}])
            .addTo(map)
            .bindPopup('<strong>{{ $kosan->nama_kosan }}</strong><br>{{ $kosan->alamat }}')
            .openPopup();
    });
    @endif

    // Handle delete confirmation
    const deleteForm = document.querySelector('#deleteModal form');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            // Additional confirmation is already handled by the modal
        });
    }
</script>
@endpush
