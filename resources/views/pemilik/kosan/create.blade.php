@extends('layouts.pemilik.app')

@section('title', 'Tambah Kosan Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pemilik.kosan.index') }}">Manajemen Kosan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Kosan Baru</li>
@endsection

@section('page-title', 'Tambah Kosan Baru')

@section('page-actions')
    <a href="{{ route('pemilik.kosan.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
@endsection

@section('content')

    {{-- Info Alert --}}
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Informasi Penting:</strong> Kosan yang Anda tambahkan akan melalui proses verifikasi oleh admin terlebih
        dahulu sebelum dapat ditampilkan kepada pengguna. Mohon pastikan semua data yang diisi sudah benar.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5><i class="fas fa-exclamation-triangle me-2"></i>Ada kesalahan dalam form:</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('pemilik.kosan.store') }}" method="POST" enctype="multipart/form-data" id="createKosanForm">
        @csrf

        <div class="row">
            <!-- Form Panel Kiri -->
            <div class="col-lg-8">
                <!-- Informasi Umum -->
                <div class="admin-pemilik-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Informasi Umum</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="nama_kosan" class="form-label required">Nama Kosan</label>
                            <input type="text" class="form-control @error('nama_kosan') is-invalid @enderror"
                                id="nama_kosan" name="nama_kosan" value="{{ old('nama_kosan') }}" required>
                            @error('nama_kosan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label required">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5"
                                required>{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Deskripsikan kosan secara detail, termasuk keunggulan dan fasilitas
                                utama.</div>
                        </div>

                        <div class="mb-3">
                            <label for="peraturan" class="form-label">Peraturan Kosan</label>
                            <textarea class="form-control @error('peraturan') is-invalid @enderror" id="peraturan" name="peraturan" rows="4">{{ old('peraturan') }}</textarea>
                            @error('peraturan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Tulis peraturan kosan yang harus dipatuhi penghuni (opsional).</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipe_kosan" class="form-label required">Jenis Kosan</label>
                                <select class="form-select @error('tipe_kosan') is-invalid @enderror" id="tipe_kosan"
                                    name="tipe_kosan" required>
                                    <option value="">Pilih Jenis Kosan</option>
                                    <option value="putra" {{ old('tipe_kosan') == 'putra' ? 'selected' : '' }}>Kos Putra
                                    </option>
                                    <option value="putri" {{ old('tipe_kosan') == 'putri' ? 'selected' : '' }}>Kos Putri
                                    </option>
                                    <option value="campur" {{ old('tipe_kosan') == 'campur' ? 'selected' : '' }}>Kos Campur
                                    </option>
                                </select>
                                @error('tipe_kosan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="id_pemilik" class="form-label">Pemilik</label>
                                <select class="form-select @error('id_pemilik') is-invalid @enderror" id="id_pemilik"
                                    name="id_pemilik">
                                    <option value="">Pilih Pemilik (Opsional)</option>
                                    @foreach ($owners ?? [] as $owner)
                                        <option value="{{ optional($owner)->user_id }}"
                                            {{ old('id_pemilik') == optional($owner)->user_id ? 'selected' : '' }}>
                                            {{ optional($owner)->name }} ({{ optional($owner)->username }})</option>
                                    @endforeach
                                </select>
                                @error('id_pemilik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alamat -->
                <div class="admin-pemilik-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-map-marker-alt me-2"></i>Alamat Kosan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="alamat" class="form-label required">Alamat Lengkap</label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="2"
                                required>{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kota" class="form-label required">Kota</label>
                            <input type="text" class="form-control @error('kota') is-invalid @enderror" id="kota"
                                name="kota" value="{{ old('kota') }}" required>
                            @error('kota')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" class="form-control @error('latitude') is-invalid @enderror"
                                    id="latitude" name="latitude" value="{{ old('latitude') }}" readonly>
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" class="form-control @error('longitude') is-invalid @enderror"
                                    id="longitude" name="longitude" value="{{ old('longitude') }}" readonly>
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Lokasi di Peta</label>
                            <button type="button" class="btn btn-info btn-sm mb-2" id="get-current-location-btn">
                                <i class="fas fa-crosshairs me-1"></i> Gunakan Lokasi Saat Ini
                            </button>
                            <div id="map" style="height: 350px; border-radius: 8px; border: 1px solid #dee2e6;">
                            </div>
                            <div class="form-text mt-2">Klik pada peta untuk menandai lokasi kosan. Koordinat akan terisi
                                otomatis.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Kanan -->
            <div class="col-lg-4">
                <!-- Foto Kosan -->
                <div class="admin-pemilik-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-image me-2"></i>Foto Kosan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="foto_kosan" class="form-label">Foto Utama Kosan</label>
                            <input type="file" class="form-control @error('foto_kosan') is-invalid @enderror"
                                id="foto_kosan" name="foto_kosan" accept="image/*" onchange="previewMainImage(this)">
                            @error('foto_kosan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Format: JPG, JPEG, PNG. Max: 2MB. Foto ini akan menjadi tampilan utama kosan.</div>
                            <!-- Preview foto utama -->
                            <div id="preview-main-image" class="mt-2" style="display: none;">
                                <img id="main-image-preview" src="" alt="Preview" class="img-thumbnail" style="max-height: 200px; width: 100%; object-fit: cover;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="foto_tambahan" class="form-label">Foto Tambahan (Max 3)</label>
                            <input type="file" class="form-control @error('foto_tambahan.*') is-invalid @enderror"
                                id="foto_tambahan" name="foto_tambahan[]" accept="image/*" multiple onchange="previewAdditionalImages(this)">
                            @error('foto_tambahan.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Pilih maksimal 3 foto tambahan. Total maksimal 4 foto (1 utama + 3 tambahan).</div>
                            <!-- Preview foto tambahan -->
                            <div id="preview-additional-images" class="mt-2 row g-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Galeri Tambahan -->
                <div class="admin-pemilik-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-images me-2"></i>Galeri (Opsional)</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle me-1"></i>
                            Galeri dapat ditambahkan setelah kosan berhasil dibuat.
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="admin-pemilik-card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Simpan Kosan
                            </button>
                            <a href="{{ route('pemilik.kosan.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        </div>
                        <div class="mt-3 text-center small text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Status awal: <strong>Menunggu Verifikasi</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .required:after {
            content: " *";
            color: red;
        }

        #map {
            cursor: pointer;
        }

        .leaflet-popup-content {
            text-align: center;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Preview foto utama
        function previewMainImage(input) {
            const preview = document.getElementById('preview-main-image');
            const previewImg = document.getElementById('main-image-preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }

        // Preview foto tambahan
        function previewAdditionalImages(input) {
            const previewContainer = document.getElementById('preview-additional-images');
            previewContainer.innerHTML = '';

            if (input.files) {
                const files = Array.from(input.files).slice(0, 3); // Max 3 files
                files.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-4';
                        col.innerHTML = `
                            <div class="position-relative">
                                <img src="${e.target.result}" class="img-thumbnail" style="width: 100%; height: 100px; object-fit: cover;">
                                <span class="badge bg-info position-absolute top-0 end-0 m-1" style="font-size: 10px;">Foto ${index + 2}</span>
                            </div>
                        `;
                        previewContainer.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                });

                // Show warning if more than 3 files selected
                if (input.files.length > 3) {
                    alert('Maksimal 3 foto tambahan. Foto yang dipilih: ' + input.files.length + '. Hanya 3 foto pertama yang akan digunakan.');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Map
            let map = L.map('map').setView([-7.250445, 112.768845], 13); // Default: Surabaya
            let marker = null;

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Set marker on click
            map.on('click', function(e) {
                if (marker) {
                    map.removeLayer(marker);
                }

                marker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map);
                document.getElementById('latitude').value = e.latlng.lat.toFixed(8);
                document.getElementById('longitude').value = e.latlng.lng.toFixed(8);
            });

            // Get current location
            document.getElementById('get-current-location-btn').addEventListener('click', function() {
                if (navigator.geolocation) {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mendapatkan lokasi...';
                    const btn = this;

                    navigator.geolocation.getCurrentPosition(function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        map.setView([lat, lng], 15);

                        if (marker) {
                            map.removeLayer(marker);
                        }

                        marker = L.marker([lat, lng]).addTo(map);
                        document.getElementById('latitude').value = lat.toFixed(8);
                        document.getElementById('longitude').value = lng.toFixed(8);

                        btn.innerHTML = '<i class="fas fa-check me-1"></i> Lokasi Didapatkan';
                        setTimeout(() => {
                            btn.innerHTML =
                                '<i class="fas fa-crosshairs me-1"></i> Gunakan Lokasi Saat Ini';
                        }, 2000);
                    }, function(error) {
                        alert('Gagal mendapatkan lokasi: ' + error.message);
                        btn.innerHTML =
                            '<i class="fas fa-crosshairs me-1"></i> Gunakan Lokasi Saat Ini';
                    });
                } else {
                    alert('Browser Anda tidak mendukung geolocation');
                }
            });

            // Foto Preview
            const fotoInput = document.getElementById('foto_kosan');
            const fotoPreview = document.getElementById('foto-preview');
            const previewImg = document.getElementById('preview-img');
            const removeFotoBtn = document.getElementById('remove-foto-btn');

            fotoInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        fotoPreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });

            removeFotoBtn.addEventListener('click', function() {
                fotoInput.value = '';
                fotoPreview.style.display = 'none';
                previewImg.src = '';
            });

            // Form Validation
            document.getElementById('createKosanForm').addEventListener('submit', function(e) {
                const lat = document.getElementById('latitude').value;
                const lng = document.getElementById('longitude').value;

                if (!lat || !lng) {
                    e.preventDefault();
                    alert('Mohon tandai lokasi kosan pada peta terlebih dahulu.');
                    document.getElementById('map').scrollIntoView({
                        behavior: 'smooth'
                    });
                    return false;
                }
            });
        });
    </script>
@endpush
