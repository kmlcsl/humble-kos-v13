let map;
let markers = [];
let userMarker;
let userCircle;
let currentLatitude = window.NearbyConfig ? window.NearbyConfig.latitude : null;
let currentLongitude = window.NearbyConfig ? window.NearbyConfig.longitude : null;
let currentRadius = window.NearbyConfig ? window.NearbyConfig.radius : 5;
let manualMode = false;
let mapClickEnabled = false;
let activeGeoWatchId = null;
let activeGeoTimers = [];

// Trackers for loading states
let isGpsSearching = false;
let isGpsModalActive = false;

// Helper: Show/Hide GPS Loading Modal
function showGPSLoading(title = 'Mencari Lokasi GPS...', subtitle = 'Mohon tunggu') {
    const titleEl = document.getElementById('loadingTitle');
    const subtextEl = document.getElementById('loadingSubtext');
    if (titleEl) titleEl.textContent = title;
    if (subtextEl) subtextEl.textContent = subtitle;
    
    const modalEl = document.getElementById('gpsLoadingModal');
    if (!modalEl || !window.bootstrap) return;

    let instance = bootstrap.Modal.getInstance(modalEl);
    if (!instance) instance = new bootstrap.Modal(modalEl);
    
    if (!isGpsModalActive) {
        isGpsModalActive = true;
        instance.show();
    }
}

function hideGPSLoading() {
    isGpsModalActive = false;
    
    const modalEl = document.getElementById('gpsLoadingModal');
    if (!modalEl) return;

    const instance = bootstrap.Modal.getInstance(modalEl);
    if (instance) {
        instance.hide();
    }

    // Aggressive cleanup to prevent stuck backdrops
    setTimeout(() => {
        if (!isGpsModalActive) {
            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        }
    }, 400);
}

