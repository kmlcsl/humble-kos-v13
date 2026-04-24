@extends('layouts.admin.app')

@section('title', 'Edit Kosan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pemilik.kosan.index') }}">Manajemen Kosan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Kosan</li>
@endsection

@section('page-title', 'Edit Kosan')

@section('page-actions')
    <div class="btn-group" role="group">
        <a href="{{ route('pemilik.kosan.show', $kosan->kosan_id) }}" class="btn btn-info">
            <i class="fas fa-eye me-1"></i>Lihat Detail
        </a>
        <a href="{{ route('pemilik.kosan.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
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

    <form action="{{ route('pemilik.kosan.update', $kosan->kosan_id) }}" method="POST" enctype="multipart/form-data"
        id="editKosanForm">
        @csrf
        @method('PUT')

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
                                id="nama_kosan" name="nama_kosan" value="{{ old('nama_kosan', $kosan->nama_kosan) }}"
                                required>
                            @error('nama_kosan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label required">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5"
                                required>{{ old('deskripsi', $kosan->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Deskripsikan kosan secara detail, termasuk keunggulan dan informasi
                                penting lainnya.</div>
                        </div>

                        <div class="mb-3">
                            <label for="peraturan" class="form-label">Peraturan Kosan</label>
                            <textarea class="form-control @error('peraturan') is-invalid @enderror" id="peraturan" name="peraturan" rows="4">{{ old('peraturan', $kosan->peraturan) }}</textarea>
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
                                    <option value="putra"
                                        {{ old('jenis_kos', $kosan->tipe_kosan) == 'putra' ? 'selected' : '' }}>Kos Putra
                                    </option>
                                    <option value="putri"
                                        {{ old('jenis_kos', $kosan->tipe_kosan) == 'putri' ? 'selected' : '' }}>Kos Putri
                                    </option>
                                    <option value="campur"
                                        {{ old('jenis_kos', $kosan->tipe_kosan) == 'campur' ? 'selected' : '' }}>Kos Campur
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
                                            {{ old('id_pemilik', $kosan->owner_id) == optional($owner)->user_id ? 'selected' : '' }}>
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
                                required>{{ old('alamat', $kosan->alamat) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kota" class="form-label required">Kota</label>
                            <input type="text" class="form-control @error('kota') is-invalid @enderror" id="kota"
                                name="kota" value="{{ old('kota', $kosan->kota) }}" required>
                            @error('kota')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" class="form-control @error('latitude') is-invalid @enderror"
                                    id="latitude" name="latitude" value="{{ old('latitude', $kosan->latitude) }}">
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" class="form-control @error('longitude') is-invalid @enderror"
                                    id="longitude" name="longitude" value="{{ old('longitude', $kosan->longitude) }}">
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

                <!-- Foto Kosan -->
                <div class="admin-pemilik-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Foto Kosan</h5>
                    </div>
                    <div class="card-body">

                        <!-- Foto Utama -->
                        <div class="mb-4">
                            <h6 class="mb-3">Foto Utama</h6>
                            @if ($kosan->foto_kosan)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $kosan->foto_kosan) }}" alt="Foto Utama" class="img-thumbnail" style="max-width: 250px; height: auto;">
                                </div>
                            @else
                                <div class="alert alert-warning small p-2">Belum ada foto utama.</div>
                            @endif
                            <div class="mt-2">
                                <label for="foto_kosan" class="form-label">{{ $kosan->foto_kosan ? 'Ganti' : 'Upload' }} Foto Utama</label>
                                <input type="file" class="form-control @error('foto_kosan') is-invalid @enderror" id="foto_kosan" name="foto_kosan" accept="image/*">
                                 @error('foto_kosan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Foto ini akan menjadi gambar utama untuk kosan Anda.</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Foto Lainnya -->
                        <div>
                            <h6 class="mb-3">Foto Lainnya</h6>
                            @if ($kosan->fotos && $kosan->fotos->isNotEmpty())
                                <div class="row g-2">
                                    @foreach ($kosan->fotos->sortBy('urutan') as $foto)
                                        <div class="col-md-6 col-lg-3">
                                            <div class="position-relative">
                                                <img src="{{ asset('storage/' . $foto->path_foto) }}" alt="Foto {{ $foto->urutan }}" class="img-thumbnail" style="width: 100%; height: 150px; object-fit: cover;">
                                                <div class="form-check position-absolute bottom-0 inset-e-0 m-2 bg-white rounded px-2 py-1">
                                                    <input class="form-check-input" type="checkbox" name="hapus_foto[]" value="{{ $foto->foto_id }}" id="hapus_{{ $foto->foto_id }}">
                                                    <label class="form-check-label small text-danger" for="hapus_{{ $foto->foto_id }}">
                                                        Hapus
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-text mt-2">Centang "Hapus" untuk menghapus foto yang tidak diinginkan saat update.</div>
                            @else
                                 <div class="alert alert-info small p-2">Belum ada foto lainnya.</div>
                            @endif
                            <div class="mt-3">
                                <label for="foto_tambahan" class="form-label">Tambah Foto Lainnya (Max 3)</label>
                                <input type="file" class="form-control @error('foto_tambahan.*') is-invalid @enderror" id="foto_tambahan" name="foto_tambahan[]" accept="image/*" multiple>
                                @error('foto_tambahan.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Form Panel Kanan -->
            <div class="col-lg-4">
                <!-- Kartu Publikasi -->
                <div class="admin-pemilik-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Publikasi</h5>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Update Kosan
                        </button>
                        <a href="{{ route('admin.manajemen-kosan.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                    </div>
                </div>

                <!-- Info Kosan -->
                <div class="admin-pemilik-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Informasi Kosan</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>ID Kosan:</strong> {{ $kosan->kosan_id }}</p>
                        <p class="mb-2"><strong>Rating:</strong> {{ number_format($kosan->rating_rata ?? 0, 1) }}/5.0
                        </p>
                        <p class="mb-2"><strong>Jumlah Kamar:</strong> {{ $kosan->kamars->count() }} kamar</p>
                        <p class="mb-0"><strong>Dibuat:</strong>
                            {{ $kosan->created_at ? $kosan->created_at->format('d M Y') : '-' }}</p>
                    </div>
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
                const maxFiles = {{ 4 - ($kosan->fotos->count() ?? 0) }};
                const files = Array.from(input.files).slice(0, maxFiles);

                files.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-4';
                        col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="img-thumbnail" style="width: 100%; height: 100px; object-fit: cover;">
                            <span class="badge bg-info position-absolute top-0 end-0 m-1" style="font-size: 10px;">Baru ${index + 1}</span>
                        </div>
                    `;
                        previewContainer.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                });

                if (input.files.length > maxFiles) {
                    alert(
                        `Maksimal ${maxFiles} foto tambahan. Foto yang dipilih: ${input.files.length}. Hanya ${maxFiles} foto pertama yang akan digunakan.`);
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
                        "Koordinat Latitude dan Longitude sudah terisi. Anda bisa menghapus nilai untuk mendapatkan lokasi baru.");
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
