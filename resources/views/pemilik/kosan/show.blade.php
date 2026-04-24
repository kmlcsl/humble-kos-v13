@extends('layouts.pemilik.app')

@section('title', 'Detail Kosan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pemilik.kosan.index') }}">Manajemen Kosan</a></li>
<li class="breadcrumb-item active">Detail Kosan</li>
@endsection

@section('page-title', 'Detail Kosan')

@section('page-actions')
<a href="{{ route('pemilik.kosan.update', $kosans->kosan_id) }}" class="btn btn-primary me-2">
    <i class="fas fa-edit me-1"></i>Edit Kosan
</a>
<a href="{{ route('pemilik.kosan.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left me-1"></i>Kembali
</a>
@endsection

@section('content')
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
                        @if($kosans->foto_kosan)
                            <img src="{{ asset('storage/' . $kosans->foto_kosan) }}" alt="{{ $kosans->nama_kosan }}" class="img-fluid rounded mb-3">
                        @endif
                    </div>
                </div>

                <table class="table table-borderless">
                    <tr>
                        <th width="200">Nama Kosan</th>
                        <td>{{ $kosans->nama_kosan }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Kosan</th>
                        <td>
                            <span class="badge {{ $kosans->tipe_kosan == 'putra' ? 'bg-primary' : ($kosans->tipe_kosan == 'putri' ? 'bg-danger' : 'bg-success') }}">
                                {{ ucfirst($kosans->tipe_kosan) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $kosans->alamat }}</td>
                    </tr>
                    <tr>
                        <th>Kota</th>
                        <td>{{ $kosans->kota }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $kosans->deskripsi }}</td>
                    </tr>
                    <tr>
                        <th>Peraturan</th>
                        <td>{{ $kosans->peraturan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Rating</th>
                        <td>
                            <i class="fas fa-star text-warning"></i>
                            {{ number_format($kosans->rating_rata ?? 0, 1) }}
                        </td>
                    </tr>
                    <tr>
                        <th>Status Validasi</th>
                        <td>
                            <span class="badge {{ $kosans->status_validasi == 'approved' ? 'bg-success' : ($kosans->status_validasi == 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ $kosans->status_validasi == 'approved' ? 'Disetujui' : ($kosans->status_validasi == 'pending' ? 'Menunggu Verifikasi' : 'Ditolak') }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Peta Lokasi -->
        @if($kosans->latitude && $kosans->longitude)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0 text-white">Lokasi Kosan</h5>
            </div>
            <div class="card-body">
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
                    <strong>{{ $kosans->kamar_count ?? 0 }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Kamar Tersedia</span>
                    <strong class="text-success">{{ $kosans->kamar_tersedia ?? 0 }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Kamar Terisi</span>
                    <strong class="text-danger">{{ $kosans->kamar_terisi ?? 0 }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span>Total Booking</span>
                    <strong>{{ $kosans->booking_count ?? 0 }}</strong>
                </div>
            </div>
        </div>

        <!-- Aksi Cepat -->
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0 text-white">Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('pemilik.kamar.index', ['kosan_id' => $kosans->kosan_id]) }}" class="btn btn-info">
                        <i class="fas fa-door-open me-2"></i>Kelola Kamar
                    </a>
                    <a href="{{ route('pemilik.bookings.index', ['kosan_id' => $kosans->kosan_id]) }}" class="btn btn-success">
                        <i class="fas fa-calendar-check me-2"></i>Lihat Booking
                    </a>
                </div>
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
    @if($kosans->latitude && $kosans->longitude)
    document.addEventListener('DOMContentLoaded', function() {
        let map = L.map('map').setView([{{ $kosans->latitude }}, {{ $kosans->longitude }}], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([{{ $kosans->latitude }}, {{ $kosans->longitude }}])
            .addTo(map)
            .bindPopup('<strong>{{ $kosans->nama_kosan }}</strong><br>{{ $kosans->alamat }}')
            .openPopup();
    });
    @endif
</script>
@endpush