// Cancel GPS Search
function cancelGPSSearch() {
    activeGeoTimers.forEach(timer => clearTimeout(timer));
    activeGeoTimers = [];

    if (activeGeoWatchId !== null) {
        navigator.geolocation.clearWatch(activeGeoWatchId);
        activeGeoWatchId = null;
    }

    isGpsSearching = false;
    hideGPSLoading();
    toggleGpsButtonLoading(false);

    const list = document.getElementById('kosanList');
    if (list) {
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
}

function checkSecurityContext() {
    const protocol = window.location.protocol;
    const hostname = window.location.hostname;
    const isSecure = protocol === 'https:' || hostname === 'localhost' || hostname === '127.0.0.1' || hostname === '::1';

    if (!isSecure) {
        const warning = document.getElementById('securityWarning');
        if (warning) {
            warning.innerHTML = `
                <div class="alert-warning-custom">
                    <div class="d-flex align-items-start gap-3">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                        <div>
                            <h6 class="fw-bold mb-2"><i class="fas fa-shield-alt me-1"></i> Peringatan Keamanan</h6>
                            <p class="mb-2 small">Website ini diakses melalui koneksi <strong>tidak aman (HTTP)</strong>. Fitur Geolocation mungkin tidak berfungsi.</p>
                            <p class="mb-0 small"><strong>Solusi:</strong> Gunakan <code>https://</code> untuk hasil terbaik.</p>
                        </div>
                    </div>
                </div>
            `;
            warning.style.display = 'block';
        }
    }
    return isSecure;
}

async function checkGPSStatus() {
    const statusInfo = document.getElementById('gpsStatusInfo');
    const statusDetails = document.getElementById('statusDetails');
    if (!statusInfo || !statusDetails) return;

    let statusHTML = '<ul class="mb-0 ps-3">';

    if (!navigator.geolocation) {
        statusHTML += '<li><i class="fas fa-times-circle text-danger me-1"></i> <strong>Browser tidak mendukung Geolocation</strong></li>';
    } else {
        statusHTML += '<li><i class="fas fa-check-circle text-success me-1"></i> Browser mendukung Geolocation</li>';
        
        if (navigator.permissions && navigator.permissions.query) {
            try {
                const result = await navigator.permissions.query({ name: 'geolocation' });
                if (result.state === 'granted') statusHTML += '<li><i class="fas fa-check-circle text-success me-1"></i> <strong>Izin lokasi: DISETUJUI</strong></li>';
                else if (result.state === 'prompt') statusHTML += '<li><i class="fas fa-question-circle text-info me-1"></i> Izin lokasi: Akan diminta saat tombol diklik</li>';
                else if (result.state === 'denied') statusHTML += '<li><i class="fas fa-times-circle text-danger me-1"></i> <strong>Izin lokasi: DITOLAK</strong></li>';
            } catch (err) {}
        }
    }

    const isSecure = checkSecurityContext();
    statusHTML += `<li><i class="fas fa-${isSecure ? 'check' : 'exclamation'}-circle text-${isSecure ? 'success' : 'warning'} me-1"></i> Koneksi ${isSecure ? 'aman (HTTPS/Localhost)' : 'tidak aman (HTTP)'}</li>`;
    statusHTML += '</ul>';
    statusDetails.innerHTML = statusHTML;
    statusInfo.style.display = 'block';
}

function saveLocationToCache(lat, lng) {
    const locationData = { latitude: lat, longitude: lng, timestamp: new Date().getTime() };
    localStorage.setItem('userLocation', JSON.stringify(locationData));
}

function getCachedLocation() {
    try {
        const cached = localStorage.getItem('userLocation');
        if (!cached) return null;
        const data = JSON.parse(cached);
        const hoursSinceCache = (new Date().getTime() - data.timestamp) / (1000 * 60 * 60);
        if (hoursSinceCache < 24) return { latitude: data.latitude, longitude: data.longitude, age: hoursSinceCache };
    } catch (e) {}
    return null;
}

function initMap() {
    if (map) return;

    const defaultLat = currentLatitude || -6.2088;
    const defaultLng = currentLongitude || 106.8456;

    try {
        if (!document.getElementById('map')) return;

        map = L.map('map').setView([defaultLat, defaultLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        checkGPSStatus();

        if (currentLatitude && currentLongitude) {
            setUserLocation(currentLatitude, currentLongitude);
            searchNearbyKosan();
        } else {
            const cached = getCachedLocation();
            if (cached) {
                setUserLocation(cached.latitude, cached.longitude);
                setTimeout(searchNearbyKosan, 500);
            }
        }
    } catch (error) {
        console.error("Map initialization error:", error);
    }
}

function setUserLocation(lat, lng, draggable = true, accuracy = null) {
    if (!map) return;

    currentLatitude = lat;
    currentLongitude = lng;
    saveLocationToCache(lat, lng);

    if (userMarker) map.removeLayer(userMarker);
    if (userCircle) map.removeLayer(userCircle);

    const userIcon = L.divIcon({
        className: 'custom-user-marker',
        html: '<div style="background: #4f6f52; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>',
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });

    userMarker = L.marker([lat, lng], { icon: userIcon, draggable: draggable }).addTo(map)
        .bindPopup('<b>Lokasi Anda</b><br><small>Drag marker ini untuk menggeser posisi</small>');

    userMarker.on('dragend', function(event) {
        const pos = event.target.getLatLng();
        currentLatitude = pos.lat;
        currentLongitude = pos.lng;
        if (userCircle) userCircle.setLatLng(pos);
        searchNearbyKosan();
    });

    userCircle = L.circle([lat, lng], { 
        color: '#4f6f52', 
        fillColor: '#739072', 
        fillOpacity: 0.1, 
        radius: currentRadius * 1000 
    }).addTo(map);
    
    map.setView([lat, lng], 13);
}

function toggleGpsButtonLoading(isLoading) {
    const btn = document.getElementById('findLocationBtn');
    const icon = btn ? btn.querySelector('.btn-icon i') : null;
    if (!btn || !icon) return;
    btn.disabled = isLoading;
    icon.className = isLoading ? 'fas fa-spinner fa-spin me-1' : 'fas fa-crosshairs me-1';
}

function searchNearbyKosan() {
    if (!currentLatitude || !currentLongitude) {
        isGpsSearching = false;
        hideGPSLoading();
        toggleGpsButtonLoading(false);
        return;
    }
    
    showSkeletonList(6);
    markers.forEach(m => map.removeLayer(m));
    markers = [];

    if (window.currentFetchController) {
        window.currentFetchController.abort();
    }
    window.currentFetchController = new AbortController();
    
    const timer = setTimeout(() => {
        if (window.currentFetchController) {
            window.currentFetchController.abort();
            isGpsSearching = false;
            hideGPSLoading();
            toggleGpsButtonLoading(false);
        }
    }, 15000);

    const apiUrl = window.NearbyConfig ? window.NearbyConfig.apiUrl : "/api/nearby-kosans";

    fetch(`${apiUrl}?lat=${currentLatitude}&lng=${currentLongitude}&radius=${currentRadius}`, {
        signal: window.currentFetchController.signal,
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => {
        clearTimeout(timer);
        if (!res.ok) throw new Error(`Server Error: ${res.status}`);
        return res.json();
    })
    .then(data => {
        displayKosanList(data);
        addKosanMarkers(data);
        isGpsSearching = false;
        hideGPSLoading();
        toggleGpsButtonLoading(false);
    })
    .catch(err => {
        clearTimeout(timer);
        if (err.name !== 'AbortError') {
            console.error("Fetch error:", err);
            const listContainer = document.getElementById('kosanList');
            if (listContainer) {
                listContainer.innerHTML = `<div class="alert alert-danger">Gagal mengambil data kosan. Silakan coba lagi.</div>`;
            }
        }
        isGpsSearching = false;
        hideGPSLoading();
        toggleGpsButtonLoading(false);
    });
}

function displayKosanList(kosans) {
    const listContainer = document.getElementById('kosanList');
    if (!listContainer) return;

    if (kosans.length === 0) {
        listContainer.innerHTML = `<div class="text-center text-muted py-5"><i class="fas fa-home fa-3x mb-3 opacity-25"></i><p>Tidak ada kos dalam radius ${currentRadius} km</p></div>`;
        return;
    }

    let html = `<h5 class="mb-3 fw-bold text-success">Ditemukan ${kosans.length} Kos</h5>`;
    kosans.forEach((k, index) => {
        const nama = k.nama_kosan || k.nama_kos || 'Kosan';
        const img = k.foto_kosan;
        const storageUrl = window.NearbyConfig ? window.NearbyConfig.storageUrl : "/storage";
        const detailUrlBase = window.NearbyConfig ? window.NearbyConfig.detailUrlBase : "/users/kosan";
        
        html += `
            <div class="kosan-card" data-index="${index}" onclick="focusKosan(${index}, ${k.latitude}, ${k.longitude})">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0 fw-bold">${nama}</h6>
                    <span class="distance-badge">${k.distance.toFixed(1)} km</span>
                </div>
                <p class="price-tag mb-2">Rp ${Number(k.harga_bulanan).toLocaleString('id-ID')}/bulan</p>
                ${img ? `<img src="${storageUrl}/${img}" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; margin-bottom: 8px;">` : ''}
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>${k.alamat || ''}</small>
                    <a href="${detailUrlBase}/${k.kosan_id}" class="btn btn-sm btn-outline-success">Detail</a>
                </div>
            </div>`;
    });
    listContainer.innerHTML = html;
}

function showSkeletonList(count = 5) {
    const listContainer = document.getElementById('kosanList');
    if (!listContainer) return;
    let html = `<h5 class="mb-3 fw-bold text-success">Memuat kos terdekat...</h5>`;
    for (let i = 0; i < count; i++) {
        html += `<div class="skeleton"><div class="skeleton-line" style="width: 60%;"></div><div class="skeleton-thumb"></div></div>`;
    }
    listContainer.innerHTML = html;
}

function addKosanMarkers(kosans) {
    if (!map) return;
    kosans.forEach((k, index) => {
        const nama = k.nama_kosan || k.nama_kos || 'Kosan';
        const detailUrlBase = window.NearbyConfig ? window.NearbyConfig.detailUrlBase : "/users/kosan";
        const marker = L.marker([k.latitude, k.longitude]).addTo(map)
            .bindPopup(`<div style="min-width: 200px;"><h6 class="fw-bold mb-2">${nama}</h6><p class="mb-1 text-success fw-bold">Rp ${Number(k.harga_bulanan).toLocaleString('id-ID')}/bulan</p><a href="${detailUrlBase}/${k.kosan_id}" class="btn btn-sm btn-success w-100">Detail</a></div>`);
        marker.on('click', () => highlightKosanCard(index));
        markers.push(marker);
    });
}

function focusKosan(index, lat, lng) {
    if (!map || !markers[index]) return;
    map.setView([lat, lng], 16);
    markers[index].openPopup();
    highlightKosanCard(index);
}

function highlightKosanCard(index) {
    document.querySelectorAll('.kosan-card').forEach(c => c.classList.remove('active'));
    document.querySelector(`.kosan-card[data-index="${index}"]`)?.classList.add('active');
}

function enableManualMode() {
    if (!map) {
        alert("Peta belum siap. Silakan refresh halaman.");
        return;
    }

    manualMode = true;
    mapClickEnabled = true;
    
    const infoEl = document.getElementById('manualModeInfo');
    if (infoEl) infoEl.style.display = 'block';
    
    const mapEl = document.getElementById('map');
    if (mapEl) mapEl.style.cursor = 'crosshair';
    
    map.off('click');
    map.on('click', (e) => {
        setUserLocation(e.latlng.lat, e.latlng.lng);
        searchNearbyKosan();
    });
}

async function searchAddress() {
    const query = document.getElementById('addressSearch').value.trim();
    if (!query) return;
    const btn = document.getElementById('searchAddressBtn');
    if (btn) btn.disabled = true;
    
    try {
        const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&countrycodes=id`);
        const data = await res.json();
        if (data.length > 0) {
            setUserLocation(parseFloat(data[0].lat), parseFloat(data[0].lon));
            searchNearbyKosan();
        } else {
            alert("Alamat tidak ditemukan.");
        }
    } catch (e) {
        alert("Terjadi kesalahan saat mencari alamat.");
    } finally { 
        if (btn) btn.disabled = false; 
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initMap();

    const findLocBtn = document.getElementById('findLocationBtn');
    if (findLocBtn) {
        findLocBtn.addEventListener('click', function() {
            if (isGpsSearching) return;
            isGpsSearching = true;

            toggleGpsButtonLoading(true);
            showGPSLoading('Mencari Lokasi GPS...', 'Mengambil sinyal satelit...');
            
            if (!navigator.geolocation) {
                isGpsSearching = false;
                hideGPSLoading();
                toggleGpsButtonLoading(false);
                alert('Browser Anda tidak mendukung Geolocation.');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (pos) => { 
                    setUserLocation(pos.coords.latitude, pos.coords.longitude); 
                    showGPSLoading('Lokasi Ditemukan', 'Mengambil data kos terdekat...');
                    searchNearbyKosan(); 
                },
                (err) => { 
                    isGpsSearching = false;
                    hideGPSLoading(); 
                    toggleGpsButtonLoading(false); 
                    let msg = 'Gagal mendapatkan lokasi.';
                    if (err.code === 1) msg += ' Izin lokasi ditolak.';
                    else if (err.code === 2) msg += ' Lokasi tidak tersedia.';
                    else if (err.code === 3) msg += ' Waktu permintaan habis.';
                    alert(msg + ' Pastikan GPS aktif dan izin diberikan.'); 
                },
                { 
                    enableHighAccuracy: true, 
                    timeout: 10000,
                    maximumAge: 60000
                }
            );
        });
    }

    const searchAddrBtn = document.getElementById('searchAddressBtn');
    if (searchAddrBtn) {
        searchAddrBtn.addEventListener('click', searchAddress);
    }

    const manualBtn = document.getElementById('manualModeBtn');
    if (manualBtn) {
        manualBtn.addEventListener('click', function(e) {
            e.preventDefault();
            enableManualMode();
        });
    }

    const radiusRange = document.getElementById('radiusRange');
    if (radiusRange) {
        radiusRange.addEventListener('input', (e) => {
            currentRadius = parseInt(e.target.value);
            const valEl = document.getElementById('radiusValue');
            if (valEl) valEl.textContent = currentRadius + ' km';
            if (userCircle) userCircle.setRadius(currentRadius * 1000);
        });
        radiusRange.addEventListener('change', searchNearbyKosan);
    }

    const cancelBtn = document.getElementById('cancelGPSBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', cancelGPSSearch);
    }

    const addressInput = document.getElementById('addressSearch');
    if (addressInput) {
        addressInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchAddress();
            }
        });
    }
});
