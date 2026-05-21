@extends('layouts.user.app')

@section('title', $kosan->nama_kosan)

@section('content')
    <!-- Breadcrumb -->
    <div class="container-fluid mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('users.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.kosan.index') }}">Daftar Kosan</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $kosan->nama_kosan }}</li>
            </ol>
        </nav>
    </div>

    <!-- Kosan Detail -->
    <div class="container-fluid" id="kosan-detail-container" data-kosan-id="{{ $kosan?->kosan_id }}"
        data-monthly-price="{{ (float) $kosan?->harga_setelah_diskon }}"
        data-yearly-price="{{ (float) $kosan?->harga_tahunan_setelah_diskon }}"
        data-availability-url="{{ route('api.kosan.availability', ['id' => $kosan?->kosan_id ?? 0]) }}">
        <div class="row">
            <!-- Left Column - Photos & Details -->
            <div class="col-lg-8 mb-4">
                @php
                    $allPhotos = collect([]);
                    // 1. Add Kosan Main Photo
                    if ($kosan->foto_kosan) {
                        $allPhotos->push(['path' => $kosan->foto_kosan, 'type' => 'Utama']);
                    }
                    // 2. Add Other Kosan Photos
                    foreach ($kosan->fotos as $foto) {
                        if ($foto?->path_foto) {
                            $allPhotos->push(['path' => $foto->path_foto, 'type' => 'Lainnya']);
                        }
                    }
                    // 3. Add Kamar Photos
                    foreach (($kosan?->kamars ?? []) as $kamar) {
                        // Add Kamar Main Photo
                        if ($kamar?->foto_kamar) {
                            $allPhotos->push(['path' => $kamar?->foto_kamar, 'type' => 'Kamar ' . ($kamar?->nomor_kamar ?? 'N/A')]);
                        }
                        // Add Other Kamar Photos
                        foreach (($kamar?->fotos ?? []) as $foto) {
                            if ($foto?->path_foto) {
                                $allPhotos->push(['path' => $foto->path_foto, 'type' => 'Kamar ' . ($kamar?->nomor_kamar ?? 'N/A')]);
                            }
                        }
                    }
                    // Set the main image for initial display
                    $mainImagePath = $allPhotos->isNotEmpty() ? $allPhotos->first()['path'] : null;
                @endphp
                <!-- Image Gallery -->
                <div class="kosan-gallery card" style="background-color: #e6e9ee; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                    <div class="main-image-container">
                        @if ($mainImagePath)
                            <img src="{{ asset('storage/' . $mainImagePath) }}" alt="{{ $kosan->nama_kosan }}" class="main-image" id="mainImage" style="border: 2px solid #ddd; box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2); width: 100%; object-fit: cover; border-radius: 8px;">
                        @else
                            <img src="{{ asset('images/no-image.jpg') }}" alt="{{ $kosan->nama_kosan }}" class="main-image" style="border: 2px solid #ddd; box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2); width: 100%; object-fit: cover; border-radius: 8px;">
                        @endif
                    </div>

                    <!-- Thumbnail Gallery -->
                    @if ($allPhotos->count() > 1)
                        <div class="thumbnail-gallery mt-3 p-3">
                            <h6 class="mb-2">Galeri Foto</h6>
                            <div class="row g-2">
                                @foreach ($allPhotos as $index => $photo)
                                    <div class="col-3 col-md-2">
                                        <div class="thumbnail-wrapper" style="cursor: pointer; position: relative; overflow: hidden; border-radius: 8px; transition: all 0.3s ease;">
                                            <img src="{{ asset('storage/' . $photo['path']) }}" alt="{{ $photo['type'] }}" class="thumbnail-image" onclick="changeMainImage('{{ asset('storage/' . $photo['path']) }}', this)" style="width: 100%; height: 80px; object-fit: cover; border: 2px solid {{ $index == 0 ? '#007bff' : '#ddd' }}; border-radius: 6px; transition: all 0.3s ease;">
                                            <span class="badge bg-dark position-absolute top-0 inset-s-0 m-1" style="font-size: 9px; opacity: 0.8;">{{ $photo['type'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Kosan Title & Basic Info -->
                <div class="kosan-info card mt-4 border-0 shadow-sm" style="background-color: #f8fafc; border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h1 class="kosan-detail-title">{{ $kosan->nama_kosan }}</h1>
                                
                                <div class="kosan-rating-mini d-flex align-items-center mb-3">
                                    <div class="text-warning me-2">
                                        @php
                                            $ratingRata = $kosan->rating_rata ?? 0;
                                            $roundedRating = round($ratingRata);
                                        @endphp
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= $roundedRating ? '' : '-o' }} {{ $i <= $roundedRating ? '' : 'text-muted opacity-25' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="fw-bold me-1" style="font-size: 0.95rem;">{{ number_format($ratingRata, 1) }}</span>
                                    <span class="text-muted" style="font-size: 0.85rem;">({{ ($kosan->ulasanKosan ?? collect())->count() }} ulasan)</span>
                                </div>

                                <p class="kosan-detail-address d-flex align-items-start">
                                    <i class="fas fa-map-marker-alt me-2 text-danger mt-1"></i>
                                    <span>{{ $kosan->alamat }}, {{ $kosan->kecamatan }}, {{ $kosan->kota }}</span>
                                </p>

                                <div class="d-flex flex-wrap gap-2 mb-2 mb-lg-0">
                                    <span class="badge {{ $kosan->tipe_kosan == 'putra' ? 'bg-primary' : ($kosan->tipe_kosan == 'putri' ? 'bg-danger' : 'bg-success') }} px-3 py-2 rounded-pill shadow-sm">
                                        Kos {{ ucfirst($kosan->tipe_kosan) }}
                                    </span>
                                    @php
                                        $kamarsList = $kosan?->kamars ?? [];
                                        $availableCountTitle = collect($kamarsList)->filter(fn($r) => $r->is_available ?? false)->count();
                                    @endphp
                                    <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm">
                                        <i class="fas fa-door-open me-1 text-primary"></i> {{ $availableCountTitle }} Kamar Tersedia
                                    </span>
                                    @if ($kosan->kos_unggulan)
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm">
                                            <i class="fas fa-star me-1"></i>Unggulan
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-4 d-flex justify-content-lg-end gap-2 mt-4 mt-lg-0">
                                <button class="btn btn-white share-button flex-fill flex-lg-grow-0" onclick="shareKosan()">
                                    <i class="fas fa-share-alt me-2 text-primary"></i>Bagikan
                                </button>
                                <button
                                    class="btn {{ $kosan->difavoritkanOleh(Auth::id()) ? 'btn-danger' : 'btn-outline-danger' }} wishlist-btn flex-fill flex-lg-grow-0"
                                    data-kosan-id="{{ $kosan->kosan_id }}"
                                    data-favorit-url="{{ route('users.kosan.toggle-favorite', $kosan->kosan_id) }}">
                                    <i class="fa{{ $kosan->difavoritkanOleh(Auth::id()) ? 's' : 'r' }} fa-heart me-2"></i>
                                    <span>Favorit</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Facilities -->
                <div class="kosan-facilities-section card mt-4" style="background-color: #e6e9ee;">
                    <div class="card-body">
                        <div class="alert alert-info border-0 shadow-sm"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <div class="d-flex align-items-start">
                                <div class="shrink-0">
                                    <div class="info-icon-wrapper">
                                        <i class="fas fa-info-circle" style="font-size: 24px;"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-2 fw-bold" style="color: white;">Informasi Fasilitas</h6>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 14px;">
                                        Pilih <strong>kamar yang Anda inginkan</strong> pada bagian "Pilih Kamar" di sebelah
                                        kanan untuk melihat fasilitas lengkap yang tersedia. Setiap kamar memiliki fasilitas
                                        yang berbeda-beda.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Display selected room facilities -->
                        <div id="selectedRoomFacilities" class="selected-facilities-wrapper" style="display: none;">
                            <div class="facilities-header mt-4 mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 class="mb-0 fw-bold">
                                        Fasilitas Kamar <span id="selectedRoomNumber" class="badge bg-primary"></span>
                                    </h5>
                                    <span class="badge bg-success" id="facilitiesCount"></span>
                                </div>
                                <hr class="mt-3 mb-4">
                            </div>
                            <div class="row g-3" id="facilitiesList"></div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="kosan-description card mt-4" style="background-color: #e6e9ee;">
                    <div class="card-body">
                        <h3 class="section-title">Deskripsi</h3>
                        <div class="description-content mt-3">
                            {{ $kosan->deskripsi }}
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="kosan-location-section card mt-4" style="background-color: #e6e9ee;">
                    <div class="card-body">
                        <h3 class="section-title">Lokasi</h3>
                        <div class="location-info mt-3">
                            <p><i class="fas fa-map-marker-alt me-2"></i> {{ $kosan->alamat }}, {{ $kosan->kecamatan }},
                                {{ $kosan->kota }}, {{ $kosan->provinsi }} {{ $kosan->kode_pos }}</p>
                        </div>
                        @if ($kosan->latitude && $kosan->longitude)
                            <div class="location-map mt-3" id="map" style="height: 300px;"></div>
                        @else
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i> Lokasi kosan belum tersedia di peta.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Reviews -->
                <div class="kosan-reviews card mt-4" style="background-color: #e6e9ee;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="section-title">Ulasan ({{ ($kosan->ulasanKosan ?? collect())->count() }})</h3>
                            @auth
                                @if ($hasActiveBookingForThisKosan)
                                    <a href="{{ route('users.kosan.review-form', $kosan->kosan_id) }}"
                                        class="btn btn-primary btn-sm">{{ $userHasReviewed ? 'Edit Ulasan' : 'Tulis Ulasan' }}</a>
                                @endif
                            @endauth
                        </div>

                        <div class="review-summary mt-3">
                            <div class="row align-items-center">
                                <div class="col-md-4 text-center mb-3 mb-md-0">
                                    <div class="average-rating">
                                        @php
                                            $ratingRataReview = $kosan->rating_rata ?? 0;
                                            $roundedRatingReview = round($ratingRataReview);
                                        @endphp
                                        <div class="rating-number">
                                            {{ number_format($ratingRataReview, 1) }}</div>
                                        <div class="rating-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $roundedRatingReview)
                                                    <i class="fas fa-star"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <div class="rating-count mt-2">{{ ($kosan->ulasanKosan ?? collect())->count() }}
                                            ulasan
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="rating-bars">
                                        @php
                                            $totalReviews = ($kosan->ulasanReview ?? collect())->count();
                                            $reviewCounts = [
                                                5 => $kosan->ulasanKosan()->where('rating', 5)->count(),
                                                4 => $kosan->ulasanKosan()->where('rating', 4)->count(),
                                                3 => $kosan->ulasanKosan()->where('rating', 3)->count(),
                                                2 => $kosan->ulasanKosan()->where('rating', 2)->count(),
                                                1 => $kosan->ulasanKosan()->where('rating', 1)->count(),
                                            ];
                                        @endphp

                                        @for ($i = 5; $i >= 1; $i--)
                                            <div class="rating-bar-item">
                                                <div class="rating-label">
                                                    <span>{{ $i }}</span>
                                                    <i class="fas fa-star"></i>
                                                </div>
                                                <div class="rating-progress-container">
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar"
                                                            style="width: {{ $totalReviews > 0 ? ($reviewCounts[$i] / $totalReviews) * 100 : 0 }}%"
                                                            aria-valuenow="{{ $reviewCounts[$i] }}" aria-valuemin="0"
                                                            aria-valuemax="{{ $totalReviews }}"></div>
                                                    </div>
                                                </div>
                                                <div class="rating-bar-count">
                                                    {{ $reviewCounts[$i] }}
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="review-list mt-4">
                            @forelse($kosan->ulasanKosan()->with('user')->latest()->take(5)->get() as $ulasan)
                                <div class="review-item">
                                    <div class="review-header d-flex justify-content-between flex-wrap">
                                        <div class="d-flex align-items-center">
                                            <div class="reviewer-avatar me-2">
                                                @php
                                                    $profilePhoto = ($ulasan->user?->foto_profil)
                                                        ? asset('storage/' . $ulasan->user?->foto_profil)
                                                        : asset('images/user-avatar.png');
                                                @endphp
                                                <img src="{{ $profilePhoto }}" alt="Reviewer" class="rounded-circle" width="40">
                                            </div>
                                            <div class="reviewer-info">
                                                <div class="reviewer-name">
                                                    {{ $ulasan->user?->nama_lengkap ?? ($ulasan->user?->username ?? 'Anonymous') }}
                                                </div>
                                                <div class="review-date text-muted">
                                                    {{ $ulasan->created_at?->format('d M Y') ?? '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="review-rating">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= ($ulasan->rating ?? 0))
                                                    <i class="fas fa-star"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="review-content mt-2">
                                        {{ $ulasan->komentar ?? '' }}
                                    </div>
                                </div>
                            @empty
                                <div class="empty-reviews text-center py-4">
                                    <i class="far fa-comment-dots fa-3x text-muted mb-3"></i>
                                    <h5>Belum Ada Ulasan</h5>
                                    <p class="text-muted">Jadilah yang pertama memberikan ulasan untuk kosan ini!</p>
                                </div>
                            @endforelse

                            @if (($kosan->ulasanReview ?? collect())->count() > 5)
                                <div class="text-center mt-3">
                                    <button class="btn btn-outline-primary" id="loadMoreReviews">Lihat Lebih
                                        Banyak</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Book & Similar Kosans -->
            <div class="col-lg-4">
                <!-- Booking Card -->
                <div class="booking-card card sticky-top" style="top: 90px; background-color: #e6e9ee;">
                    <div class="card-body">
                        <h3 class="booking-title">Booking Kamar</h3>

                        <div class="availability-info mt-3">
                            @php
                                $kamarsList = $kosan?->kamars ?? [];
                                $rooms = collect($kamarsList)->filter(function($r) {
                                    return $r && method_exists($r, '__get');
                                });
                                $availableCountInitial = $rooms->filter(function($r) {
                                    return $r && method_exists($r, '__get') && ($r->is_available ?? false);
                                })->count();
                                $occupiedCount = $rooms->filter(function($r) {
                                    return $r && method_exists($r, '__get') && (($r->status_kamar ?? null) === 'terisi');
                                })->count();
                                $maintenanceCount = $rooms->filter(function($r) {
                                    return ($r && method_exists($r, 'isUnderMaintenance')) ? $r->isUnderMaintenance() : false;
                                })->count();
                            @endphp
                            <div
                                class="availability-badge {{ $availableCountInitial > 0 ? 'available' : 'unavailable' }}">
                                @if ($availableCountInitial > 0)
                                    <i class="fas fa-check-circle me-2"></i>{{ $availableCountInitial }} kamar tersedia
                                @elseif ($maintenanceCount > 0 && $occupiedCount === 0)
                                    <i class="fas fa-tools me-2"></i> Dalam pemeliharaan
                                @else
                                    <i class="fas fa-times-circle me-2"></i> Tidak tersedia
                                @endif
                            </div>
                        </div>

                        @if (($availableCountInitial ?? 0) > 0)
                            <div class="kamar-selection mt-3">
                                <label for="jumlahKamar" class="form-label">Jumlah Kamar</label>
                                <select class="form-select" id="jumlahKamar" name="jumlah_kamar">
                                    @for ($i = 1; $i <= min(3, $availableCountInitial ?? 0); $i++)
                                        <option value="{{ $i }}">{{ $i }} Kamar</option>
                                    @endfor
                                </select>
                                <small class="text-muted">Maksimal pemesanan 3 kamar per pengguna</small>
                            </div>
                        @endif

                        <div class="booking-price mt-3">
                            <div class="price-label">Harga per Kamar</div>
                            <div class="price-value">
                                @if ($kosan->persentase_diskon > 0)
                                    <span class="price-old">Rp
                                        {{ number_format($kosan->harga_bulanan, 0, ',', '.') }}</span>
                                    <span class="price-current" id="perRoomPrice">Rp
                                        {{ number_format($kosan->getHargaSetelahDiskonAttribute(), 0, ',', '.') }}</span>
                                @else
                                    <span class="price-current" id="perRoomPrice">Rp
                                        {{ number_format($kosan->harga_bulanan, 0, ',', '.') }}</span>
                                @endif
                                <span class="price-period">/ bulan</span>
                            </div>
                        </div>

                        <div class="room-picker mt-3">
                            <label class="form-label fw-bold">Pilih Kamar</label>
                            <div class="row g-2" id="roomPicker">
                                @forelse (($kosan?->kamars ?? []) as $room)
                                    @php
                                        $isAvailable = $room && method_exists($room, '__get') ? ($room->is_available ?? false) : false;
                                        $fotoKamar = $room && method_exists($room, '__get') ? ($room->foto_kamar ?? null) : null;
                                        $kamarId = $room && method_exists($room, '__get') ? ($room->kamar_id ?? '') : '';
                                        $nomorKamar = $room && method_exists($room, '__get') ? ($room->nomor_kamar ?? '') : '';
                                        $hargaSetelahDiskon = $room && method_exists($room, '__get') ? ($room->harga_setelah_diskon ?? 0) : 0;
                                        $statusKamar = $room && method_exists($room, '__get') ? ($room->status_kamar ?? null) : null;
                                        $isMaint = ($room && method_exists($room, 'isUnderMaintenance')) ? $room->isUnderMaintenance() : false;
                                    @endphp
                                    <div class="col-6">
                                        <button type="button"
                                            class="room-card btn w-100 text-start {{ $isAvailable ? '' : 'disabled' }}"
                                            data-kamar-id="{{ $kamarId }}"
                                            data-nomor="{{ $nomorKamar }}"
                                            data-price="{{ (float) $hargaSetelahDiskon }}"
                                            data-image="{{ $fotoKamar ? asset('storage/' . $fotoKamar) : asset('images/no-image.jpg') }}"
                                            {{ $isAvailable ? '' : 'disabled' }}>
                                            
                                            <div class="room-card-image mb-2">
                                                @if ($fotoKamar)
                                                    <img src="{{ asset('storage/' . $fotoKamar) }}"
                                                        alt="Kamar {{ $nomorKamar }}">
                                                @else
                                                    <img src="{{ asset('images/no-image.jpg') }}"
                                                        alt="Kamar {{ $nomorKamar }}">
                                                @endif
                                                
                                                @if (!$isAvailable)
                                                    <div class="room-badge-overlay">
                                                        @if ($statusKamar === 'terisi')
                                                            <span class="badge bg-secondary">Terisi</span>
                                                        @elseif($isMaint)
                                                            <span class="badge bg-warning text-dark">Maint.</span>
                                                        @else
                                                            <span class="badge bg-secondary">Full</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="room-card-info">
                                                <div class="room-name">Kamar {{ $nomorKamar }}</div>
                                                <div class="room-price-small">
                                                    Rp {{ number_format($hargaSetelahDiskon, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </button>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <p class="text-muted text-center">Tidak ada kamar tersedia saat ini.</p>
                                    </div>
                                @endforelse
                            </div>
                            <small class="text-muted mt-2 d-block">Pilih kamar untuk melihat detail harga dan fasilitas.</small>
                        </div>

                        <div class="booking-duration mt-3">
                            <label for="bookingDuration" class="form-label">Pilih Durasi</label>
                            <select class="form-select" id="bookingDuration">
                                <option value="bulanan" data-value="1"
                                    data-price="{{ $kosan->getHargaSetelahDiskonAttribute() }}">Bulanan (custom)</option>
                                @if ($kosan->harga_tiga_bulan)
                                    <option value="tiga_bulan" data-value="3"
                                        data-price="{{ $kosan->harga_tiga_bulan }}">Paket 3 Bulan</option>
                                @endif
                                @if ($kosan->harga_semester)
                                    <option value="semester" data-value="6" data-price="{{ $kosan->harga_semester }}">
                                        Paket 6 Bulan</option>
                                @endif
                                <option value="tahunan" data-value="12"
                                    data-price="{{ $kosan->getHargaTahunanSetelahDiskonAttribute() }}">Tahunan (custom)
                                </option>
                            </select>
                            <div class="mt-2" id="input_bulanan_custom_show" style="display:none;">
                                <label for="bulan_custom_show" class="form-label">Jumlah Bulan (1–11)</label>
                                <input type="number" class="form-control" id="bulan_custom_show" min="1"
                                    max="11" value="1">
                            </div>
                            <div class="mt-2" id="input_tahunan_custom_show" style="display:none;">
                                <label for="tahun_custom_show" class="form-label">Jumlah Tahun (≥1)</label>
                                <input type="number" class="form-control" id="tahun_custom_show" min="1"
                                    value="1">
                            </div>
                        </div>

                        <div class="booking-date mt-3">
                            <label for="bookingDate" class="form-label fw-bold">Tanggal Mulai</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                </span>
                                <input type="date" class="form-control border-start-0 ps-0" id="bookingDate" min="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="total-price mt-3">
                            <div class="d-flex justify-content-between">
                                <span>Total</span>
                                <span class="fw-bold" id="totalPrice">Rp
                                    {{ number_format($kosan->getHargaSetelahDiskonAttribute(), 0, ',', '.') }}</span>
                            </div>
                            <small class="text-muted" id="priceDetails">Harga untuk jumlah kamar dan durasi
                                terpilih</small>
                        </div>

                        <div class="booking-action mt-3">
                            @php
                                $kamarsList = $kosan?->kamars ?? [];
                                $availableCountInitial = collect($kamarsList)
                                    ->filter(function ($r) {
                                        return $r && method_exists($r, '__get') && ($r->is_available ?? false);
                                    })
                                    ->count();
                            @endphp
                            <form action="{{ route('users.kosan.booking-form', $kosan->kosan_id) }}" method="GET">
                                <input type="hidden" name="durasi" id="inputDurasi" value="bulanan">
                                <input type="hidden" name="tanggal_mulai" id="inputTanggalMulai"
                                    value="{{ date('Y-m-d') }}">
                                <input type="hidden" name="jumlah_kamar" id="inputJumlahKamar" value="1">
                                <input type="hidden" name="nilai_durasi" id="inputNilaiDurasi" value="1">
                                <input type="hidden" name="kamar_id" id="inputKamarId" value="">
                                @php $buttonDisabled = ($availableCountInitial <= 0) || ($hasActiveBooking ?? false); @endphp
                                <button type="submit"
                                    class="btn {{ $buttonDisabled ? 'btn-secondary' : 'btn-primary' }} btn-lg w-100"
                                    {{ $buttonDisabled ? 'disabled' : '' }} id="bookingSubmitBtn">Booking
                                    Sekarang</button>
                            </form>
                            @if ($hasActiveBooking ?? false)
                                <small class="text-danger d-block mt-2"><i class="fas fa-exclamation-circle me-1"></i>
                                    Anda memiliki booking aktif. Selesaikan terlebih dahulu sebelum melakukan booking
                                    baru.</small>
                            @endif
                        </div>

                        <div class="booking-description mt-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Booking ini untuk pemesanan kamar kosan, bukan
                                seluruh bangunan kosan.
                            </div>
                        </div>

                        <div class="booking-contact mt-3">
                            @if ($kosan->pemilik)
                                <span class="contact-label">Pemilik Kos</span>
                                <div class="owner-card">
                                    <div class="owner-avatar">
                                        @php
                                            $ownerPhoto = ($kosan->pemilik?->foto_profil)
                                                ? asset('storage/' . $kosan->pemilik?->foto_profil)
                                                : asset('images/user-avatar.png');
                                        @endphp
                                        <img src="{{ $ownerPhoto }}" alt="{{ $kosan->pemilik?->name }}">
                                    </div>
                                    <div class="owner-info">
                                        <div class="owner-name">{{ $kosan->pemilik?->name }}</div>
                                        <div class="owner-status">
                                            <i class="fas fa-circle"></i> Online
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Kosans -->
        @if ($kosanSerupa->isNotEmpty())
            <div class="similar-kosans mt-4 mb-5">
                <h3 class="section-title mb-3">Kosan Serupa</h3>
                <div class="row">
                    @foreach ($kosanSerupa as $similarKosan)
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="kosan-card" style="background-color: #e6e9ee;">
                                <div class="kosan-image-wrapper">
                                    @if ($similarKosan->foto_kosan)
                                        <img src="{{ asset('storage/' . $similarKosan->foto_kosan) }}"
                                            alt="{{ $similarKosan->nama_kosan }}" class="kosan-image">
                                    @else
                                        <img src="{{ asset('images/no-image.jpg') }}"
                                            alt="{{ $similarKosan->nama_kosan }}" class="kosan-image">
                                    @endif

                                    @if ($similarKosan->persentase_diskon > 0)
                                        <div class="kosan-badge diskon">Diskon {{ $similarKosan->persentase_diskon }}%
                                        </div>
                                    @elseif($similarKosan->kos_unggulan)
                                        <div class="kosan-badge unggulan">Unggulan</div>
                                    @elseif($similarKosan->created_at->diffInDays(now()) < 7)
                                        <div class="kosan-badge baru">Baru</div>
                                    @endif

                                    <button
                                        class="wishlist-btn {{ $similarKosan->difavoritkanOleh(Auth::id()) ? 'active' : '' }}"
                                        data-kosan-id="{{ $similarKosan->kosan_id }}"
                                        data-favorit-url="{{ route('users.kosan.toggle-favorite', $similarKosan->kosan_id) }}">
                                        <i
                                            class="fa{{ $similarKosan->difavoritkanOleh(Auth::id()) ? 's' : 'r' }} fa-heart"></i>
                                    </button>
                                </div>
                                <div class="kosan-content">
                                    <div class="kosan-rating">
                                        <i class="fas fa-star"></i>
                                        <span>{{ number_format($similarKosan->rating_rata ?? 0, 1) }}</span>
                                        <span
                                            class="rating-count">({{ ($similarKosan->ulasanReview ?? collect())->count() }})</span>
                                    </div>
                                    <h5 class="kosan-title">{{ $similarKosan->nama_kosan }}</h5>
                                    <p class="kosan-location">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $similarKosan->kecamatan }}, {{ $similarKosan->kota }}
                                    </p>
                                    <div class="kosan-type">
                                        <span
                                            class="badge {{ $similarKosan->tipe_kosan == 'putra' ? 'bg-primary' : ($similarKosan->tipe_kosan == 'putri' ? 'bg-danger' : 'bg-success') }}">
                                            Kos {{ ucfirst($similarKosan->tipe_kosan) }}
                                        </span>
                                        @if ($similarKosan?->kamar_tersedia > 0)
                                            <span class="badge bg-info">{{ $similarKosan?->kamar_tersedia }} kamar tersedia</span>
                                        @else
                                            <span class="badge bg-danger">Kamar penuh</span>
                                        @endif
                                    </div>
                                    <div class="kosan-facilities mt-2">
                                        @php
                                            $fasNames = ($similarKosan->kamars ?? collect())->flatMap(function($k) {
                                                return $k->fasilitas ?? collect();
                                            })->pluck('nama_fasilitas')->unique()->all();
                                            $hasWifi = in_array('WiFi', $fasNames) || in_array('Wifi', $fasNames);
                                            $hasAC = in_array('AC', $fasNames);
                                            $hasKM = in_array('Kamar Mandi Dalam', $fasNames);
                                            $hasParkir = in_array('Parkir', $fasNames);
                                        @endphp

                                        @if ($hasWifi)
                                            <span data-bs-toggle="tooltip" title="WiFi"><i
                                                    class="fas fa-wifi"></i></span>
                                        @endif

                                        @if ($hasAC)
                                            <span data-bs-toggle="tooltip" title="AC"><i
                                                    class="fas fa-snowflake"></i></span>
                                        @endif

                                        @if ($hasKM)
                                            <span data-bs-toggle="tooltip" title="Kamar Mandi Dalam"><i
                                                    class="fas fa-bath"></i></span>
                                        @endif

                                        @if ($hasParkir)
                                            <span data-bs-toggle="tooltip" title="Parkir"><i
                                                    class="fas fa-parking"></i></span>
                                        @endif
                                    </div>
                                    <div class="kosan-price mt-2">
                                        @if ($similarKosan->persentase_diskon > 0)
                                            <span class="price-old">Rp
                                                {{ number_format($similarKosan->harga_bulanan, 0, ',', '.') }}</span>
                                            <span class="price">Rp
                                                {{ number_format($similarKosan->getHargaSetelahDiskonAttribute(), 0, ',', '.') }}</span>
                                        @else
                                            <span class="price">Rp
                                                {{ number_format($similarKosan->harga_bulanan, 0, ',', '.') }}</span>
                                        @endif
                                        <span class="period">/ bulan</span>
                                    </div>
                                </div>
                                <div class="kosan-action">
                                    <a href="{{ route('users.kosan.show', $similarKosan->kosan_id) }}"
                                        class="btn btn-sm btn-primary w-100">Detail</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    <!-- Modal: Rating Requirement -->
    <div class="modal fade modal-rating-requirement" id="ratingRequirementModal" tabindex="-1"
        aria-labelledby="ratingRequirementLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ratingRequirementLabel">Syarat Memberi Ulasan & Rating</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-info-circle requirement-icon me-3"></i>
                        <div>
                            <p class="mb-2">Anda harus menyewa kosan ini untuk memberikan ulasan dan rating.</p>
                            <small class="text-muted">Silakan pilih kamar dan lakukan booking terlebih dahulu.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-view-rooms"
                        onclick="document.getElementById('roomPicker')?.scrollIntoView({ behavior: 'smooth', block: 'start' })">
                        <i class="fas fa-door-open me-1"></i> Lihat Kamar
                    </button>
                    <a href="{{ route('users.kosan.booking-form', $kosan->kosan_id) }}" class="btn btn-booking-now">
                        <i class="fas fa-calendar-check me-1"></i> Booking Sekarang
                    </a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        :root {
            --primary: #4f6f52;
            --primary-dark: #3a4d39;
            --primary-light: #a4c3a2;
            --secondary: #eef5e4;
            --accent: #f0a04b;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 0;
        }

        /* Gallery Styles */
        .kosan-gallery {
            position: relative;
        }

        .main-image-container {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .main-image {
            width: 100%;
            height: 350px; /* Diperpendek dari 400px/500px */
            object-fit: cover;
            display: block;
        }

        .thumbnails-container {
            height: 400px;
            overflow-y: auto;
            padding: 10px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .thumbnail {
            width: 100%;
            height: 90px;
            cursor: pointer;
            border-radius: 8px;
            overflow: hidden;
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        .thumbnail.active {
            opacity: 1;
            border: 2px solid var(--primary);
        }

        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Kosan Info Styles */
        .kosan-detail-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .kosan-detail-address {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 16px;
        }

        @media (max-width: 768px) {
            .kosan-detail-title {
                font-size: 1.35rem;
            }
            .kosan-detail-address {
                font-size: 0.85rem;
            }
            .card-body.p-4 {
                padding: 1.25rem !important;
            }
        }

        .kosan-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .rating-stars {
            color: #ffc107;
        }

        .rating-stars .far {
            color: #e1e1e1;
        }

        /* Price Styles */
        .price-info {
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .price-title {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .price-old {
            text-decoration: line-through;
            color: #6c757d;
            font-size: 18px;
            margin-right: 10px;
        }

        .price-current {
            font-weight: 700;
            color: var(--primary);
            font-size: 22px;
        }

        .price-period {
            font-size: 14px;
            color: #6c757d;
        }

        .price-option-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            height: 100%;
        }

        .price-option-title {
            font-size: 14px;
            color: #6c757d;
        }

        .price-option-value {
            font-weight: 700;
            color: var(--primary);
            font-size: 16px;
        }

        /* Facilities Styles */
        .info-icon-wrapper {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            backdrop-filter: blur(10px);
        }

        .selected-facilities-wrapper {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .facility-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 2px solid #f0f0f0;
            height: 100%;
        }

        .facility-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(79, 111, 82, 0.15);
            border-color: var(--primary-light);
        }

        .facility-card-inner {
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .facility-icon-wrapper {
            width: 48px;
            height: 48px;
            min-width: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(79, 111, 82, 0.2);
            position: relative;
            overflow: hidden;
        }

        .facility-icon-wrapper::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.3) 50%, transparent 70%);
            top: -50%;
            left: -50%;
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {

            0%,
            100% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
            }

            50% {
                transform: translateX(100%) translateY(100%) rotate(45deg);
            }
        }

        .facility-icon-img {
            width: 28px;
            height: 28px;
            object-fit: contain;
            filter: brightness(0) invert(1);
            position: relative;
            z-index: 1;
        }

        .facility-icon-font {
            font-size: 20px;
            color: white;
            position: relative;
            z-index: 1;
        }

        .facility-icon-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
        }

        .facility-info {
            flex: 1;
            min-width: 0;
        }

        .facility-name {
            font-size: 15px;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .facility-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            background: rgba(79, 111, 82, 0.1);
            color: var(--primary);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .facility-badge i {
            font-size: 10px;
        }

        .empty-facilities-message {
            text-align: center;
            padding: 60px 20px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 2px dashed #dee2e6;
        }

        .empty-facilities-message i {
            opacity: 0.5;
        }

        .facilities-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .facility-item {
            display: flex;
            align-items: center;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
        }

        .facility-item.available {
            background-color: var(--secondary);
            color: var(--primary-dark);
        }

        .facility-item.unavailable {
            background-color: #f8f9fa;
            color: #adb5bd;
            text-decoration: line-through;
        }

        /* Responsive adjustments for facilities */
        @media (max-width: 576px) {
            .facility-card-inner {
                padding: 15px;
                gap: 10px;
            }

            .facility-icon-wrapper {
                width: 42px;
                height: 42px;
                min-width: 42px;
            }

            .facility-icon-img {
                width: 24px;
                height: 24px;
            }

            .facility-icon-font {
                font-size: 18px;
            }

            .facility-name {
                font-size: 13px;
            }

            .facility-badge {
                font-size: 11px;
                padding: 3px 10px;
            }
        }

        /* Description Styles */
        .description-content {
            color: #495057;
            line-height: 1.6;
            white-space: pre-line;
        }

        /* Location Styles */
        .location-info {
            color: #495057;
        }

        /* Booking Date Input Style */
        .booking-date .input-group-text {
            border-radius: 10px 0 0 10px;
            padding-left: 15px;
        }
        .booking-date .form-control {
            border-radius: 0 10px 10px 0;
            height: 45px;
        }
        .booking-date .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }
        .booking-date .input-group:focus-within {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            border-radius: 10px;
        }
        .average-rating {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .rating-number {
            font-size: 42px; /* Diperkecil dari 48px */
            font-weight: 700;
            line-height: 1;
            color: var(--primary-dark);
            margin-bottom: 4px;
        }

        @media (max-width: 767.98px) {
            .rating-number {
                font-size: 32px; /* Diperkecil lagi untuk mobile */
            }
            .kosan-title-container h1 {
                font-size: 1.5rem !important;
                line-height: 1.3;
            }
            .basic-info-item {
                font-size: 0.85rem;
            }
        }

        .rating-count {
            color: #6c757d;
            font-size: 14px;
        }

        .rating-bars {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .rating-bar-item {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .rating-label {
            width: 55px;
            font-size: 14px;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
        }

        .rating-label i {
            color: #ffc107;
            font-size: 12px;
        }

        .rating-progress-container {
            flex-grow: 1;
            margin: 0 12px;
        }

        .progress {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            background-color: var(--primary);
            border-radius: 10px;
        }

        .rating-bar-count {
            width: 30px;
            text-align: right;
            font-size: 13px;
            color: #6c757d;
            font-weight: 500;
            flex-shrink: 0;
        }

        @media (max-width: 576px) {
            .rating-label {
                width: 45px;
                font-size: 12px;
            }
            .rating-progress-container {
                margin: 0 8px;
            }
            .rating-bar-count {
                width: 25px;
                font-size: 11px;
            }
        }

        .review-item {
            background-color: #fcfcfc;
            /* Light background */
            border: 1px solid #e0e0e0;
            /* Subtle border */
            border-radius: 8px;
            /* Rounded corners */
            padding: 15px;
            /* Adjust padding */
            margin-bottom: 15px;
            /* Spacing between reviews */
        }

        .review-item:last-child {
            /* Remove margin for the last item */
            margin-bottom: 0;
        }

        .reviewer-avatar {
            flex-shrink: 0;
            /* Prevent avatar from shrinking */
        }

        .reviewer-avatar img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .reviewer-name {
            font-weight: 600;
            font-size: 16px;
        }

        .review-content {
            color: #495057;
            line-height: 1.6;
        }

        /* Adjust review-header for mobile stacking */
        .review-header.flex-wrap {
            margin-bottom: 10px;
            /* Space between header and content when wrapped */
        }

        .review-header.flex-wrap .d-flex.align-items-center {
            margin-bottom: 10px;
            /* Space for reviewer info when it stacks above rating */
        }

        @media (min-width: 768px) {
            .review-header.flex-wrap .d-flex.align-items-center {
                margin-bottom: 0;
                /* No extra margin on larger screens */
            }
        }

        .kosan-rating-info {
            cursor: pointer;
        }

        /* Booking Card Styles */
        .booking-card {
            border: 1px solid #eee;
        }

        .booking-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-dark);
        }

        .availability-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 500;
        }

        .availability-badge.available {
            background-color: var(--secondary);
            color: var(--primary-dark);
        }

        .availability-badge.unavailable {
            background-color: #f8d7da;
            color: #842029;
        }

        .booking-price {
            margin-bottom: 15px;
        }

        .price-label {
            font-size: 14px;
            color: #6c757d;
        }

        .price-value {
            font-weight: 700;
            font-size: 18px;
        }

        .total-price {
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 15px;
        }

        .booking-contact {
            border-top: 2px solid #e9ecef;
            padding-top: 20px;
            margin-top: 20px;
        }

        .modal-rating-requirement .modal-header {
            border-bottom: none;
        }

        .modal-rating-requirement .modal-footer {
            border-top: none;
        }

        .modal-rating-requirement .requirement-icon {
            font-size: 32px;
            color: var(--primary);
        }

        @media (max-width: 575.98px) {
            .modal-rating-requirement .modal-body {
                padding: 1rem;
            }
        }

        .btn-booking-now {
            background: var(--primary);
            color: #fff;
        }

        .btn-booking-now:hover {
            background: var(--primary-dark);
            color: #fff;
        }

        .btn-view-rooms {
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-view-rooms:hover {
            background: var(--secondary);
            color: var(--primary-dark);
        }

        .contact-label {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 12px;
            display: block;
        }

        .owner-card {
            display: flex;
            align-items: center;
            background-color: #f8f9fa;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            text-decoration: none !important;
        }

        .owner-card:hover {
            border-color: var(--primary-light);
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .owner-avatar img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .owner-info {
            flex: 1;
            margin-left: 12px;
            min-width: 0;
        }

        .owner-name {
            font-weight: 600;
            font-size: 14px;
            color: var(--primary-dark);
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .owner-status {
            font-size: 12px;
            display: flex;
            align-items: center;
            color: #198754;
        }

        .owner-status i {
            font-size: 8px;
            margin-right: 5px;
        }

        .contact-action-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .contact-action-btn:hover {
            background-color: var(--primary-dark);
            color: white;
            transform: scale(1.1);
        }

        @media (max-width: 576px) {
            .owner-card {
                padding: 10px;
            }
            .owner-avatar img {
                width: 40px;
                height: 40px;
            }
            .owner-name {
                font-size: 13px;
            }
        }

        /* Similar Kosans Styles */
        .kosan-card {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .kosan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .kosan-image-wrapper {
            position: relative;
            height: 180px;
        }

        .kosan-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .kosan-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            z-index: 1;
        }

        .kosan-badge.unggulan {
            background-color: var(--primary);
            color: white;
        }

        .kosan-badge.diskon {
            background-color: #e74c3c;
            color: white;
        }

        .kosan-badge.baru {
            background-color: var(--accent);
            color: white;
        }

        .wishlist-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1;
            transition: transform 0.2s;
        }

        .wishlist-btn i {
            color: #ccc;
            font-size: 18px;
            transition: color 0.2s;
        }

        .wishlist-btn.active i {
            color: #e74c3c;
        }

        .wishlist-btn:hover {
            transform: scale(1.1);
        }

        .kosan-content {
            padding: 15px;
        }

        .kosan-rating {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .kosan-rating i {
            color: #ffc107;
            margin-right: 5px;
        }

        .rating-count {
            font-size: 12px;
            color: #6c757d;
            margin-left: 3px;
        }

        .kosan-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--dark);
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .kosan-location {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .kosan-type {
            margin-bottom: 10px;
        }

        .kosan-facilities {
            display: flex;
            gap: 12px;
        }

        .kosan-facilities span {
            color: #6c757d;
            font-size: 14px;
        }

        .kosan-price {
            margin: 12px 0;
            display: flex;
            align-items: baseline;
            flex-wrap: wrap;
        }

        .kosan-price .price {
            font-weight: 700;
            color: var(--primary);
            font-size: 16px;
        }

        .kosan-price .price-old {
            font-size: 14px;
            text-decoration: line-through;
            color: #6c757d;
            margin-right: 8px;
        }

        .kosan-price .period {
            font-size: 13px;
            color: #6c757d;
            margin-left: 4px;
        }

        .kosan-action {
            padding: 0 15px 15px;
        }



        /* Responsive Adjustments */
        @media (max-width: 767.98px) {
            .main-image {
                height: 220px; /* Diperpendek dari 250px */
            }

            .thumbnails-container {
                height: 120px;
                flex-direction: row;
                overflow-x: auto;
                overflow-y: hidden;
            }

            .thumbnail {
                width: 100px;
                min-width: 100px;
                height: 100%;
            }
        }

        /* Perbaikan untuk Tombol Bagikan dan Favorit */
        .share-button {
            transition: all 0.3s ease;
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 110px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .share-button:hover {
            background-color: var(--primary-light);
            border-color: var(--primary);
            color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .share-button i {
            margin-right: 6px;
            font-size: 16px;
        }

        /* Override untuk tombol favorit di detail kosan */
        .kosan-info .wishlist-btn, .kosan-info .share-button {
            position: static;
            width: auto;
            height: 40px !important;
            border-radius: 50px !important;
            font-weight: 600;
            font-size: 0.85rem !important;
            padding: 0 20px !important;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            background-color: white; /* Default background */
            color: #495057;
        }

        /* Pastikan btn-danger tetap merah */
        .kosan-info .wishlist-btn.btn-danger {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: white !important;
        }
        
        .kosan-info .wishlist-btn.btn-danger i {
            color: white !important;
        }

        .kosan-info .wishlist-btn.btn-danger:hover {
            background-color: #bb2d3b !important;
            border-color: #b02a37 !important;
        }

        @media (max-width: 576px) {
            .kosan-info .wishlist-btn, .kosan-info .share-button {
                font-size: 0.8rem !important;
                padding: 0 12px !important;
                height: 36px !important;
            }
        }

        /* Animasi tambahan */
        .kosan-info .wishlist-btn i {
            transition: transform 0.3s;
        }

        .kosan-info .wishlist-btn:hover i {
            transform: scale(1.2);
        }

        .room-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 8px;
            background-color: #fff;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .room-card.disabled {
            opacity: 0.7;
            cursor: not-allowed;
            background-color: #f8f9fa;
        }

        .room-card-active {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 0.2rem rgba(79, 111, 82, 0.15);
            background-color: var(--secondary) !important;
            transform: translateY(-2px);
        }

        .room-card-image {
            width: 100%;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }

        .room-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .room-badge-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .room-badge-overlay .badge {
            font-size: 10px;
            padding: 4px 8px;
        }

        .room-card-info {
            flex: 1;
        }

        .room-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 2px;
            line-height: 1.2;
        }

        .room-price-small {
            font-size: 11px;
            color: #6c757d;
        }

        .room-card-active .room-name {
            color: var(--primary-dark);
        }

        @media (max-width: 576px) {
            .room-card {
                padding: 6px;
            }
            .room-card-image {
                height: 60px;
            }
            .room-name {
                font-size: 12px;
            }
            .room-price-small {
                font-size: 10px;
            }
        }
        #bookingSubmitBtn:disabled {
            cursor: not-allowed;
            pointer-events: none;
            opacity: 0.6;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Data Fasilitas Kamar Global
        window.kamarFacilities = {
            @foreach ($kosan->kamars as $room)
                @php
                    $kamarId = $room->kamar_id;
                    $fasilitasKamar = $room->fasilitas;
                @endphp
                "{{ $kamarId }}": [
                    @foreach ($fasilitasKamar as $fas)
                        {
                            "id": {{ $fas->fasilitas_id }},
                            "nama": "{{ $fas->nama_fasilitas }}",
                            "icon": "{{ $fas->icon_fasilitas ?? 'fas fa-check-circle' }}"
                        }{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ]{{ !$loop->last ? ',' : '' }}
            @endforeach
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Get the main container and read the kosanId from its data attribute.
            const container = document.getElementById('kosan-detail-container');
            const kosanId = container.dataset.kosanId;

            // Initialize gallery
            initializeGallery();

            // Initialize map if coordinates exist
            // Note: Map initialization still uses Blade syntax, but it's in a conditional block
            // that the linter seems to handle correctly. We will leave it for now.
            initializeMap();

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Handle booking calculation
            const durationSelect = document.getElementById('bookingDuration');
            const jumlahKamarSelect = document.getElementById('jumlahKamar');
            const totalPriceElement = document.getElementById('totalPrice');
            const priceDetailsElement = document.getElementById('priceDetails');
            const inputDurasi = document.getElementById('inputDurasi');
            const inputTanggalMulai = document.getElementById('inputTanggalMulai');
            const inputJumlahKamar = document.getElementById('inputJumlahKamar');
            const inputKamarId = document.getElementById('inputKamarId');
            const roomButtons = document.querySelectorAll('.room-card');
            let selectedKamarId = null;

            // Handle booking date input
            const bookingDateInput = document.getElementById('bookingDate');
            if (bookingDateInput && inputTanggalMulai) {
                const today = new Date();
                const formattedDate = [
                    today.getFullYear(),
                    String(today.getMonth() + 1).padStart(2, '0'),
                    String(today.getDate()).padStart(2, '0')
                ].join('-');
                bookingDateInput.value = formattedDate;
                inputTanggalMulai.value = formattedDate;
                bookingDateInput.addEventListener('change', function() {
                    inputTanggalMulai.value = this.value;
                });
            }

            // Handle wishlist buttons
            const wishlistButtons = document.querySelectorAll('.wishlist-btn');
            wishlistButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const clickedKosanId = this.getAttribute('data-kosan-id');
                    const url = this.getAttribute('data-favorit-url');
                    const icon = this.querySelector('i');

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    }).then(response => response.json()).then(data => {
                        if (data.success) {
                            if (data.action === 'added') {
                                button.classList.add('active', 'btn-danger');
                                button.classList.remove('btn-outline-danger');
                                if (icon) {
                                    icon.classList.replace('far', 'fas');
                                }
                            } else {
                                button.classList.remove('active', 'btn-danger');
                                button.classList.add('btn-outline-danger');
                                if (icon) {
                                    icon.classList.replace('fas', 'far');
                                }
                            }
                        }
                    }).catch(error => console.error('Error:', error));
                });
            });

            // Load more reviews button
            const loadMoreReviewsBtn = document.getElementById('loadMoreReviews');
            if (loadMoreReviewsBtn) {
                loadMoreReviewsBtn.addEventListener('click', function() {
                    loadMoreReviews(kosanId); // Use the JS variable
                });
            }

            const ratingInfo = document.querySelector('.kosan-rating-info');
            if (ratingInfo) {
                const reviewUrl = "{{ route('users.kosan.review-form', $kosan->kosan_id) }}";
                const loginUrl = "{{ route('login') }}";
                const isAuth = "{{ Auth::check() ? 'true' : 'false' }}" === 'true';
                const eligible = {{ $hasActiveBookingForThisKosan ?? false ? 'true' : 'false' }};
                ratingInfo.addEventListener('click', function() {
                    if (!isAuth) {
                        window.location.href = loginUrl;
                        return;
                    }
                    if (eligible) {
                        window.location.href = reviewUrl;
                    } else {
                        const modalEl = document.getElementById('ratingRequirementModal');
                        if (modalEl && window.bootstrap?.Modal) {
                            const modal = new bootstrap.Modal(modalEl, {
                                backdrop: 'static',
                                keyboard: false
                            });
                            modal.show();
                        } else {
                            alert('Anda harus menyewa kosan ini untuk memberikan ulasan dan rating.');
                        }
                    }
                });
            }

            // Room selection handlers
            let selectedKamarIds = [];

            if (roomButtons && roomButtons.length) {
                roomButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        if (this.classList.contains('disabled')) return;
                        
                        const kamarId = this.getAttribute('data-kamar-id');
                        const targetCount = parseInt(jumlahKamarSelect.value) || 1;

                        if (selectedKamarIds.includes(kamarId)) {
                            // Deselect if already selected
                            selectedKamarIds = selectedKamarIds.filter(id => id !== kamarId);
                            this.classList.remove('room-card-active');
                        } else {
                            // Select if not at limit
                            if (selectedKamarIds.length < targetCount) {
                                selectedKamarIds.push(kamarId);
                                this.classList.add('room-card-active');
                            } else if (targetCount === 1) {
                                // If limit is 1, replace selection
                                roomButtons.forEach(b => b.classList.remove('room-card-active'));
                                selectedKamarIds = [kamarId];
                                this.classList.add('room-card-active');
                            } else {
                                alert(`Anda sudah memilih ${targetCount} kamar. Batalkan pilihan kamar lain jika ingin mengubah.`);
                            }
                        }

                        if (inputKamarId) inputKamarId.value = selectedKamarIds.join(',');
                        
                        const roomImage = this.getAttribute('data-image');
                        if (roomImage && selectedKamarIds.includes(kamarId)) {
                            changeMainImage(roomImage, null);
                        }

                        if (window.recalc) {
                            window.recalc();
                        }
                        toggleSubmitBySelection();
                        
                        if (selectedKamarIds.length > 0) {
                            const lastKamarId = selectedKamarIds[selectedKamarIds.length - 1];
                            const lastBtn = document.querySelector(`.room-card[data-kamar-id="${lastKamarId}"]`);
                            if (window.displayRoomFacilities) {
                                window.displayRoomFacilities(lastKamarId, lastBtn.getAttribute('data-nomor'));
                            }
                        }
                    });
                });
            }

            // Reset selection when jumlah kamar changes
            if (jumlahKamarSelect) {
                jumlahKamarSelect.addEventListener('change', function() {
                    selectedKamarIds = [];
                    roomButtons.forEach(b => b.classList.remove('room-card-active'));
                    if (inputKamarId) inputKamarId.value = '';
                    toggleSubmitBySelection();
                    if (window.recalc) window.recalc();
                });
            }

            function toggleSubmitBySelection() {
                const submitBtn = document.getElementById('bookingSubmitBtn');
                const hasActive = {{ $hasActiveBooking ?? false ? 'true' : 'false' }};
                const targetCount = parseInt(jumlahKamarSelect?.value) || 1;
                
                if (!submitBtn) return;
                
                const isCorrectCount = selectedKamarIds.length === targetCount;
                const mustDisable = hasActive || !isCorrectCount;
                
                if (mustDisable) {
                    submitBtn.disabled = true;
                    submitBtn.classList.remove('btn-primary');
                    submitBtn.classList.add('btn-secondary');
                    
                    if (!hasActive && !isCorrectCount) {
                        submitBtn.innerText = `Pilih ${targetCount - selectedKamarIds.length} Kamar Lagi`;
                    } else if (hasActive) {
                        submitBtn.innerText = `Ada Booking Aktif`;
                    }
                } else {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('btn-secondary');
                    submitBtn.classList.add('btn-primary');
                    submitBtn.innerText = `Booking Sekarang`;
                }
            }
        });

        // Other functions remain outside DOMContentLoaded but can use kosanId if passed as an argument

        function initializeGallery() {
            // ... (no changes needed here)
        }

        function changeMainImage(imgSrc, thumbnailElement) {
            // ... (no changes needed here)
        }

        function initializeMap() {
            @if ($kosan?->latitude && $kosan?->longitude)
                const mapElement = document.getElementById('map');
                if (!mapElement) return;

                const kosanLocation = [{{ $kosan->latitude }}, {{ $kosan->longitude }}];
                const map = L.map('map').setView(kosanLocation, 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                const marker = L.marker(kosanLocation).addTo(map);
                marker.bindPopup("{{ $kosan?->nama_kosan }}").openPopup();
            @endif
        }

        function shareKosan() {
            if (navigator.share) {
                navigator.share({
                        title: "{{ $kosan?->nama_kosan }}",
                        text: "Lihat kosan {{ $kosan?->nama_kosan }} di HumbleKos",
                        url: window.location.href
                    })
                    .catch(error => console.log('Error sharing:', error));
            } else {
                const tempInput = document.createElement('input');
                document.body.appendChild(tempInput);
                tempInput.value = window.location.href;
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                alert('URL telah disalin ke clipboard!');
            }
        }

        function loadMoreReviews(kosanId) {
            if (!kosanId) return;
            const reviewList = document.querySelector('.review-list');
            const loadMoreBtn = document.getElementById('loadMoreReviews');
            const currentReviewCount = document.querySelectorAll('.review-item').length;

            loadMoreBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
            loadMoreBtn.disabled = true;

            setTimeout(() => {
                loadMoreBtn.innerHTML = 'Lihat Lebih Banyak';
                loadMoreBtn.disabled = false;
                alert('Implementasi load more reviews memerlukan endpoint API tambahan. Kosan ID: ' + kosanId);
            }, 1000);
        }

        function formatNumber(num) {
            return parseFloat(num).toLocaleString('id-ID');
        }
    </script>
@endpush

@push('scripts')
    <script>
        // Function to change main image when clicking thumbnail
        function changeMainImage(imageSrc, clickedThumbnail) {
            const mainImage = document.getElementById('mainImage');
            if (mainImage) {
                // Add a simple fade effect
                mainImage.style.opacity = '0';
                setTimeout(() => {
                    mainImage.src = imageSrc;
                    mainImage.style.opacity = '1';
                }, 300);
            }

            // Reset all thumbnails first
            const thumbnails = document.querySelectorAll('.thumbnail-image');
            thumbnails.forEach(thumb => {
                thumb.style.border = '2px solid #ddd';
                thumb.style.transform = 'scale(1)';
            });

            // Highlight the clicked one
            if (clickedThumbnail) {
                clickedThumbnail.style.border = '2px solid #007bff';
                clickedThumbnail.style.transform = 'scale(1.05)';
            }
        }

        // Add hover effect to thumbnails
        document.addEventListener('DOMContentLoaded', function() {
            const thumbnails = document.querySelectorAll('.thumbnail-image');
            thumbnails.forEach(thumb => {
                thumb.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.1)';
                    this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.3)';
                });
                thumb.addEventListener('mouseleave', function() {
                    const isActive = this.style.border.includes('3px');
                    this.style.transform = isActive ? 'scale(1.05)' : 'scale(1)';
                    this.style.boxShadow = 'none';
                });
            });
        });

        function __initBookingCalc() {
            const container = document.getElementById('kosan-detail-container');
            const durSel = document.getElementById('bookingDuration');
            const bulanDiv = document.getElementById('input_bulanan_custom_show');
            const tahunDiv = document.getElementById('input_tahunan_custom_show');
            const bulanInput = document.getElementById('bulan_custom_show');
            const tahunInput = document.getElementById('tahun_custom_show');
            const jumlahKamarSel = document.getElementById('jumlahKamar');
            const totalPrice = document.getElementById('totalPrice');
            const priceDetails = document.getElementById('priceDetails');
            const inputDurasi = document.getElementById('inputDurasi');
            const inputNilaiDurasi = document.getElementById('inputNilaiDurasi');
            const inputJumlahKamar = document.getElementById('inputJumlahKamar');
            const inputTanggalMulai = document.getElementById('inputTanggalMulai');

            // Read data from data-* attributes
            const monthly = parseFloat(container.dataset.monthlyPrice) || 0;
            const yearly = parseFloat(container.dataset.yearlyPrice) || 0;
            const kosanId = container.dataset.kosanId;
            const availabilityBase = container.dataset.availabilityUrl;

            const kamarPrices = {
                @foreach (($kosan?->kamars ?? []) as $index => $room)
                    @php
                        $kamarId = $room && method_exists($room, '__get') ? ($room->kamar_id ?? null) : null;
                        $harga = $room && method_exists($room, '__get') ? ($room->harga_setelah_diskon ?? 0) : 0;
                    @endphp
                    @if ($kamarId)
                        "{{ $kamarId }}": {{ (float) $harga }}{{ !$loop->last ? ',' : '' }}
                    @endif
                @endforeach
            };

            function toggleCustom() {
                if (durSel.value === 'bulanan') {
                    bulanDiv.style.display = 'block';
                    tahunDiv.style.display = 'none';
                } else if (durSel.value === 'tahunan') {
                    bulanDiv.style.display = 'none';
                    tahunDiv.style.display = 'block';
                } else {
                    bulanDiv.style.display = 'none';
                    tahunDiv.style.display = 'none';
                }
            }

            function displayRoomFacilities(kamarId, nomorKamar) {
                const facilitiesSection = document.getElementById('selectedRoomFacilities');
                const roomNumberSpan = document.getElementById('selectedRoomNumber');
                const facilitiesList = document.getElementById('facilitiesList');
                const facilitiesCount = document.getElementById('facilitiesCount');

                // Get facilities data
                const facilities = window.kamarFacilities?.[kamarId] || [];

                if (facilities.length > 0) {
                    facilitiesSection.style.display = 'block';
                    roomNumberSpan.textContent = nomorKamar;
                    facilitiesCount.innerHTML =
                        `<i class="fas fa-check-circle me-1"></i>${facilities.length} Fasilitas`;

                    let facilitiesHTML = '';
                    facilities.forEach((fas, index) => {
                        const isImageIcon = fas.icon && (fas.icon.includes('/') || fas.icon.includes('.'));

                        facilitiesHTML += `
                            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                                <div class="facility-card">
                                    <div class="facility-card-inner">
                                        <div class="facility-icon-wrapper">
                                            ${isImageIcon
                                                ? `<img src="${fas.icon.startsWith('http') ? fas.icon : '/storage/' + fas.icon}" alt="${fas.nama}" class="facility-icon-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                       <div class="facility-icon-fallback" style="display:none;"><i class="fas fa-check-circle"></i></div>`
                                                : `<i class="${fas.icon || 'fas fa-check-circle'} facility-icon-font"></i>`
                                            }
                                        </div>
                                        <div class="facility-info">
                                            <h6 class="facility-name">${fas.nama}</h6>
                                            <div class="facility-badge">
                                                <i class="fas fa-check me-1"></i>Tersedia
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    facilitiesList.innerHTML = facilitiesHTML;
                } else {
                    facilitiesSection.style.display = 'block';
                    roomNumberSpan.textContent = nomorKamar;
                    facilitiesCount.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i>0 Fasilitas`;
                    facilitiesList.innerHTML = `
                        <div class="col-12">
                            <div class="empty-facilities-message">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">Tidak ada fasilitas untuk kamar ini</h6>
                                <p class="text-muted small mb-0">Silakan pilih kamar lain untuk melihat fasilitas yang tersedia.</p>
                            </div>
                        </div>
                    `;
                }
            }

            window.displayRoomFacilities = displayRoomFacilities;

            function recalc() {
                let roomsCount = 1;
                if (jumlahKamarSel && jumlahKamarSel.value) {
                    roomsCount = parseInt(jumlahKamarSel.value);
                }

                const selectedIds = (document.getElementById('inputKamarId')?.value || '').split(',').filter(id => id);
                
                // HANYA hitung total harga dari kamar yang sudah benar-benar diklik
                let totalBasePrice = 0;
                if (selectedIds.length > 0) {
                    selectedIds.forEach(id => {
                        totalBasePrice += (kamarPrices[id] || monthly);
                    });
                } else {
                    // Jika belum ada yang dipilih, tampilkan 0 atau harga default 1 kamar sebagai info awal
                    totalBasePrice = 0; 
                }

                let durationType = durSel.value;
                let durationValueInMonths = parseInt(durSel.selectedOptions[0].getAttribute('data-value'));
                const selectedOption = durSel.selectedOptions[0];
                let displayDurationText = durSel.selectedOptions[0].text.replace(' (custom)', '');

                if (durationType === 'bulanan') {
                    const m = parseInt(bulanInput.value);
                    durationValueInMonths = (!isNaN(m) && m >= 1 && m <= 11) ? m : 1;
                    displayDurationText = `${durationValueInMonths} Bulan`;
                } else if (durationType === 'tahunan') {
                    const y = parseInt(tahunInput.value);
                    durationValueInMonths = (!isNaN(y) && y >= 1) ? y * 12 : 12;
                    displayDurationText = `${durationValueInMonths / 12} Tahun`;
                }

                let finalTotal = 0;
                // Jika totalBasePrice 0, maka finalTotal juga 0
                if (totalBasePrice > 0) {
                    if (durationType === 'bulanan' || durationType === 'tahunan') {
                        finalTotal = totalBasePrice * durationValueInMonths;
                    } else {
                        const optionPrice = parseFloat(selectedOption.getAttribute('data-price') || '0');
                        if (optionPrice > 0) {
                            const factor = optionPrice / monthly;
                            finalTotal = totalBasePrice * factor;
                        } else {
                            finalTotal = totalBasePrice * durationValueInMonths;
                        }
                    }
                }

                if (inputJumlahKamar) inputJumlahKamar.value = roomsCount;
                if (inputDurasi) inputDurasi.value = durationType;
                if (inputNilaiDurasi) inputNilaiDurasi.value = (durationType === 'tahunan') ? (durationValueInMonths / 12) : durationValueInMonths;

                totalPrice.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(finalTotal);
                
                const perRoomPriceEl = document.getElementById('perRoomPrice');
                if (perRoomPriceEl) {
                    if (selectedIds.length > 0) {
                        // Ambil daftar harga dari kamar-kamar yang dipilih
                        const prices = selectedIds.map(id => kamarPrices[id] || monthly);
                        const minPrice = Math.min(...prices);
                        const maxPrice = Math.max(...prices);

                        if (minPrice === maxPrice) {
                            // Jika semua harga sama
                            perRoomPriceEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(minPrice);
                        } else {
                            // Jika ada perbedaan harga
                            perRoomPriceEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(minPrice) + ' - Rp ' + new Intl.NumberFormat('id-ID').format(maxPrice);
                        }
                    } else {
                        perRoomPriceEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(monthly);
                    }
                }

                // Update teks detail agar lebih informatif
                if (selectedIds.length < roomsCount) {
                    priceDetails.innerHTML = `<span class="text-danger fw-bold">Pilih ${roomsCount - selectedIds.length} kamar lagi</span>`;
                } else {
                    priceDetails.textContent = `Total untuk ${roomsCount} Kamar selama ${displayDurationText}`;
                }

                checkAvailability();
            }

            window.recalc = recalc;

            if (durSel) durSel.addEventListener('change', function() {
                toggleCustom();
                recalc();
            });
            if (bulanInput) {
                bulanInput.addEventListener('input', recalc);
                bulanInput.addEventListener('change', recalc);
            }
            if (tahunInput) {
                tahunInput.addEventListener('input', recalc);
                tahunInput.addEventListener('change', recalc);
            }
            if (jumlahKamarSel) {
                jumlahKamarSel.addEventListener('change', recalc);
                jumlahKamarSel.addEventListener('input', recalc);
            }
            const startField = document.getElementById('bookingDate');
            if (startField) startField.addEventListener('change', recalc);

            toggleCustom();
            recalc();

            // Auto-select first available room to show facilities immediately
            const firstAvailableRoom = document.querySelector('.room-card:not(.disabled)');
            if (firstAvailableRoom) {
                setTimeout(() => {
                    firstAvailableRoom.click();
                    // Don't scroll on auto-select to avoid jumping on page load
                }, 500);
            }

            async function checkAvailability() {
                if (!kosanId) return;
                const startEl = document.getElementById('bookingDate');
                const startDate = (startEl && startEl.value) ? startEl.value : '{{ date('Y-m-d') }}';
                const type = durSel.value;
                let value = parseInt(durSel.selectedOptions[0].getAttribute('data-value'));
                if (type === 'bulanan' && bulanInput && bulanInput.value) {
                    value = parseInt(bulanInput.value);
                }
                if (type === 'tahunan' && tahunInput && tahunInput.value) {
                    value = parseInt(tahunInput.value);
                }
                const rooms = (jumlahKamarSel && jumlahKamarSel.value) ? parseInt(jumlahKamarSel.value) : 1;

                try {
                    const url =
                        `${availabilityBase}?start=${encodeURIComponent(startDate)}&duration_type=${encodeURIComponent(type)}&duration_value=${value}`;
                    const res = await fetch(url);

                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }

                    const data = await res.json();

                    if (data.error) {
                        console.error('API Error:', data.message || data.error);
                        updateAvailabilityUI(0, rooms);
                        return;
                    }

                    const availableCount = parseInt(data.available_count || 0);
                    updateAvailabilityUI(availableCount, rooms);
                } catch (e) {
                    console.error('Availability check error:', e);
                    // Use fallback: count available rooms from frontend data
                    const availableRooms = document.querySelectorAll('.room-card:not(.disabled)').length;
                    updateAvailabilityUI(availableRooms, rooms);
                }
            }

            function updateAvailabilityUI(availableCount, requestedRooms) {
                const statusBadge = document.querySelector('.availability-badge');
                const btn = document.getElementById('bookingSubmitBtn');
                const hasActive = {{ $hasActiveBooking ?? false ? 'true' : 'false' }};
                
                const targetCount = parseInt(jumlahKamarSelect?.value) || 1;
                const currentSelectedCount = selectedKamarIds.length;
                const isCountCorrect = currentSelectedCount === targetCount;

                if (availableCount >= targetCount) {
                    if (statusBadge) {
                        statusBadge.classList.remove('unavailable');
                        statusBadge.classList.add('available');
                        statusBadge.innerHTML = `<i class="fas fa-check-circle me-2"></i>${availableCount} kamar tersedia`;
                    }
                    
                    if (hasActive) {
                        if (btn) {
                            btn.disabled = true;
                            btn.innerText = 'Ada Booking Aktif';
                        }
                    } else if (!isCountCorrect) {
                        if (btn) {
                            btn.disabled = true;
                            btn.classList.remove('btn-primary');
                            btn.classList.add('btn-secondary');
                            btn.innerText = `Pilih ${targetCount - currentSelectedCount} Kamar Lagi`;
                        }
                    } else {
                        if (btn) {
                            btn.disabled = false;
                            btn.classList.remove('btn-secondary');
                            btn.classList.add('btn-primary');
                            btn.innerText = 'Booking Sekarang';
                        }
                    }
                } else {
                    if (statusBadge) {
                        statusBadge.classList.remove('available');
                        statusBadge.classList.add('unavailable');
                        statusBadge.innerHTML =
                            `<i class="fas fa-times-circle me-2"></i>Kamar tidak mencukupi`;
                    }
                    if (btn) {
                        btn.disabled = true;
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-secondary');
                        btn.innerText = 'Kamar Tidak Mencukupi';
                    }
                }
            }

            // Check availability on page load and periodically
            setTimeout(checkAvailability, 1000);
            setInterval(checkAvailability, 30000); // Reduced frequency to 30 seconds
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', __initBookingCalc);
        } else {
            __initBookingCalc();
        }
    </script>
@endpush
