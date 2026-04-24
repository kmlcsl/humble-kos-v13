@extends('layouts.admin.app')

@section('title', 'Tambah Kosan Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.manajemen-kosan.index') }}">Manajemen Kosan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Kosan Baru</li>
@endsection

@section('page-title', 'Tambah Kosan Baru')

@section('page-actions')
    <a href="{{ route('admin.manajemen-kosan.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
@endsection

@section('content')

    {{-- Display All Validation Errors --}}
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

    {{-- Display Success Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Display Error Message --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.manajemen-kosan.store') }}" method="POST" enctype="multipart/form-data"
        id="createKosanForm">
        @csrf

        <div class="row">
            <!-- Form Panel Kiri -->
            <div class="col-lg-8">
                <!-- Informasi Umum Kosan -->
                <div class="admin-pemilik-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Informasi Umum</h5>
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
                            <div class="form-text">Deskripsikan kosan secara detail, termasuk keunggulan dan informasi
                                penting lainnya.</div>
                        </div>

                        <div class="mb-3">
                            <label for="peraturan" class="form-label">Peraturan Kosan</label>
                            <textarea class="form-control @error('peraturan') is-invalid @enderror" id="peraturan" name="peraturan" rows="4">{{ old('peraturan') }}</textarea>
                            @error('peraturan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Tulis peraturan kosan yang harus dipatuhi penghuni.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jenis_kos" class="form-label required">Jenis Kosan</label>
                                <select class="form-select @error('jenis_kos') is-invalid @enderror" id="jenis_kos"
                                    name="jenis_kos" required>
                                    <option value="">Pilih Jenis Kosan</option>
                                    <option value="putra" {{ old('jenis_kos') == 'putra' ? 'selected' : '' }}>Kos Putra
                                    </option>
                                    <option value="putri" {{ old('jenis_kos') == 'putri' ? 'selected' : '' }}>Kos Putri
                                    </option>
                                    <option value="campur" {{ old('jenis_kos') == 'campur' ? 'selected' : '' }}>Kos Campur
                                    </option>
                                </select>
                                @error('jenis_kos')
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

                <!-- Alamat Kosan -->
                <div class="admin-pemilik-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Alamat Kosan</h5>
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
                                    id="latitude" name="latitude" value="{{ old('latitude') }}">
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" class="form-control @error('longitude') is-invalid @enderror"
                                    id="longitude" name="longitude" value="{{ old('longitude') }}">
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Peta Lokasi</label>
                            <button type="button" class="btn btn-info btn-sm mb-2" id="get-current-location-btn">
                                <i class="fas fa-crosshairs me-1"></i> Dapatkan Lokasi Saat Ini
                            </button>
                            <div id="map" style="height: 300px; border-radius: 8px;"></div>
                            <div class="form-text">Klik pada peta untuk menentukan lokasi kosan. Koordinat akan terisi
                                otomatis.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Panel Kanan -->
            <div class="col-lg-4">
                <!-- Foto Kosan -->
                <div class="admin-pemilik-card">
                    <div class="card-header">
                        <h5 class="card-title">Foto Kosan</h5>
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

                    <!-- Kartu Publikasi -->
                <div class="admin-pemilik-card">
                    <div class="card-header">
                        <h5 class="card-title">Publikasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="status_validasi" class="form-label">Status Validasi</label>
                            <select class="form-select @error('status_validasi') is-invalid @enderror"
                                id="status_validasi" name="status_validasi">
                                <option value="pending"
                                    {{ old('status_validasi', 'pending') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="approved" {{ old('status_validasi') == 'approved' ? 'selected' : '' }}>
                                    Disetujui</option>
                                <option value="rejected" {{ old('status_validasi') == 'rejected' ? 'selected' : '' }}>
                                    Ditolak</option>
                            </select>
                            @error('status_validasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Status validasi kosan oleh admin.</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-1"></i> Simpan Kosan
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

    <style>
        .required::after {
            content: ' *';
            color: red;
        }

        .preview-item {
            position: relative;
            margin-bottom: 15px;
        }

        .preview-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .preview-radio {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1;
        }

        .preview-remove {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(255, 255, 255, 0.8);
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #dc3545;
            z-index: 1;
        }

        .preview-remove:hover {
            background-color: rgba(255, 255, 255, 1);
            color: #bd2130;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

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
            const map = L.map('map').setView([-6.2088, 106.8456], 13); // Default to Jakarta
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            let marker;
            const latField = document.getElementById('latitude');
            const lngField = document.getElementById('longitude');
            const getLocationBtn = document.getElementById('get-current-location-btn');

            // Function to set marker and map view
            function setMapAndMarker(lat, lng, zoom = 15) {
                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker([lat, lng]).addTo(map);
                map.setView([lat, lng], zoom);
            }

            // Function to get current location and populate fields/map
            function getCurrentLocationAndPopulate(force = false) {
                if ((force || (!latField.value && !lngField.value)) && navigator.geolocation) {
                    if (getLocationBtn) getLocationBtn.disabled = true; // Disable button while fetching

                    navigator.geolocation.getCurrentPosition(function(position) {
                        const currentLat = position.coords.latitude;
                        const currentLng = position.coords.longitude;

                        latField.value = currentLat.toFixed(7);
                        lngField.value = currentLng.toFixed(7);
                        setMapAndMarker(currentLat, currentLng);
                        marker.bindPopup("Lokasi Anda Saat Ini").openPopup();
                        if (getLocationBtn) getLocationBtn.disabled = false;
                    }, function(error) {
                        console.warn('ERROR(' + error.code + '): ' + error.message);
                        let errorMessage = 'Gagal mendapatkan lokasi Anda.';
                        if (error.code === error.PERMISSION_DENIED) {
                            errorMessage =
                                'Anda menolak izin lokasi. Mohon izinkan akses lokasi di pengaturan browser.';
                        } else if (error.code === error.POSITION_UNAVAILABLE) {
                            errorMessage = 'Informasi lokasi tidak tersedia.';
                        } else if (error.code === error.TIMEOUT) {
                            errorMessage = 'Permintaan lokasi habis waktu. Coba lagi.';
                        }
                        alert(errorMessage);
                        if (getLocationBtn) getLocationBtn.disabled = false;
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000, // Increased timeout
                        maximumAge: 0
                    });
                } else if (!navigator.geolocation) {
                    console.warn("Geolocation is not supported by this browser.");
                    if (force) {
                        alert("Browser Anda tidak mendukung Geolocation API.");
                    }
                } else if (force) {
                    alert(
                        "Koordinat Latitude dan Longitude sudah terisi. Anda bisa menghapus nilai untuk mendapatkan lokasi baru."
                    );
                }
            }

            // --- Initial setup ---

            // If existing values, set marker and map view
            if (latField.value && lngField.value) {
                setMapAndMarker(parseFloat(latField.value), parseFloat(lngField.value));
            } else {
                // Initial auto-generate on page load if fields are empty
                getCurrentLocationAndPopulate();
            }

            // Event listener for the "Dapatkan Lokasi Saat Ini" button
            if (getLocationBtn) {
                getLocationBtn.addEventListener('click', function() {
                    getCurrentLocationAndPopulate(true); // Force update
                });
            }

            // Add marker on map click
            map.on('click', function(e) {
                latField.value = e.latlng.lat.toFixed(7);
                lngField.value = e.latlng.lng.toFixed(7);
                setMapAndMarker(e.latlng.lat, e.latlng.lng);
            });

            // Handle file upload preview
            const fileInput = document.getElementById('foto_kosan');
            const previewContainer = document.getElementById('preview-container');

            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    previewContainer.innerHTML = ''; // Clear previous previews

                    if (this.files.length > 0) {
                        const file = this.files[0];
                        const col = document.createElement('div');
                        col.className = 'col-12 mb-3';
                        const img = document.createElement('img');
                        img.className = 'img-thumbnail';
                        img.style.maxHeight = '200px';
                        img.src = URL.createObjectURL(file);
                        col.appendChild(img);
                        previewContainer.appendChild(col);
                    }
                });
            }
        });
    </script>
@endpush
