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
        // Global Config for nearby.js
        window.NearbyConfig = {
            latitude: @json($latitude ?? null),
            longitude: @json($longitude ?? null),
            radius: @json($radius ?? 5),
            apiUrl: @json(url('/api/nearby-kosans')),
            storageUrl: @json(asset('storage')),
            detailUrlBase: @json(url('/users/kosan'))
        };
    </script>
    <script src="{{ asset('js/nearby.js') }}?v={{ time() }}"></script>
@endpush
