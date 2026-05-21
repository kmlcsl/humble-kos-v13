@extends('layouts.user.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="container-fluid">
            <div class="row align-items-center g-3">
                <div class="col-12 col-lg-7">
                    <h1>Selamat Datang, {{ Auth::user()?->name ?? 'Pengguna' }}!</h1>
                    <p class="text-muted mb-0">Temukan kos terbaik untuk kebutuhan Anda di HumbleKos</p>
                </div>
                <div class="col-12 col-lg-5">
                    <div class="search-box">
                        <form action="{{ route('users.kosan.search') }}" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" name="keyword" placeholder="Cari kos...">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-6 col-lg-3 mb-3 mb-md-4">
                <div class="status-card">
                    <div class="row g-0 align-items-center">
                        <div class="col-4 col-sm-3 text-center">
                            <div class="status-icon bg-gray-light">
                                <i class="fas fa-calendar-check" style="color: #4f6f52;"></i>
                            </div>
                        </div>
                        <div class="col-8 col-sm-9">
                            <div class="card-body">
                                <h5 class="card-title">Booking Aktif</h5>
                                <p class="card-text mb-0">{{ $active_booking_count ?? 0 }} Booking</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 mb-3 mb-md-4">
                <div class="status-card">
                    <div class="row g-0 align-items-center">
                        <div class="col-4 col-sm-3 text-center">
                            <div class="status-icon bg-gray-light">
                                <i class="fas fa-heart" style="color: #4f6f52;"></i>
                            </div>
                        </div>
                        <div class="col-8 col-sm-9">
                            <div class="card-body">
                                <h5 class="card-title">Kosan Favorit</h5>
                                <p class="card-text mb-0"><span id="wishlist-count-display">{{ $wishlist_count ?? 0 }}</span> Kosan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 mb-3 mb-md-4">
                <div class="status-card">
                    <div class="row g-0 align-items-center">
                        <div class="col-4 col-sm-3 text-center">
                            <div class="status-icon bg-gray-light">
                                <i class="fas fa-money-bill-wave" style="color: #4f6f52;"></i>
                            </div>
                        </div>
                        <div class="col-8 col-sm-9">
                            <div class="card-body">
                                <h5 class="card-title">Pending</h5>
                                <p class="card-text mb-0">{{ $pending_booking_count ?? 0 }} Bayar</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 mb-3 mb-md-4">
                <div class="status-card">
                    <div class="row g-0 align-items-center">
                        <div class="col-4 col-sm-3 text-center">
                            <div class="status-icon bg-gray-light">
                                <i class="fas fa-bell" style="color: #4f6f52;"></i>
                            </div>
                        </div>
                        <div class="col-8 col-sm-9">
                            <div class="card-body">
                                <h5 class="card-title">Notifikasi</h5>
                                <p class="card-text mb-0">{{ $notification_count ?? 0 }} Baru</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommended Kos -->
    <div class="container-fluid mt-4">
        <div class="section-header d-flex justify-content-between align-items-center mb-3">
            <h2 class="section-title">Rekomendasi Kos</h2>
            <a href="{{ route('users.kosan.index') }}" class="view-all">Lihat Semua <i
                    class="fas fa-arrow-right ms-1"></i></a>
        </div>

        <div class="row">
            @forelse($recommended_kosans ?? [] as $kosan)
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="kos-card">
                        <div class="kos-image-wrapper">
                            <!-- FIXED: Improved image validation and fallback -->
                            @php
                                $fotoKosan = data_get($kosan, 'foto_kosan.path_gambar') ?? data_get($kosan, 'foto_kosan');
                                $hasValidImage = is_string($fotoKosan) &&
                                                 trim($fotoKosan) !== '' &&
                                                 file_exists(storage_path('app/public/' . $fotoKosan));
                            @endphp

                            @if ($hasValidImage)
                                <img src="{{ asset('storage/' . $fotoKosan) }}"
                                    alt="{{ data_get($kosan, 'nama_kosan', 'Kosan') }}" class="kos-image"
                                    onload="this.style.background='none'"
                                    onerror="this.style.display='none'; this.onerror=null;">
                            @else
                                <div class="kos-image-placeholder" style="width: 100%; height: 200px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 14px;">
                                    <i class="fas fa-image" style="font-size: 48px; opacity: 0.3;"></i>
                                </div>
                            @endif

                            @php
                                $showBadge = false;
                                $badgeText = '';
                                $createdAt = data_get($kosan, 'created_at');
                                if ($createdAt && method_exists($createdAt, 'diffInDays') && $createdAt->diffInDays(now()) < 30) {
                                    $showBadge = true;
                                    $badgeText = 'Baru';
                                } elseif ((float) data_get($kosan, 'rating_rata', 0) >= 4.5) {
                                    $showBadge = true;
                                    $badgeText = 'Unggulan';
                                }
                            @endphp
                            @if ($showBadge)
                                <div class="kos-badge">{{ $badgeText }}</div>
                            @endif

                            @php
                                $isFavorited = Auth::check() && in_array(data_get($kosan, 'kosan_id', 0), $favorite_kosan_ids ?? []);
                            @endphp
                            <button class="wishlist-btn {{ $isFavorited ? 'active' : '' }}"
                                data-id="{{ data_get($kosan, 'kosan_id', 0) }}">
                                <i class="fa fa-heart"></i>
                            </button>
                        </div>
                        <div class="kos-content">
                            @php
                                $displayRating = (float) data_get($kosan, 'avgRating', data_get($kosan, 'rating_rata', 0));
                                $displayNama = data_get($kosan, 'nama_kosan', 'Kosan');
                                $displayKota = data_get($kosan, 'kota', '-');
                            @endphp
                            <div class="kos-rating">
                                <i class="fas fa-star"></i>
                                <span>{{ number_format($displayRating, 1) }}</span>
                            </div>
                            <h5 class="kos-title">{{ $displayNama }}</h5>
                            <p class="kos-location"><i class="fas fa-map-marker-alt me-1"></i> {{ $displayKota }}</p>
                            <div class="kos-facilities">
                            @php
                                // Get facilities from the cheapest kamar (safely)
                                $facilities = collect();
                                $kosanKamars = data_get($kosan, 'kamars');
                                if ($kosanKamars instanceof \Illuminate\Support\Collection && $kosanKamars->isNotEmpty()) {
                                    $firstKamar = $kosanKamars->first();
                                    $facilities = data_get($firstKamar, 'fasilitas', collect());
                                }
                            @endphp
                                @if (method_exists($facilities, 'where') && ($facilities->where('nama_fasilitas', 'WiFi')->first() || $facilities->where('nama_fasilitas', 'Wifi')->first()))
                                    <span><i class="fas fa-wifi"></i></span>
                                @endif
                                @if (method_exists($facilities, 'where') && ($facilities->where('nama_fasilitas', 'AC')->first() || $facilities->where('nama_fasilitas', 'Air Conditioner')->first()))
                                    <span><i class="fas fa-snowflake"></i></span>
                                @endif
                                @if (method_exists($facilities, 'where') && $facilities->where('nama_fasilitas', 'Kamar Mandi Dalam')->first())
                                    <span><i class="fas fa-bath"></i></span>
                                @endif
                            </div>
                            <div class="kos-price">
                                @php
                                    $hargaBulanan = (float) data_get($kosan, 'harga_bulanan', 0);
                                @endphp
                                <span class="price">Rp {{ number_format($hargaBulanan, 0, ',', '.') }}</span>
                                <span class="period">/ bulan</span>
                            </div>
                        </div>
                        @php
                            $displayKosanId = (int) data_get($kosan, 'kosan_id', 0);
                        @endphp
                        <div class="kos-action">
                            <a href="{{ route('users.kosan.show', $displayKosanId) }}"
                                class="btn btn-sm btn-primary w-100">Detail</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        Belum ada rekomendasi kosan untuk Anda saat ini.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Latest Bookings -->
    <div class="container-fluid mt-4">
        <div class="section-header d-flex justify-content-between align-items-center mb-3">
            <h2 class="section-title">Booking Terbaru</h2>
            <a href="{{ route('users.bookings.index') }}" class="view-all">Riwayat <i class="fas fa-arrow-right ms-1"></i></a>
        </div>

        <div class="card styled-table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table booking-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Kos</th>
                                <th>Tanggal</th>
                                <th>Durasi</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_bookings ?? [] as $booking)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <!-- FIXED: Better null checking for booking images -->
                                            @php
                                                $bookingFotoKosan = data_get($booking, 'kosan.foto_kosan.path_gambar') ?? data_get($booking, 'kosan.foto_kosan');
                                                $bookingNamaKosan = data_get($booking, 'kosan.nama_kosan', 'Kosan tidak tersedia');
                                                $nomorKamar = data_get($booking, 'kamar.nomor_kamar', '-');
                                                $hasValidBookingImage = is_string($bookingFotoKosan) &&
                                                                        trim($bookingFotoKosan) !== '' &&
                                                                        file_exists(storage_path('app/public/' . $bookingFotoKosan));
                                            @endphp

                                            @if ($hasValidBookingImage)
                                                <img src="{{ asset('storage/' . $bookingFotoKosan) }}"
                                                    alt="{{ $bookingNamaKosan }}" class="booking-image me-3"
                                                    onload="this.style.background='none'"
                                                    onerror="this.style.display='none'; this.onerror=null;">
                                            @else
                                                <div class="me-3" style="width: 40px; height: 40px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 8px; flex-shrink: 0;">
                                                    <i class="fas fa-image" style="font-size: 16px; color: #6c757d; opacity: 0.5;"></i>
                                                </div>
                                            @endif

                                            <div>
                                                <h6 class="mb-0">{{ $bookingNamaKosan }}</h6>
                                                <small class="text-muted">Kamar {{ $nomorKamar }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    @php
                                        $bookingTanggalMulai = data_get($booking, 'formatted_tanggal_mulai', '-');
                                        $bookingDurasi = data_get($booking, 'durasi_text', '-');
                                        $bookingTotalHarga = data_get($booking, 'formatted_total_harga', '-');
                                        $bookingStatusBadge = data_get($booking, 'status_badge', '<span class=\"badge bg-secondary\">-</span>');
                                        $bookingId = (int) data_get($booking, 'booking_id', 0);
                                    @endphp
                                    <td>{{ $bookingTanggalMulai }}</td>
                                    <td>{{ $bookingDurasi }}</td>
                                    <td>{{ $bookingTotalHarga }}</td>
                                    <td>{!! $bookingStatusBadge !!}</td>
                                    <td>
                                        <a href="{{ route('users.bookings.show', $bookingId) }}"
                                            class="btn btn-sm btn-outline-primary">Detail</a>
                                        </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3">Belum ada booking terbaru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Nearby Kos -->
    <div class="container-fluid mt-4">
        <div class="section-header d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <h2 class="section-title">Kos Terdekat</h2>
                <button id="refreshLocation" class="btn btn-sm btn-outline-primary rounded-circle" title="Refresh Lokasi">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <a href="{{ route('users.kosan.nearby') }}" class="view-all">Lihat Semua <i
                    class="fas fa-arrow-right ms-1"></i></a>
        </div>

        <div class="nearby-map-card">
            <div class="row g-0">
                <div class="col-lg-8">
                    <div class="map-container" id="nearby-map">
                        <!-- Map will be loaded here by JavaScript -->
                    </div>
                    <div id="locationStatus" class="p-2" style="display:none;"></div>
                </div>
                <div class="col-lg-4">
                    <div class="nearby-kos-list">
                        @forelse($nearby_kosans ?? [] as $index => $kosan)
                            @php
                                $nearbyFotoKosan = data_get($kosan, 'foto_kosan.path_gambar') ?? data_get($kosan, 'foto_kosan');
                                $nearbyNamaKosan = data_get($kosan, 'nama_kosan', 'Kosan');
                                $nearbyKosanId = (int) data_get($kosan, 'kosan_id', 0);
                                $nearbyLat = (float) data_get($kosan, 'latitude', 0);
                                $nearbyLng = (float) data_get($kosan, 'longitude', 0);
                                $nearbyDistance = data_get($kosan, 'distance_text', '- km');
                                $nearbyHarga = (float) data_get($kosan, 'harga_bulanan', 0);
                            @endphp
                            <div class="nearby-kos-item {{ $index == 0 ? 'active' : '' }}" data-id="{{ $nearbyKosanId }}"
                                data-lat="{{ $nearbyLat }}" data-lng="{{ $nearbyLng }}">
                                @if($nearbyFotoKosan && !empty($nearbyFotoKosan))
                                    <img src="{{ asset('storage/' . $nearbyFotoKosan) }}"
                                        alt="{{ $nearbyNamaKosan }}" class="nearby-kos-image">
                                @else
                                    <div class="nearby-kos-image" style="width: 60px; height: 60px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 8px; flex-shrink: 0;">
                                        <i class="fas fa-image" style="font-size: 20px; color: #6c757d; opacity: 0.5;"></i>
                                    </div>
                                @endif
                                <div class="nearby-kos-info">
                                    <h6>{{ $nearbyNamaKosan }}</h6>
                                    <p class="mb-0"><i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $nearbyDistance }} dari lokasi Anda</p>
                                    <div class="nearby-kos-price">Rp
                                        {{ number_format($nearbyHarga, 0, ',', '.') }} / bulan</div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center">
                                <p class="mb-0">Tidak ada kosan terdekat dari lokasi Anda saat ini.</p>
                                <p class="small text-muted mt-2">Pastikan Anda mengizinkan akses lokasi untuk melihat kosan
                                    terdekat.</p>
                                <button id="enableLocation" class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-map-marker-alt me-1"></i> Aktifkan Lokasi
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root {
            --primary: #4f6f52;
            --primary-dark: #3a4d39;
            --primary-light: #a4c3a2;
            --secondary: #eef5e4;
        }

        /* Welcome Section */
        .welcome-section {
            background: linear-gradient(135deg, rgba(79, 111, 82, 0.05) 0%, rgba(164, 195, 162, 0.05) 100%);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            margin-top: 0;
        }

        .welcome-section h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--primary-dark);
            line-height: 1.3;
        }

        .welcome-section p {
            font-size: 14px;
            line-height: 1.4;
            margin-bottom: 0;
        }

        .search-box {
            margin-top: 0;
        }

        .search-box .form-control {
            border-radius: 50px;
            padding: 10px 20px;
            border: 1px solid #dee2e6;
            font-size: 14px;
        }

        .search-box .btn {
            border-radius: 50px;
            padding: 10px 20px;
        }

        /* Mobile responsive welcome section - IMPROVED */
        @media (max-width: 768px) {
            .welcome-section {
                padding: 15px 12px;
                margin-bottom: 12px;
                margin-top: 0;
                border-radius: 10px;
            }

            .welcome-section h1 {
                font-size: 18px;
                margin-bottom: 4px;
                line-height: 1.2;
            }

            .welcome-section p {
                font-size: 13px;
                line-height: 1.3;
            }

            .search-box {
                margin-top: 10px;
            }

            .search-box .form-control {
                font-size: 13px;
                padding: 8px 15px;
            }

            .search-box .btn {
                padding: 8px 15px;
                font-size: 13px;
            }
        }

        @media (max-width: 576px) {
            .welcome-section {
                padding: 12px 10px;
                margin-bottom: 10px;
                border-radius: 8px;
            }

            .welcome-section h1 {
                font-size: 16px;
                margin-bottom: 3px;
            }

            .welcome-section p {
                font-size: 12px;
            }

            .search-box .form-control {
                font-size: 12px;
                padding: 7px 12px;
            }

            .search-box .btn {
                padding: 7px 12px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .welcome-section {
                padding: 10px 8px;
                margin-bottom: 8px;
            }

            .welcome-section h1 {
                font-size: 15px;
                margin-bottom: 2px;
            }

            .welcome-section p {
                font-size: 11px;
            }

            .search-box {
                margin-top: 8px;
            }

            .search-box .form-control {
                font-size: 11px;
                padding: 6px 10px;
            }

            .search-box .btn {
                padding: 6px 10px;
                font-size: 11px;
            }
        }

        @media (max-width: 375px) {
            .welcome-section {
                padding: 8px 6px;
                margin-bottom: 6px;
            }

            .welcome-section h1 {
                font-size: 14px;
                margin-bottom: 2px;
            }

            .welcome-section p {
                font-size: 10px;
            }

            .search-box .form-control {
                font-size: 10px;
                padding: 5px 8px;
            }

            .search-box .btn {
                padding: 5px 8px;
                font-size: 10px;
            }
        }

        /* Status Cards */
        .status-card {
            background-color: #4f6f52;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            height: 100%;
            transition: transform 0.3s;
            padding: 10px;
        }

        .status-card:hover {
            transform: translateY(-5px);
        }

        .status-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto;
            flex-shrink: 0;
        }

        .card-body {
            padding: 5px 8px;
        }

        .card-title {
            font-size: 18px;
            margin-bottom: 8px;
            line-height: 1.2;
            color: white;
        }

        .card-text {
            font-size: 16px;
            margin: 0;
            color: white;
        }

        /* Mobile responsive status cards */
        @media (max-width: 768px) {
            .status-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }

            .card-title {
                font-size: 14px;
                margin-bottom: 4px;
            }

            .card-text {
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {
            .status-card {
                padding: 8px;
            }

            .status-icon {
                width: 45px;
                height: 45px;
                font-size: 18px;
            }

            .card-body {
                padding: 4px 6px;
            }

            .card-title {
                font-size: 12px;
                margin-bottom: 2px;
            }

            .card-text {
                font-size: 11px;
            }
        }

        .bg-primary-light {
            background-color: rgba(79, 111, 82, 0.1);
        }

        .bg-success-light {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .bg-gray-light {
            /* background-color: rgba(255, 193, 7, 0.1); */
            background-color: rgba(248, 249, 250, 0.9);
        }

        /* Section Headers */
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 0;
        }

        .view-all {
            font-size: 14px;
            color: var(--primary);
            text-decoration: none;
        }

        .view-all:hover {
            text-decoration: underline;
        }

        /* Kos Cards */
        .kos-card {
            background-color: #e6e9ee;
            border: 1px solid #dcdcdc;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            height: 100%;
            transition: transform 0.3s;
            margin: 4px;
        }

        .styled-table-card {
            background-color: #e6e9ee;
            border: 1px solid #dcdcdc;
            margin: 4px;
        }

        .kos-card:hover {
            transform: translateY(-5px);
        }

        .kos-image-wrapper {
            position: relative;
            height: 160px;
        }

        .kos-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* FIXED: Remove background image from CSS - handle in PHP instead */
        .kos-image,
        .booking-image,
        .nearby-kos-image {
            transition: opacity 0.3s ease;
        }

        .kos-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: var(--primary);
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .wishlist-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 32px;
            height: 32px;
            background-color: white;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #ccc;
        }

        .wishlist-btn.active {
            color: #ff6b6b;
        }

        .kos-content {
            padding: 15px;
        }

        .kos-rating {
            margin-bottom: 10px;
            color: #ffc107;
        }

        .kos-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .kos-location {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .kos-facilities {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
            color: #6c757d;
        }

        .kos-price {
            margin-bottom: 10px;
        }

        .kos-price .price {
            font-weight: 700;
            color: var(--primary);
            font-size: 16px;
        }

        .kos-price .period {
            color: #6c757d;
            font-size: 13px;
        }

        .kos-action {
            padding: 0 15px 15px;
        }

        /* Booking Table */
        .booking-table th {
            font-weight: 600;
            color: var(--dark);
        }

        .booking-image {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
        }

        /* Nearby Map */
        .nearby-map-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .map-container {
            height: 300px;
        }

        .nearby-kos-list {
            height: 300px;
            overflow-y: auto;
        }

        .nearby-kos-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .nearby-kos-item:hover,
        .nearby-kos-item.active {
            background-color: #f8f9fa;
        }

        .nearby-kos-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
        }

        .nearby-kos-info h6 {
            font-size: 15px;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .nearby-kos-info p {
            font-size: 12px;
            color: #6c757d;
        }

        .nearby-kos-price {
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
        }

        .leaflet-container {
            height: 300px;
            border-radius: 8px;
        }

        .user-location-marker {
            background-color: #4285F4;
            border: 3px solid white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
        }

        .kosan-marker {
            background-color: #4f6f52;
            border: 2px solid white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map;
        let markers = [];
        let userMarker;
        let userLocation = null;
        let requestingLocation = false;

        function initMap() {
            // Default center di Indonesia
            const defaultCenter = [-2.548926, 118.0148634];

            try {
                // Initialize Leaflet map
                map = L.map('nearby-map').setView(defaultCenter, 5);

                // Add OpenStreetMap tiles (FREE!)
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Custom icons
                const userIcon = L.divIcon({
                    className: 'user-location-marker',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });

                const kosanIcon = L.divIcon({
                    className: 'kosan-marker',
                    iconSize: [16, 16],
                    iconAnchor: [8, 8]
                });

                // Check cached location first
                const cachedLat = localStorage.getItem('userLat');
                const cachedLng = localStorage.getItem('userLng');

                if (cachedLat && cachedLng) {
                    userLocation = [parseFloat(cachedLat), parseFloat(cachedLng)];
                    map.setView(userLocation, 13);
                    userMarker = L.marker(userLocation, { icon: userIcon })
                        .addTo(map)
                        .bindPopup('Lokasi Anda');
                    if (!document.querySelector('.nearby-kos-item')) {
                        loadNearbyKosans();
                    } else {
                        initKosanMarkers();
                    }
                } else {
                    initKosanMarkers();
                }

            } catch (error) {
                console.error('Error initializing map:', error);
                document.getElementById("nearby-map").innerHTML =
                    '<div class="d-flex align-items-center justify-content-center h-100">' +
                    '<p class="text-muted">Terjadi kesalahan saat memuat peta.</p>' +
                    '</div>';
            }

            // Event listener untuk tombol aktifkan lokasi
            const enableLocationBtn = document.getElementById('enableLocation');
            if (enableLocationBtn) {
                enableLocationBtn.addEventListener('click', requestUserLocation);
            }
        }

        function initKosanMarkers() {
            const kosanItems = document.querySelectorAll('.nearby-kos-item');

            if (kosanItems.length === 0) {
                return;
            }

            const kosanIcon = L.divIcon({
                className: 'kosan-marker',
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            });

            kosanItems.forEach((item, index) => {
                const lat = parseFloat(item.dataset.lat);
                const lng = parseFloat(item.dataset.lng);
                const id = item.dataset.id;

                if (!isNaN(lat) && !isNaN(lng)) {
                    const position = [lat, lng];
                    const title = item.querySelector('h6').textContent;
                    const location = item.querySelector('p').innerHTML;
                    const price = item.querySelector('.nearby-kos-price').textContent;

                    const marker = L.marker(position, {
                            icon: kosanIcon
                        })
                        .addTo(map)
                        .bindPopup(`
                        <div style="min-width: 200px; max-width: 250px;">
                            <h6 style="margin-bottom: 5px; font-size: 14px;">${title}</h6>
                            <p style="margin-bottom: 5px; font-size: 12px; color: #666;">${location}</p>
                            <p style="font-weight: 600; color: #4f6f52; margin-bottom: 10px; font-size: 13px;">${price}</p>
                            <a href=\"/users/kosan/${id}\" class=\"btn btn-sm btn-primary\" style=\"font-size: 12px;\">Detail</a>
                        </div>
                    `);

                    markers.push(marker);

                    // Add click event to marker
                    marker.on('click', () => {
                        highlightKosanItem(item, kosanItems);
                    });

                    // Add click event to list item
                    item.addEventListener('click', () => {
                        map.setView(position, 15);
                        marker.openPopup();
                        highlightKosanItem(item, kosanItems);
                    });

                    // Open first marker popup
                    if (index === 0) {
                        setTimeout(() => {
                            marker.openPopup();
                        }, 500);
                    }
                }
            });

            // Fit map to show all markers
            if (markers.length > 0) {
                const group = new L.featureGroup(markers);
                if (userMarker) group.addLayer(userMarker);
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }

        function highlightKosanItem(activeItem, allItems) {
            allItems.forEach(el => el.classList.remove('active'));
            activeItem.classList.add('active');
        }

        function loadNearbyKosans() {
            if (!userLocation) return;

            const container = document.querySelector('.nearby-kos-list');

            // Show loading
            container.innerHTML = `
            <div class="p-4 text-center">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Mencari kosan terdekat...</p>
            </div>
        `;

            fetch(`/api/nearby-kosans?lat=${userLocation[0]}&lng=${userLocation[1]}`, {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!Array.isArray(data) || data.length === 0) {
                        container.innerHTML = `
                    <div class="p-4 text-center">
                        <p class="mb-0">Tidak ada kosan terdekat dari lokasi Anda saat ini.</p>
                        <p class="small text-muted mt-2">Radius pencarian: 10 km</p>
                    </div>
                `;
                        return;
                    }

                    container.innerHTML = '';
                    data.forEach((kosan, index) => {
                        const nama = kosan.nama_kosan || kosan.nama_kos || 'Kosan';
                        const img = kosan.foto_kosan || kosan.foto_kosan;
                        const imageHtml = img
                            ? `<img src="/storage/${img}" alt="${nama}" class="nearby-kos-image">`
                            : `<div class="nearby-kos-image" style="width: 60px; height: 60px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 8px; flex-shrink: 0;"><i class="fas fa-image" style="font-size: 20px; color: #6c757d; opacity: 0.5;"></i></div>`;

                        container.innerHTML += `
                    <div class="nearby-kos-item ${index === 0 ? 'active' : ''}"
                         data-id="${kosan.kosan_id}"
                         data-lat="${kosan.latitude}"
                         data-lng="${kosan.longitude}">
                        ${imageHtml}
                        <div class="nearby-kos-info">
                            <h6>${nama}</h6>
                            <p class="mb-0"><i class="fas fa-map-marker-alt me-1"></i> ${kosan.distance_text} dari lokasi Anda</p>
                            <div class="nearby-kos-price">Rp ${formatNumber(kosan.harga_bulanan)} / bulan</div>
                        </div>
                    </div>
                `;
                    });

                    // Init markers after adding items
                    setTimeout(() => {
                        initKosanMarkers();
                    }, 100);
                })
                .catch(error => {
                    console.error('Error loading nearby kosans:', error);
                    container.innerHTML = `
                <div class="p-4 text-center">
                    <p class="mb-0 text-danger">Gagal memuat kosan terdekat.</p>
                    <button class="btn btn-sm btn-outline-primary mt-2" onclick="loadNearbyKosans()">
                        <i class="fas fa-redo me-1"></i> Coba Lagi
                    </button>
                </div>
            `;
                });
        }

        function requestUserLocation() {
            if (navigator.geolocation) {
                if (requestingLocation) return;
                requestingLocation = true;
                showLocationStatus('Meminta izin lokasi...', 'info');

                const tryLowAccuracy = () => {
                    navigator.geolocation.getCurrentPosition(
                        (pos2) => {
                            localStorage.setItem('userLat', pos2.coords.latitude);
                            localStorage.setItem('userLng', pos2.coords.longitude);
                            requestingLocation = false;
                            showLocationStatus('Lokasi diperoleh', 'success');
                            window.location.reload();
                        },
                        (err2) => {
                            console.error('Low accuracy location error:', err2);
                            requestingLocation = false;
                            let msg2 = 'Gagal mendapatkan lokasi. ';
                            if (err2.code === err2.PERMISSION_DENIED) msg2 += 'Akses lokasi ditolak. Mohon aktifkan di pengaturan browser.';
                            else if (err2.code === err2.POSITION_UNAVAILABLE) msg2 += 'Lokasi tidak tersedia.';
                            else if (err2.code === err2.TIMEOUT) msg2 += 'Permintaan lokasi melebihi batas waktu.';
                            else msg2 += 'Terjadi kesalahan yang tidak diketahui.';
                            const hasCache = !!(localStorage.getItem('userLat') && localStorage.getItem('userLng'));
                            showLocationStatus(msg2 + (hasCache ? ' Menggunakan lokasi tersimpan.' : ''), 'danger', true);
                        },
                        { enableHighAccuracy: false, timeout: 25000, maximumAge: 900000 }
                    );
                };

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        localStorage.setItem('userLat', position.coords.latitude);
                        localStorage.setItem('userLng', position.coords.longitude);
                        requestingLocation = false;
                        showLocationStatus('Lokasi diperoleh', 'success');
                        window.location.reload();
                    },
                    (error) => {
                        console.error('High accuracy location error:', error);
                        if (error.code === error.TIMEOUT || error.code === error.POSITION_UNAVAILABLE) {
                            showLocationStatus('Mencoba ulang dengan akurasi standar...', 'info');
                            tryLowAccuracy();
                            return;
                        }
                        let message = 'Gagal mendapatkan lokasi. ';
                        if (error.code === error.PERMISSION_DENIED) message += 'Akses lokasi ditolak. Mohon aktifkan di pengaturan browser.';
                        else message += 'Terjadi kesalahan saat memproses lokasi.';
                        requestingLocation = false;
                        showLocationStatus(message, 'danger', true);
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 300000 }
                );
            } else {
                showLocationStatus("Browser Anda tidak mendukung Geolocation.", 'danger');
            }
        }

        function showLocationStatus(text, type = 'info', retry = false) {
            const el = document.getElementById('locationStatus');
            el.style.display = 'block';
            el.className = `p-2 alert alert-${type}`;
            el.innerHTML = `
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span>${text}</span>
                    </div>
                    <div>
                        ${retry ? `<button id="retryLocation" class="btn btn-sm btn-outline-primary">Coba Lagi</button>` : ''}
                    </div>
                </div>
                ${type === 'danger' ? `<div class="mt-2 small text-muted">Jika tetap gagal: pastikan izin lokasi aktif, aktifkan GPS, coba koneksi lain, atau buka menu "Peta Lokasi Kos".</div>` : ''}
            `;
            const btn = document.getElementById('retryLocation');
            if (btn) {
                btn.onclick = requestUserLocation;
            }
        }

        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            initMap();

            // Handle refresh location button
            const refreshBtn = document.getElementById('refreshLocation');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    if (icon) icon.classList.add('fa-spin');
                    requestUserLocation();
                });
            }

            // Wishlist toggle functionality
            document.querySelectorAll('.wishlist-btn').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation(); // Mencegah card ter-klik jika ada link
                    
                    const kosanId = this.dataset.id;
                    if (!kosanId || kosanId == 0) return;

                    // Logika jika belum login
                    const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
                    if (!isLoggedIn) {
                        this.classList.add('active'); // Merah sekilas
                        setTimeout(() => {
                            this.classList.remove('active'); // Kembali semula
                            // Opsional: tampilkan info login jika diperlukan
                        }, 800);
                        return;
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    // Perbaikan URL: Hapus 'toggle-' karena di rute aslinya hanya '/favorite'
                    const url = `{{ url('users/kosan') }}/${kosanId}/favorite`;

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const isAdded = data.action === 'added';
                            this.classList.toggle('active', isAdded);
                            
                            // Perubahan Ikon: fas (isi) untuk favorit, far (kosong) jika dihapus
                            const icon = this.querySelector('i');
                            if (icon) {
                                if (isAdded) {
                                    icon.classList.remove('far');
                                    icon.classList.add('fas');
                                } else {
                                    icon.classList.remove('fas');
                                    icon.classList.add('far');
                                }
                            }
                            
                            // Update angka di dashboard secara real-time
                            const display = document.getElementById('wishlist-count-display');
                            if (display) {
                                let currentCount = parseInt(display.innerText) || 0;
                                display.innerText = isAdded ? currentCount + 1 : Math.max(0, currentCount - 1);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Revert UI jika gagal
                        this.classList.toggle('active');
                    });
                });
            });
        });
    </script>
@endpush
