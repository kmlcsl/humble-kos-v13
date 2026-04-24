@extends('layouts.user.app')

@section('title', 'Peta Lokasi Kos')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #map {
            height: calc(100vh - 200px);
            min-height: 500px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .kosan-card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .kosan-card:hover {
            box-shadow: 0 4px 16px rgba(79, 111, 82, 0.2);
            border-color: #4f6f52;
            transform: translateY(-2px);
        }

        .kosan-card.active {
            border-color: #4f6f52;
            background: #f0f7f1;
        }

        .kosan-list {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
            padding-right: 8px;
        }

        .kosan-list::-webkit-scrollbar {
            width: 6px;
        }

        .kosan-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .kosan-list::-webkit-scrollbar-thumb {
            background: #4f6f52;
            border-radius: 10px;
        }

        .distance-badge {
            background: linear-gradient(135deg, #4f6f52 0%, #739072 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .price-tag {
            color: #4f6f52;
            font-weight: 700;
            font-size: 18px;
        }

        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .leaflet-popup-content {
            margin: 12px;
            min-width: 200px;
        }

        .location-btn {
            background: linear-gradient(135deg, #4f6f52 0%, #739072 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(79, 111, 82, 0.25);
        }

        .location-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(79, 111, 82, 0.35);
            color: white;
        }

        .radius-control {
            background: white;
            padding: 16px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 16px;
        }

        /* Simple Loading Modal */
        #gpsLoadingModal .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        #gpsLoadingModal .modal-body {
            padding: 40px;
            text-align: center;
        }

        /* Simple Spinner */
        .gps-spinner {
            width: 60px;
            height: 60px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #4f6f52;
            border-radius: 50%;
            margin: 0 auto 20px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }


/* Skeleton loader styles */
.skeleton {
    overflow: hidden;
    position: relative;
    background: #fff;
    border-radius: 12px;
    padding: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    margin-bottom: 10px;
}
.skeleton::after {
    content: "";
    position: absolute;
    top: 0;
    left: -150px;
    height: 100%;
    width: 150px;
    background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(240,240,240,0.7) 50%, rgba(255,255,255,0) 100%);
    animation: shimmer 1.2s infinite;
}
@keyframes shimmer {
    0% { left: -150px; }
    100% { left: 100%; }
}
.skeleton-line {
    height: 12px;
    background: #e9ecef;
    border-radius: 6px;
    margin-bottom: 8px;
}
.skeleton-thumb {
    width: 100%;
    height: 120px;
    background: #e9ecef;
    border-radius: 8px;
    margin: 8px 0;
}


        .alert-info-custom {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: 2px solid #2196f3;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .alert-warning-custom {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 2px solid #ffc107;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4 py-3">
        <!-- Header -->
        <div class="mb-4">
            <h1 class="kosan-title mb-2">
                <i class="fas fa-map-marked-alt text-success me-2"></i>
                Peta Lokasi Kos Terdekat
            </h1>
            <p class="text-muted">Temukan kos terdekat dari lokasi Anda</p>
        </div>

        <!-- Security Warning -->
        <div id="securityWarning" style="display: none;"></div>

        <!-- GPS Status Info -->
        <div id="gpsStatusInfo" class="alert-info-custom" style="display: none;">
            <div class="d-flex align-items-start gap-3">
                <i class="fas fa-info-circle fa-2x text-primary"></i>
                <div class="grow">
                    <h6 class="fw-bold mb-2">
                        <i class="fas fa-satellite-dish me-1"></i> Status GPS & Izin Lokasi
                    </h6>
                    <div id="statusDetails" class="small"></div>
                </div>
            </div>
        </div>

        <!-- Location Controls -->
        <div class="radius-control">
            <!-- Search Address Box -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-search-location me-1"></i> Cari Lokasi dengan Alamat
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                        <input type="text" class="form-control" id="addressSearch"
                            placeholder="Ketik alamat, nama tempat, atau kota... (contoh: Universitas Indonesia, Jakarta)">
                        <button class="btn btn-primary" type="button" id="searchAddressBtn">
                            <i class="fas fa-search me-1"></i> Cari
                        </button>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Tips: Gunakan pencarian alamat jika GPS tidak akurat (lebih cepat & akurat!)
                    </small>
                </div>
            </div>

            <!-- Radius & GPS Buttons -->
            <div class="row align-items-center">
                <div class="col-md-3">
                    <label for="radiusSelect" class="form-label mb-0 fw-semibold">
                        <i class="fas fa-circle-notch me-1"></i> Radius Pencarian
                    </label>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-3">
                        <input type="range" class="form-range" id="radiusRange" min="1" max="20"
                            value="5" step="1">
                        <span class="badge bg-success" id="radiusValue" style="min-width: 60px;">5 km</span>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="d-flex gap-2">
                        <button type="button" class="location-btn flex-fill" id="findLocationBtn">
                            <span class="btn-icon"><i class="fas fa-crosshairs me-1"></i></span>
                            <span class="btn-text">GPS Auto</span>
                        </button>
                        <button type="button" class="btn btn-outline-success flex-fill" id="manualModeBtn">
                            <i class="fas fa-hand-pointer me-1"></i>Pilih Manual
                        </button>
                    </div>
                </div>
            </div>

            <!-- Manual Mode Instruction -->
            <div class="alert alert-info small mt-3 mb-0" id="manualModeInfo" style="display: none;">
                <i class="fas fa-hand-pointer me-1"></i>
                <strong>Mode Manual:</strong> Klik peta atau drag marker hijau untuk mencari kos di lokasi tersebut. Pencarian otomatis!
            </div>

            <!-- GPS Accuracy Info -->
            <div id="accuracyInfo" class="alert alert-warning small mt-3 mb-0" style="display: none;">
                <i class="fas fa-exclamation-triangle me-1"></i>
                <strong>Akurasi GPS:</strong> <span id="accuracyValue"></span>
                <br>
                <small>Jika lokasi tidak akurat, gunakan <strong>Pencarian Alamat</strong> atau <strong>Pilih
                        Manual</strong> di peta.</small>
            </div>
        </div>

        <div class="row">
            <!-- Map Section -->
            <div class="col-lg-8 mb-4">
                <div id="map"></div>
            </div>

            <!-- Kosan List Section -->
            <div class="col-lg-4">
                <div class="kosan-list" id="kosanList">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-map-marker-alt fa-3x mb-3 opacity-25"></i>
                        <p>Klik tombol "Cari Lokasi Saya" untuk menemukan kos terdekat</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- GPS Loading Modal - Bootstrap Standard -->
    <div class="modal fade" id="gpsLoadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="gps-spinner"></div>
                    <h6 class="fw-bold mb-2" id="loadingTitle">Mencari Lokasi GPS...</h6>
                    <p class="text-muted small mb-3" id="loadingSubtext">Mohon tunggu</p>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="cancelGPSBtn">
                        <i class="fas fa-times me-1"></i> Batalkan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        let map;
        let markers = [];
        let userMarker;
        let userCircle;
        let currentLatitude = {{ $latitude ?? 'null' }};
        let currentLongitude = {{ $longitude ?? 'null' }};
        let currentRadius = {{ $radius ?? 5 }};
        let manualMode = false;
        let mapClickEnabled = false;
        let gpsModal = null;
        let activeGeoWatchId = null;
        let activeGeoTimers = [];

        // Helper: Show/Hide GPS Loading Modal
        function showGPSLoading(title = 'Mencari Lokasi GPS...', subtitle = 'Mohon tunggu') {
            document.getElementById('loadingTitle').textContent = title;
            document.getElementById('loadingSubtext').textContent = subtitle;
            if (!gpsModal) {
                gpsModal = new bootstrap.Modal(document.getElementById('gpsLoadingModal'));
            }
            gpsModal.show();
        }

        function hideGPSLoading() {
            if (gpsModal) {
                gpsModal.hide();
            }
        }

        // Cancel GPS Search
        function cancelGPSSearch() {
            // Clear all timers
            activeGeoTimers.forEach(timer => clearTimeout(timer));
            activeGeoTimers = [];

            // Stop geolocation watch if active
            if (activeGeoWatchId !== null) {
                navigator.geolocation.clearWatch(activeGeoWatchId);
                activeGeoWatchId = null;
            }

            hideGPSLoading();
            toggleGpsButtonLoading(false);

            const list = document.getElementById('kosanList');
            list.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-times-circle fa-3x text-secondary mb-3"></i>
                    <p class="text-muted">Pencarian GPS dibatalkan</p>
                    <button class="btn btn-sm btn-primary" onclick="document.getElementById('findLocationBtn').click()">
                        <i class="fas fa-redo me-1"></i> Coba Lagi
                    </button>
                </div>
            `;
        }

        // Setup cancel button
        document.addEventListener('DOMContentLoaded', function() {
            const cancelBtn = document.getElementById('cancelGPSBtn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', cancelGPSSearch);
            }
        });

        // Check HTTPS/localhost security
        function checkSecurityContext() {
            const protocol = window.location.protocol;
            const hostname = window.location.hostname;
            const isSecure = protocol === 'https:' || hostname === 'localhost' || hostname === '127.0.0.1' || hostname ===
                '::1';

            if (!isSecure) {
                const warning = document.getElementById('securityWarning');
                warning.innerHTML = `
                <div class="alert-warning-custom">
                    <div class="d-flex align-items-start gap-3">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                        <div>
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-shield-alt me-1"></i> Peringatan Keamanan
                            </h6>
                            <p class="mb-2 small">
                                Website ini diakses melalui koneksi <strong>tidak aman (HTTP)</strong>.
                                Fitur Geolocation mungkin tidak berfungsi di beberapa browser modern.
                            </p>
                            <p class="mb-0 small">
                                <strong>Solusi:</strong> Gunakan <code>https://</code> atau akses melalui <code>localhost/127.0.0.1</code> untuk hasil terbaik.
                            </p>
                        </div>
                    </div>
                </div>
            `;
                warning.style.display = 'block';
            }
            return isSecure;
        }

        // Check GPS & Permission Status
        async function checkGPSStatus() {
            const statusInfo = document.getElementById('gpsStatusInfo');
            const statusDetails = document.getElementById('statusDetails');
            let statusHTML = '<ul class="mb-0 ps-3">';

            // Check geolocation support
            if (!navigator.geolocation) {
                statusHTML +=
                    '<li><i class="fas fa-times-circle text-danger me-1"></i> <strong>Browser tidak mendukung Geolocation</strong></li>';
                statusDetails.innerHTML = statusHTML + '</ul>';
                statusInfo.style.display = 'block';
                return;
            }
            statusHTML +=
            '<li><i class="fas fa-check-circle text-success me-1"></i> Browser mendukung Geolocation</li>';

            // Check permission status
            if (navigator.permissions && navigator.permissions.query) {
                try {
                    const result = await navigator.permissions.query({
                        name: 'geolocation'
                    });
                    if (result.state === 'granted') {
                        statusHTML +=
                            '<li><i class="fas fa-check-circle text-success me-1"></i> <strong>Izin lokasi: DISETUJUI</strong></li>';
                    } else if (result.state === 'prompt') {
                        statusHTML +=
                            '<li><i class="fas fa-question-circle text-info me-1"></i> Izin lokasi: Akan diminta saat tombol diklik</li>';
                    } else if (result.state === 'denied') {
                        statusHTML +=
                            '<li><i class="fas fa-times-circle text-danger me-1"></i> <strong>Izin lokasi: DITOLAK</strong></li>';
                        statusHTML +=
                            '<li class="mt-2 text-muted">Cara mengizinkan:<br>- Chrome: Klik ikon gembok di address bar → Site settings → Location → Allow<br>- Firefox: Klik ikon (i) di address bar → Permissions → Location → Allow</li>';
                    }
                } catch (err) {
                    statusHTML +=
                        '<li><i class="fas fa-info-circle text-muted me-1"></i> Status izin: Tidak dapat diperiksa (akan diminta saat dibutuhkan)</li>';
                }
            } else {
                statusHTML +=
                    '<li><i class="fas fa-info-circle text-muted me-1"></i> Browser tidak mendukung Permission API</li>';
            }

            // Security context
            const isSecure = checkSecurityContext();
            if (isSecure) {
                statusHTML +=
                    '<li><i class="fas fa-check-circle text-success me-1"></i> Koneksi aman (HTTPS/Localhost)</li>';
            } else {
                statusHTML +=
                    '<li><i class="fas fa-exclamation-triangle text-warning me-1"></i> <strong>Koneksi tidak aman (HTTP)</strong> - Beberapa browser mungkin memblokir akses lokasi</li>';
            }

            // GPS Tips for Windows
            statusHTML +=
                '<li class="mt-2"><i class="fas fa-laptop me-1"></i> <strong>Tips untuk Windows/Laptop:</strong><br>';
            statusHTML +=
                '<small class="text-muted">- Aktifkan Location Services: Settings → Privacy → Location → ON<br>';
            statusHTML +=
                '- Pastikan browser memiliki akses lokasi: Settings → Privacy → Location → Allow apps access<br>';
            statusHTML += '- Proses GPS di laptop lebih lambat dari smartphone (bisa 30-90 detik)</small></li>';

            statusHTML += '</ul>';
            statusDetails.innerHTML = statusHTML;
            statusInfo.style.display = 'block';
        }

        // Save location to localStorage with timestamp
        function saveLocationToCache(lat, lng) {
            const locationData = {
                latitude: lat,
                longitude: lng,
                timestamp: new Date().getTime()
            };
            localStorage.setItem('userLocation', JSON.stringify(locationData));
            // Keep backward compatibility
            localStorage.setItem('userLat', lat.toString());
            localStorage.setItem('userLng', lng.toString());
        }

        // Get cached location if still valid (within 24 hours)
        function getCachedLocation() {
            try {
                const cached = localStorage.getItem('userLocation');
                if (!cached) return null;

                const data = JSON.parse(cached);
                const now = new Date().getTime();
                const hoursSinceCache = (now - data.timestamp) / (1000 * 60 * 60);

                if (hoursSinceCache < 24) {
                    return {
                        latitude: data.latitude,
                        longitude: data.longitude,
                        age: hoursSinceCache
                    };
                }
            } catch (e) {
                console.error('Error reading cached location:', e);
            }
            return null;
        }

        // Initialize map
        function initMap() {
            // Default ke Jakarta jika tidak ada koordinat
            const defaultLat = currentLatitude || -6.2088;
            const defaultLng = currentLongitude || 106.8456;

            map = L.map('map').setView([defaultLat, defaultLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            // Check GPS status on load
            checkGPSStatus();

            // Jika ada koordinat awal, langsung cari
            if (currentLatitude && currentLongitude) {
                setUserLocation(currentLatitude, currentLongitude);
                searchNearbyKosan();
            } else {
                const cached = getCachedLocation();
                if (cached) {
                    setUserLocation(cached.latitude, cached.longitude);
                    const hoursAgo = Math.round(cached.age * 10) / 10;
                    const list = document.getElementById('kosanList');
                    list.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-map-marker-alt fa-3x mb-3 text-success"></i>
                        <p>Menggunakan lokasi tersimpan<br><small class="text-muted">(${hoursAgo} jam yang lalu)</small></p>
                        <p class="small text-muted">Mencari kos terdekat...</p>
                    </div>
                `;
                    // Auto search setelah set cached location
                    setTimeout(() => {
                        searchNearbyKosan();
                    }, 500);
                }
            }
        }

        // Set user location marker
        function setUserLocation(lat, lng, draggable = true, accuracy = null) {
            currentLatitude = lat;
            currentLongitude = lng;

            // Save to cache
            saveLocationToCache(lat, lng);

            // Remove existing user marker and circle
            if (userMarker) map.removeLayer(userMarker);
            if (userCircle) map.removeLayer(userCircle);

            // Add user marker (DRAGGABLE)
            const userIcon = L.divIcon({
                className: 'custom-user-marker',
                html: '<div style="background: #4f6f52; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });

            userMarker = L.marker([lat, lng], {
                    icon: userIcon,
                    draggable: draggable // Make marker draggable
                }).addTo(map)
                .bindPopup('<b>Lokasi Anda</b><br><small>Drag marker ini untuk menggeser posisi</small>');

            // Event when marker is dragged - FULL AUTO SEARCH!
            userMarker.on('dragend', function(event) {
                const position = event.target.getLatLng();
                currentLatitude = position.lat;
                currentLongitude = position.lng;
                saveLocationToCache(position.lat, position.lng);

                // Update circle position
                if (userCircle) {
                    userCircle.setLatLng(position);
                }

                console.log('Marker dragged to:', position.lat, position.lng);

                // LANGSUNG AUTO SEARCH tanpa perlu klik button!
                const list = document.getElementById('kosanList');
                if (list) {
                    list.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-map-marker-alt fa-3x mb-3 text-success"></i>
                            <p class="fw-bold">Lokasi diperbarui!</p>
                            <p class="small text-muted">Mencari kos terdekat...</p>
                        </div>
                    `;
                }

                // Auto search setelah 300ms
                setTimeout(() => {
                    searchNearbyKosan();
                }, 300);
            });

            // Add radius circle
            userCircle = L.circle([lat, lng], {
                color: '#4f6f52',
                fillColor: '#739072',
                fillOpacity: 0.1,
                radius: currentRadius * 1000 // convert to meters
            }).addTo(map);

            // Add accuracy circle if available
            if (accuracy && accuracy > 100) {
                L.circle([lat, lng], {
                    color: '#ff6b6b',
                    fillColor: '#ff6b6b',
                    fillOpacity: 0.05,
                    radius: accuracy,
                    dashArray: '5, 10'
                }).addTo(map);

                // Show accuracy warning
                const accuracyInfo = document.getElementById('accuracyInfo');
                const accuracyValue = document.getElementById('accuracyValue');

                if (accuracyInfo && accuracyValue) {
                    if (accuracy > 1000) {
                        accuracyValue.innerHTML =
                            `<strong class="text-danger">Sangat Rendah (±${(accuracy/1000).toFixed(1)} km)</strong>`;
                    } else {
                        accuracyValue.innerHTML =
                            `<strong class="text-warning">Rendah (±${Math.round(accuracy)} meter)</strong>`;
                    }
                    accuracyInfo.style.display = 'block';
                }
            } else {
                const accuracyInfo = document.getElementById('accuracyInfo');
                if (accuracyInfo) {
                    accuracyInfo.style.display = 'none';
                }
            }

            // Center map
            map.setView([lat, lng], 13);
        }

                function toggleGpsButtonLoading(isLoading) {

                    const btn = document.getElementById('findLocationBtn');

                    const icon = btn.querySelector('.btn-icon i');

                    if (!btn || !icon) return;

        

                    if (isLoading) {

                        btn.disabled = true;

                        icon.className = 'fas fa-spinner fa-spin me-1';

                    } else {

                        btn.disabled = false;

                        icon.className = 'fas fa-crosshairs me-1';

                    }

                }

        

                // Search nearby kosan

                function searchNearbyKosan() {

                    if (!currentLatitude || !currentLongitude) {

                        alert('Lokasi tidak ditemukan. Silakan aktifkan lokasi Anda.');

                        return;

                    }

                    // Update modal text jika masih muncul
                    if (gpsModal && document.getElementById('gpsLoadingModal').classList.contains('show')) {
                        showGPSLoading('Memuat Kos Terdekat...', 'Mohon tunggu');
                    }

                    showSkeletonList(6);

        

                    // Clear existing markers

                    markers.forEach(marker => map.removeLayer(marker));

                    markers = [];

        

                    // Fetch nearby kosan

                    window.currentFetchController = new AbortController();

                    const timer = setTimeout(() => {

                        window.currentFetchController.abort();

                        console.error('Fetch timed out after 10 seconds.');

                        hideGPSLoading();

                        alert('Waktu pencarian habis. Coba lagi.');

                        toggleGpsButtonLoading(false); 

                    }, 10000); // 10-second timeout

        

                    fetch(`/api/nearby-kosans?lat=${currentLatitude}&lng=${currentLongitude}&radius=${currentRadius}`, {

                            signal: window.currentFetchController.signal

                        })

                        .then(response => {

                            clearTimeout(timer);

                            if (!response.ok) {

                                throw new Error(`HTTP error! status: ${response.status}`);

                            }

                            return response.json();

                        })

                        .then(data => {

                            displayKosanList(data);

                            addKosanMarkers(data);

                            hideGPSLoading();

                            toggleGpsButtonLoading(false);

                        })

                        .catch(error => {

                            clearTimeout(timer);

                            if (error.name === 'AbortError') {

                                // Already handled by timeout alert

                                return;

                            }

                            console.error('Error:', error);

                            hideGPSLoading();

                            alert('Terjadi kesalahan saat mencari kos terdekat. Periksa konsol untuk detail.');

                            toggleGpsButtonLoading(false);

                        });

                }

        

                // Display kosan list

                function displayKosanList(kosans) {

                    const listContainer = document.getElementById('kosanList');

        

                    if (kosans.length === 0) {

                        listContainer.innerHTML = `

                        <div class="text-center text-muted py-5">

                            <i class="fas fa-home fa-3x mb-3 opacity-25"></i>

                            <p>Tidak ada kos dalam radius ${currentRadius} km</p>

                            <small>Coba perbesar radius pencarian</small>

                        </div>

                    `;

                        return;

                    }

        

                    let html = `<h5 class="mb-3 fw-bold text-success">Ditemukan ${kosans.length} Kos</h5>`;

        

                    kosans.forEach((kosan, index) => {
                        const nama = kosan.nama_kosan || kosan.nama_kos || 'Kosan';
                        const img = kosan.foto_kosan || kosan.foto_kosan;
                        html += `

                        <div class="kosan-card" data-index="${index}" onclick="focusKosan(${index}, ${kosan.latitude}, ${kosan.longitude})">

                            <div class="d-flex justify-content-between align-items-start mb-2">

                                <h6 class="mb-0 fw-bold">${nama}</h6>

                                <span class="distance-badge">${kosan.distance.toFixed(1)} km</span>

                            </div>

                            <p class="price-tag mb-2">Rp ${Number(kosan.harga_bulanan).toLocaleString('id-ID')}/bulan</p>

                            ${img ?
                                `<img src="/storage/${img}" alt="${nama}"
                                         style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; margin-bottom: 8px;">`
                                : ''}

                            <div class="d-flex justify-content-between align-items-center">

                                <small class="text-muted">

                                    <i class="fas fa-map-marker-alt me-1"></i>${kosan.alamat || 'Alamat tidak tersedia'}

                                </small>

                                <a href="/users/kosan/${kosan.kosan_id}" class="btn btn-sm btn-outline-success">Lihat Detail</a>

                            </div>

                        </div>

                    `;

                    });

        

                    listContainer.innerHTML = html;

                }

        

                // Show skeleton list while loading

                function showSkeletonList(count = 5) {

                    const listContainer = document.getElementById('kosanList');

                    let html = `<h5 class="mb-3 fw-bold text-success">Memuat kos terdekat...</h5>`;

                    for (let i = 0; i < count; i++) {

                        html += `

                        <div class="skeleton">

                            <div class="skeleton-line" style="width: 60%; height: 14px;"></div>

                            <div class="skeleton-line" style="width: 30%; height: 12px;"></div>

                            <div class="skeleton-thumb"></div>

                            <div class="d-flex justify-content-between">

                                <div class="skeleton-line" style="width: 40%; height: 12px;"></div>

                                <div class="skeleton-line" style="width: 20%; height: 12px;"></div>

                            </div>

                        </div>

                    `;

                    }

                    listContainer.innerHTML = html;

                }

        

                // Add kosan markers to map

                function addKosanMarkers(kosans) {

                    kosans.forEach((kosan, index) => {
                        const nama = kosan.nama_kosan || kosan.nama_kos || 'Kosan';
                        const marker = L.marker([kosan.latitude, kosan.longitude])
                            .addTo(map)
                            .bindPopup(`
                            <div style="min-width: 200px;">

                                <h6 class="fw-bold mb-2">${nama}</h6>

                                <p class="mb-1 text-success fw-bold">Rp ${Number(kosan.harga_bulanan).toLocaleString('id-ID')}/bulan</p>

                                <p class="mb-2 small text-muted"><i class="fas fa-road me-1"></i>${kosan.distance.toFixed(1)} km dari Anda</p>

                                <a href="/users/kosan/${kosan.kosan_id}" class="btn btn-sm btn-success w-100">Lihat Detail</a>

                            </div>

                        `);

        

                        marker.on('click', function() {

                            highlightKosanCard(index);

                        });

        

                        markers.push(marker);

                    });

                }

        

                // Focus on specific kosan

                function focusKosan(index, lat, lng) {

                    map.setView([lat, lng], 16);

                    markers[index].openPopup();

                    highlightKosanCard(index);

                }

        

                // Highlight kosan card

                function highlightKosanCard(index) {

                    document.querySelectorAll('.kosan-card').forEach(card => {

                        card.classList.remove('active');

                    });

                    document.querySelector(`.kosan-card[data-index="${index}"]`)?.classList.add('active');

                }

        

                // Get user location

                document.getElementById('findLocationBtn').addEventListener('click', function() {

                    if (!navigator.geolocation) {

                        const list = document.getElementById('kosanList');

                        list.innerHTML =

                            '<div class="text-center py-4"><p class="text-danger mb-2">Browser Anda tidak mendukung Geolocation.</p><p class="small text-muted">Gunakan browser terbaru atau perangkat lain.</p></div>';

                        return;

                    }

        

                    toggleGpsButtonLoading(true);

                    showGPSLoading('Mencari Lokasi GPS...', 'Mohon tunggu');

                    const list = document.getElementById('kosanList');

                    list.innerHTML =

                        '<div class="text-center py-4"><p class="text-muted">Meminta izin lokasi...</p></div>';

        

                    // Try low accuracy fallback

                    const tryLowAccuracy = () => {

                        showGPSLoading('Mencari Lokasi...', 'Mode akurasi rendah');

        

                        navigator.geolocation.getCurrentPosition(

                            function(pos2) {

                                // Jangan update modal - biarkan tetap "Mencari Lokasi..."
                                // Modal akan di-hide oleh searchNearbyKosan()

                                setUserLocation(

                                    pos2.coords.latitude,

                                    pos2.coords.longitude,

                                    true,

                                    pos2.coords.accuracy

                                );

                                searchNearbyKosan();

                            },

                            function(err2) {

                                hideGPSLoading();

                                toggleGpsButtonLoading(false);

                                let msg2 = 'Tidak dapat mengakses lokasi Anda.';

                                let helpText = '';

        

                                if (err2.code === err2.PERMISSION_DENIED) {

                                    msg2 = 'Akses lokasi ditolak oleh browser.';

                                    helpText = `

                                    <div class="alert alert-warning small mt-2">

                                        <strong>Cara mengaktifkan izin lokasi:</strong><br>

                                        • <strong>Chrome:</strong> Klik ikon gembok/info di address bar → Site settings → Location → Allow<br>

                                        • <strong>Firefox:</strong> Klik ikon (i) di address bar → Permissions → Location → Allow<br>

                                        • <strong>Edge:</strong> Klik ikon gembok → Permissions → Location → Allow<br><br>

                                        <strong>Windows 10/11:</strong><br>

                                        • Settings → Privacy → Location → ON<br>

                                        • Pastikan browser Anda memiliki akses lokasi

                                    </div>

                                `;

                                } else if (err2.code === err2.POSITION_UNAVAILABLE) {

                                    msg2 = 'Informasi lokasi tidak tersedia dari perangkat Anda.';

                                    helpText = `

                                    <div class="alert alert-info small mt-2">

                                        <strong>Alternatif Cepat:</strong><br>

                                        • Gunakan <strong>Pencarian Alamat</strong> di atas (lebih cepat & akurat)<br>

                                        • Atau klik <strong>Pilih Manual</strong> untuk memilih lokasi di peta

                                    </div>

                                `;

                                } else if (err2.code === err2.TIMEOUT) {

                                    msg2 = 'GPS terlalu lama merespon.';

                                    helpText = `

                                    <div class="alert alert-info small mt-2">

                                        <strong>Solusi Cepat:</strong><br>

                                        • Gunakan <strong>Pencarian Alamat</strong> (lebih cepat & akurat)<br>

                                        • Atau klik <strong>Pilih Manual</strong> di peta

                                    </div>

                                `;

                                }

        

                                const cached = getCachedLocation();

        

                                const list2 = document.getElementById('kosanList');

                                list2.innerHTML = `

                                <div class="py-4">

                                    <div class="text-center mb-3">

                                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>

                                        <p class="text-danger fw-bold mb-2">${msg2}</p>

                                    </div>

                                    ${helpText}

                                    <div class="d-flex gap-2 justify-content-center mt-3 flex-wrap">

                                        ${cached ? `<button id="useCachedBtn" class="btn btn-sm btn-success">

                                                <i class="fas fa-map-marker-alt me-1"></i> Lokasi Tersimpan

                                            </button>` : ''}

                                        <button id="useManualBtn" class="btn btn-sm btn-primary">

                                            <i class="fas fa-hand-pointer me-1"></i> Pilih Manual

                                        </button>

                                        <button id="retryLocationBtn" class="btn btn-sm btn-secondary">

                                            <i class="fas fa-redo me-1"></i> Coba GPS Lagi

                                        </button>

                                    </div>

                                </div>

                            `;

                                const retryBtn = document.getElementById('retryLocationBtn');

                                if (retryBtn) retryBtn.onclick = () => document.getElementById('findLocationBtn')

                                    .click();

                                const useCachedBtn = document.getElementById('useCachedBtn');

                                if (useCachedBtn && cached) {

                                    useCachedBtn.onclick = () => {

                                        setUserLocation(cached.latitude, cached.longitude);

                                        searchNearbyKosan();

                                    };

                                }

                                const useManualBtn = document.getElementById('useManualBtn');

                                if (useManualBtn) useManualBtn.onclick = enableManualMode;

                            }, {

                                enableHighAccuracy: false,

                                timeout: 6000, // 6 seconds - faster fallback

                                maximumAge: 120000 // Accept 2 minute old position

                            }

                        );

                    };

        

                    // Clear previous timers
                    activeGeoTimers.forEach(timer => clearTimeout(timer));
                    activeGeoTimers = [];

                    // Try high accuracy first
                    showGPSLoading('Mencari Lokasi GPS...', 'Mohon tunggu');

                    const geoTotalTimeoutMs = 10000; // Total 10 seconds max

                    const geoTimer = setTimeout(() => {
                        tryLowAccuracy();
                    }, 4000); // Try low accuracy after 4 seconds
                    activeGeoTimers.push(geoTimer);

                    const hardTimeout = setTimeout(() => {

                        hideGPSLoading();

                        toggleGpsButtonLoading(false);

                        const list = document.getElementById('kosanList');

                        const cached = getCachedLocation();

                        list.innerHTML = `

                        <div class="py-4 text-center">

                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>

                            <p class="fw-bold mb-2">GPS terlalu lama merespon.</p>

                            <p class="small text-muted">Gunakan lokasi tersimpan atau pilih manual.</p>

                            <div class="d-flex gap-2 justify-content-center mt-2">

                                ${cached ? `<button id="useCachedBtn" class="btn btn-sm btn-success"><i class="fas fa-map-marker-alt me-1"></i> Lokasi Tersimpan</button>` : ''}

                                <button id="enableManualBtn" class="btn btn-sm btn-primary"><i class="fas fa-hand-pointer me-1"></i> Pilih Manual</button>

                            </div>

                        </div>

                    `;

                        const useCachedBtn = document.getElementById('useCachedBtn');

                        if (useCachedBtn && cached) {

                            useCachedBtn.onclick = () => {

                                setUserLocation(cached.latitude, cached.longitude);

                                searchNearbyKosan();

                            };

                        }

                        const enableManualBtn = document.getElementById('enableManualBtn');

                        if (enableManualBtn) enableManualBtn.onclick = enableManualMode;

                    }, geoTotalTimeoutMs);
                    activeGeoTimers.push(hardTimeout);



                    navigator.geolocation.getCurrentPosition(

                        function(position) {

                            clearTimeout(geoTimer);

                            clearTimeout(hardTimeout);

                            // Jangan update modal - biarkan tetap "Mencari Lokasi..."
                            // Modal akan di-hide otomatis oleh searchNearbyKosan()

                            setUserLocation(

                                position.coords.latitude,

                                position.coords.longitude,

                                true,

                                position.coords.accuracy

                            );

                            searchNearbyKosan();

                        },

                        function(error) {

                            clearTimeout(geoTimer);

                            clearTimeout(hardTimeout);

                            toggleGpsButtonLoading(false);

                            if (error.code === error.TIMEOUT || error.code === error.POSITION_UNAVAILABLE) {

                                tryLowAccuracy();

                                return;

                            }



                            hideGPSLoading();

                            let errorMsg = 'Tidak dapat mengakses lokasi Anda.';

                            let helpText = '';

        

                            if (error.code === error.PERMISSION_DENIED) {

                                errorMsg = 'Akses lokasi ditolak oleh browser.';

                                helpText = `

                                <div class="alert alert-warning small mt-2">

                                    <strong>Cara mengaktifkan izin lokasi:</strong><br>

                                    • <strong>Chrome:</strong> Klik ikon gembok/info di address bar → Site settings → Location → Allow<br>

                                    • <strong>Firefox:</strong> Klik ikon (i) di address bar → Permissions → Location → Allow<br>

                                    • <strong>Edge:</strong> Klik ikon gembok → Permissions → Location → Allow<br><br>

                                    <strong>Windows 10/11:</strong><br>

                                    • Settings → Privacy → Location → ON<br>

                                    • Pastikan browser Anda memiliki akses lokasi

                                </div>

                            `;

                            }

        

                            const list3 = document.getElementById('kosanList');

                            list3.innerHTML = `

                            <div class="py-4">

                                <div class="text-center mb-3">

                                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>

                                    <p class="text-danger fw-bold mb-2">${errorMsg}</p>

                                </div>

                                ${helpText}

                                <div class="text-center mt-3">

                                    <button id="retryLocationBtn" class="btn btn-sm btn-primary">

                                        <i class="fas fa-redo me-1"></i> Coba Lagi

                                    </button>

                                </div>

                            </div>

                        `;

                            const retryBtn = document.getElementById('retryLocationBtn');

                            if (retryBtn) retryBtn.onclick = () => document.getElementById('findLocationBtn').click();

                        }, {

                            enableHighAccuracy: true,

                            timeout: 5000, // 5s for GPS fix - faster

                            maximumAge: 60000 // Accept 1 minute old position

                        }

                    );

                });

        // Radius change handler
        document.getElementById('radiusRange').addEventListener('input', function(e) {
            currentRadius = parseInt(e.target.value);
            document.getElementById('radiusValue').textContent = currentRadius + ' km';

            // Update circle radius
            if (userCircle) {
                userCircle.setRadius(currentRadius * 1000);
            }
        });

        document.getElementById('radiusRange').addEventListener('change', function() {
            if (currentLatitude && currentLongitude) {
                searchNearbyKosan();
            }
        });

        // Geocoding Search with Nominatim (OpenStreetMap - FREE!)
        async function searchAddress() {
            const query = document.getElementById('addressSearch').value.trim();
            if (!query) {
                alert('Masukkan alamat atau nama tempat yang ingin dicari');
                return;
            }

            const searchBtn = document.getElementById('searchAddressBtn');
            const originalText = searchBtn.innerHTML;
            searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mencari...';
            searchBtn.disabled = true;

            try {
                // Use Nominatim API (OpenStreetMap - FREE)
                const response = await fetch(
                    `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=id`, {
                        headers: {
                            'User-Agent': 'KosanApp/1.0'
                        },
                        referrerpolicy: "no-referrer"
                    }
                );

                const results = await response.json();

                if (results.length === 0) {
                    alert('Lokasi tidak ditemukan. Coba gunakan kata kunci yang lebih spesifik.');
                    return;
                }

                // If multiple results, show to user
                if (results.length > 1) {
                    let message = 'Ditemukan beberapa lokasi:\n\n';
                    results.forEach((r, i) => {
                        message += `${i + 1}. ${r.display_name}\n`;
                    });
                    message += '\nMenggunakan hasil pertama.';

                    if (confirm(message + '\n\nLanjutkan?')) {
                        const result = results[0];
                        setUserLocation(parseFloat(result.lat), parseFloat(result.lon), true, null);
                        map.setView([result.lat, result.lon], 15);

                        // Show success message (searching will happen automatically)
                        const list = document.getElementById('kosanList');
                        list.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="fw-bold">Lokasi ditemukan!</p>
                        <p class="small text-muted">${result.display_name}</p>
                        <p class="small text-muted mt-2">Mencari kos terdekat...</p>
                    </div>
                `;

                        // Auto search setelah lokasi ditemukan
                        setTimeout(() => {
                            searchNearbyKosan();
                        }, 800);
                    }
                } else {
                    const result = results[0];
                    setUserLocation(parseFloat(result.lat), parseFloat(result.lon), true, null);
                    map.setView([result.lat, result.lon], 15);

                    // Show success message
                    const list = document.getElementById('kosanList');
                    list.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="fw-bold">Lokasi ditemukan!</p>
                        <p class="small text-muted">${result.display_name}</p>
                        <p class="small text-muted mt-2">Mencari kos terdekat...</p>
                    </div>
                `;

                    // Auto search setelah lokasi ditemukan
                    setTimeout(() => {
                        searchNearbyKosan();
                    }, 800);
                }

            } catch (error) {
                console.error('Geocoding error:', error);
                alert('Terjadi kesalahan saat mencari lokasi. Pastikan koneksi internet Anda stabil.');
            } finally {
                searchBtn.innerHTML = originalText;
                searchBtn.disabled = false;
            }
        }

        // Manual Mode: Click on map to set location - FULL AUTO!
        function enableManualMode() {
            if (!map) {
                console.error('Map belum di-inisialisasi');
                alert('Peta belum siap. Silakan tunggu sebentar dan coba lagi.');
                return;
            }

            manualMode = true;
            mapClickEnabled = true;

            // Show instruction
            const manualInfo = document.getElementById('manualModeInfo');
            if (manualInfo) {
                manualInfo.style.display = 'block';
            }

            // Change map cursor
            const mapElement = document.getElementById('map');
            if (mapElement) {
                mapElement.style.cursor = 'crosshair';
            }

            // If no user marker yet, wait for click
            if (!userMarker) {
                const list = document.getElementById('kosanList');
                if (list) {
                    list.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-mouse-pointer fa-3x text-primary mb-3"></i>
                        <p class="fw-bold">Mode Manual Aktif</p>
                        <p class="small text-muted">Klik di peta untuk menentukan lokasi Anda</p>
                    </div>
                `;
                }
            }

            // Remove previous click handlers to avoid duplicates
            map.off('click');

            // Enable click event on map - LANGSUNG AUTO SEARCH!
            map.on('click', function(e) {
                if (mapClickEnabled) {
                    setUserLocation(e.latlng.lat, e.latlng.lng, true, null);

                    // Show searching message
                    const list = document.getElementById('kosanList');
                    if (list) {
                        list.innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-map-marker-alt fa-3x mb-3 text-success"></i>
                                <p class="fw-bold">Lokasi dipilih!</p>
                                <p class="small text-muted">Mencari kos terdekat...</p>
                            </div>
                        `;
                    }

                    // LANGSUNG AUTO SEARCH!
                    setTimeout(() => {
                        searchNearbyKosan();
                    }, 300);
                }
            });

            console.log('Manual mode enabled - Full auto search!');
        }

        // Disable manual mode
        function disableManualMode() {
            manualMode = false;
            mapClickEnabled = false;

            const mapElement = document.getElementById('map');
            if (mapElement) {
                mapElement.style.cursor = '';
            }

            const manualInfo = document.getElementById('manualModeInfo');
            if (manualInfo) {
                manualInfo.style.display = 'none';
            }

            if (map) {
                map.off('click');
            }

            console.log('Manual mode disabled');
        }

        // Event Listeners
        const searchAddressBtn = document.getElementById('searchAddressBtn');
        if (searchAddressBtn) {
            searchAddressBtn.addEventListener('click', searchAddress);
        }

        // Allow Enter key in search box
        const addressSearch = document.getElementById('addressSearch');
        if (addressSearch) {
            addressSearch.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchAddress();
                }
            });
        }

        // Manual mode button - FULL AUTO!
        const manualModeBtn = document.getElementById('manualModeBtn');
        if (manualModeBtn) {
            manualModeBtn.addEventListener('click', function() {
                enableManualMode();
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
        });
    </script>
    <script>
        // Cancel button handler
        (function() {
            const overlay = document.getElementById('loadingOverlay');
            if (!overlay) return;
            const cancelBtn = document.createElement('button');
            cancelBtn.className = 'btn btn-sm btn-outline-danger mt-2';
            cancelBtn.innerHTML = '<i class="fas fa-times me-1"></i> Batalkan';
            cancelBtn.onclick = function() {
                try {
                    if (window.currentFetchController) {
                        window.currentFetchController.abort();
                    }
                } catch (e) {}
                overlay.style.display = 'none';
                const btn = document.getElementById('findLocationBtn');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-crosshairs me-1"></i>GPS Auto';
                }
            };
            const container = document.querySelector('#loadingOverlay .loading-spinner');
            if (container) container.appendChild(cancelBtn);
        })();
    </script>
@endpush
