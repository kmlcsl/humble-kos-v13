@extends('layouts.user.app')

@section('title', 'Daftar Kosan')

@section('content')
    <!-- Kosan Header Section -->
    <div class="kosan-header mb-4 py-3">
        <div class="container-fluid">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h1 class="kosan-title">Daftar Kosan</h1>
                    <p class="text-muted mb-0">Temukan kosan terbaik sesuai dengan kebutuhan Anda</p>
                </div>
                <div class="kosan-actions d-flex gap-2 justify-content-md-end">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle action-btn-equal" type="button" id="sortDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-sort me-1"></i> Urutkan
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="sortDropdown">
                            <li><a class="dropdown-item {{ request()->sort == 'terbaru' ? 'active' : '' }}"
                                    href="{{ route('users.kosan.index', array_merge(request()->except('sort'), ['sort' => 'terbaru'])) }}">Terbaru</a>
                            </li>
                            <li><a class="dropdown-item {{ request()->sort == 'harga_terendah' ? 'active' : '' }}"
                                    href="{{ route('users.kosan.index', array_merge(request()->except('sort'), ['sort' => 'harga_terendah'])) }}">Harga
                                    Terendah</a></li>
                            <li><a class="dropdown-item {{ request()->sort == 'harga_tertinggi' ? 'active' : '' }}"
                                    href="{{ route('users.kosan.index', array_merge(request()->except('sort'), ['sort' => 'harga_tertinggi'])) }}">Harga
                                    Tertinggi</a></li>
                            <li><a class="dropdown-item {{ request()->sort == 'rating_tertinggi' ? 'active' : '' }}"
                                    href="{{ route('users.kosan.index', array_merge(request()->except('sort'), ['sort' => 'rating_tertinggi'])) }}">Rating
                                    Tertinggi</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-primary action-btn-equal" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Active Tags -->
    @if (request()->has('keyword') ||
            request()->has('kota') ||
            request()->has('tipe_kosan') ||
            request()->has('harga_min') ||
            request()->has('harga_max') ||
            request()->has('kampus') ||
            request()->has('fasilitas'))
        <div class="filter-tags mb-4">
            <div class="container-fluid">
                <div class="d-flex align-items-center">
                    <span class="me-2">Filter aktif:</span>
                    <div class="filter-tag-container">
                        @if (request()->has('keyword') && !empty(request()->keyword))
                            <div class="filter-tag">
                                <span>Kata kunci: {{ request()->keyword }}</span>
                                <a href="{{ route('users.kosan.index', array_merge(request()->except('keyword'), ['keyword' => ''])) }}"
                                    class="filter-tag-remove"><i class="fas fa-times"></i></a>
                            </div>
                        @endif

                        @if (request()->has('kota') && !empty(request()->kota))
                            <div class="filter-tag">
                                <span>Kota: {{ request()->kota }}</span>
                                <a href="{{ route('users.kosan.index', array_merge(request()->except('kota'), ['kota' => ''])) }}"
                                    class="filter-tag-remove"><i class="fas fa-times"></i></a>
                            </div>
                        @endif

                        @if (request()->has('kampus') && !empty(request()->kampus))
                            <div class="filter-tag">
                                <span>Kampus: {{ request()->kampus }}</span>
                                <a href="{{ route('users.kosan.index', array_merge(request()->except('kampus'), ['kampus' => ''])) }}"
                                    class="filter-tag-remove"><i class="fas fa-times"></i></a>
                            </div>
                        @endif

                        @if (request()->has('tipe_kosan') && !empty(request()->tipe_kosan))
                            <div class="filter-tag">
                                <span>Jenis: {{ ucfirst(request()->tipe_kosan) }}</span>
                                <a href="{{ route('users.kosan.index', array_merge(request()->except('tipe_kosan'), ['tipe_kosan' => ''])) }}"
                                    class="filter-tag-remove"><i class="fas fa-times"></i></a>
                            </div>
                        @endif

                        @if (
                            (request()->has('harga_min') && !empty(request()->harga_min)) ||
                                (request()->has('harga_max') && !empty(request()->harga_max)))
                            <div class="filter-tag">
                                <span>Harga: {{ request()->harga_min ? number_format(request()->harga_min, 0, ',', '.') : '0' }} -
                                    {{ request()->harga_max ? number_format(request()->harga_max, 0, ',', '.') : '~' }}</span>
                                <a href="{{ route('users.kosan.index', array_merge(request()->except(['harga_min', 'harga_max']))) }}"
                                    class="filter-tag-remove"><i class="fas fa-times"></i></a>
                            </div>
                        @endif

                        @if (request()->has('fasilitas') && is_array(request()->fasilitas))
                            <div class="filter-tag">
                                <span>Fasilitas: {{ implode(', ', request()->fasilitas) }}</span>
                                <a href="{{ route('users.kosan.index', array_merge(request()->except(['fasilitas']))) }}"
                                    class="filter-tag-remove"><i class="fas fa-times"></i></a>
                            </div>
                        @endif

                        <a href="{{ route('users.kosan.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">Reset
                            Filter</a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Kosan List -->
    <div class="container-fluid">
        <div class="row">
            @forelse($kosans as $kosan)
                <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="kosan-card">
                        <div class="kosan-image-wrapper">
                            @if ($kosan->fotoUtama)
                                <img src="{{ asset('storage/' . $kosan->fotoUtama->path_gambar) }}"
                                    alt="{{ $kosan->nama_kosan }}" class="kosan-image">
                            @else
                                <img src="{{ asset('images/no-image.jpg') }}" alt="{{ $kosan->nama_kosan }}"
                                    class="kosan-image">
                            @endif

                            @if ($kosan->persentase_diskon > 0)
                                <div class="kosan-badge diskon">Diskon {{ $kosan->persentase_diskon }}%</div>
                            @elseif($kosan->kos_unggulan)
                                <div class="kosan-badge unggulan">Unggulan</div>
                            @elseif($kosan->created_at->diffInDays(now()) < 7)
                                <div class="kosan-badge baru">Baru</div>
                            @endif

                            <button class="wishlist-btn {{ $kosan->difavoritkanOleh(Auth::id()) ? 'active' : '' }}"
                                data-kosan-id="{{ $kosan->kosan_id }}"
                                data-favorit-url="{{ route('users.kosan.toggle-favorite', $kosan->kosan_id) }}">
                                <i class="fa{{ $kosan->difavoritkanOleh(Auth::id()) ? 's' : 'r' }} fa-heart"></i>
                            </button>
                        </div>
                        <div class="kosan-content">
                            <div class="kosan-rating">
                                <i class="fas fa-star"></i>
                                <span>{{ number_format($kosan->getRataRataRatingAttribute(), 1) }}</span>
                                <span class="rating-count">({{ $kosan->getJumlahUlasanAttribute() }})</span>
                            </div>
                            <h5 class="kosan-title">{{ $kosan->nama_kosan }}</h5>
                            <p class="kosan-location">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $kosan->kecamatan }}, {{ $kosan->kota }}
                            </p>
                            <div class="kosan-type">
                                <span
                                    class="badge {{ $kosan->jenis_kos == 'putra' ? 'bg-primary' : ($kosan->jenis_kos == 'putri' ? 'bg-danger' : 'bg-success') }}">
                                    Kos {{ ucfirst($kosan->jenis_kos) }}
                                </span>
                                @if ($kosan->kamar_tersedia > 0)
                                    <span class="badge bg-info">{{ $kosan->kamar_tersedia }} kamar tersedia</span>
                                @else
                                    <span class="badge bg-danger">Kamar penuh</span>
                                @endif
                            </div>
                            <div class="kosan-facilities mt-2">
                                @if ($kosan->fasilitas_wifi)
                                    <span data-bs-toggle="tooltip" title="WiFi"><i class="fas fa-wifi"></i></span>
                                @endif

                                @if ($kosan->fasilitas_ac)
                                    <span data-bs-toggle="tooltip" title="AC"><i class="fas fa-snowflake"></i></span>
                                @endif

                                @if ($kosan->fasilitas_kamar_mandi_dalam)
                                    <span data-bs-toggle="tooltip" title="Kamar Mandi Dalam"><i
                                            class="fas fa-bath"></i></span>
                                @endif

                                @if ($kosan->fasilitas_parkir)
                                    <span data-bs-toggle="tooltip" title="Parkir"><i class="fas fa-parking"></i></span>
                                @endif
                            </div>
                            <div class="kosan-price mt-2">
                                @if ($kosan->persentase_diskon > 0)
                                    <span class="price-old">Rp
                                        {{ number_format($kosan->harga_bulanan, 0, ',', '.') }}</span>
                                    <span class="price">Rp
                                        {{ number_format($kosan->getHargaSetelahDiskonAttribute(), 0, ',', '.') }}</span>
                                @else
                                    <span class="price">Rp {{ number_format($kosan->harga_bulanan, 0, ',', '.') }}</span>
                                @endif
                                <span class="period">/ bulan</span>
                            </div>
                        </div>
                        <div class="kosan-action">
                            <a href="{{ route('users.kosan.show', $kosan->kosan_id) }}"
                                class="btn btn-sm btn-primary w-100">Detail</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state text-center py-5">
                        <div class="empty-state-image mb-3">
                            <img src="{{ asset('images/empty-search.svg') }}" alt="Tidak ada kosan" class="img-fluid">
                        </div>
                        <h3>Kosan Tidak Ditemukan</h3>
                        <p class="text-muted">Maaf, tidak ada kosan yang sesuai dengan filter Anda. Coba ubah filter atau
                            reset pencarian.</p>
                        <a href="{{ route('users.kosan.index') }}" class="btn btn-primary mt-3">Reset Filter</a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $kosans->withQueryString()->links() }}
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Kosan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" action="{{ route('users.kosan.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="keyword" class="form-label">Kata Kunci</label>
                                    <input type="text" class="form-control" id="keyword" name="keyword"
                                        value="{{ request()->keyword }}" placeholder="Cari nama kos, alamat, dll">
                                </div>

                                <div class="mb-3">
                                    <label for="kota" class="form-label">Kota</label>
                                    <select class="form-select" id="kota" name="kota">
                                        <option value="">Semua Kota</option>
                                        @foreach ($cities ?? [] as $city)
                                            <option value="{{ $city }}"
                                                {{ request()->kota == $city ? 'selected' : '' }}>{{ $city }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="kampus" class="form-label">Dekat Kampus</label>
                                    <select class="form-select" id="kampus" name="kampus">
                                        <option value="">Semua Kampus</option>
                                        <option value="Universitas Teuku Umar" {{ request()->kampus == 'Universitas Teuku Umar' ? 'selected' : '' }}>Universitas Teuku Umar</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="tipe_kosan" class="form-label">Jenis Kos</label>
                                    <select class="form-select" id="tipe_kosan" name="tipe_kosan">
                                        <option value="">Semua Jenis</option>
                                        <option value="putra" {{ request()->tipe_kosan == 'putra' ? 'selected' : '' }}>Kos Putra</option>
                                        <option value="putri" {{ request()->tipe_kosan == 'putri' ? 'selected' : '' }}>Kos Putri</option>
                                        <option value="campur" {{ request()->tipe_kosan == 'campur' ? 'selected' : '' }}>Kos Campur</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Rentang Harga</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="number" class="form-control" name="harga_min"
                                                placeholder="Min" value="{{ request()->harga_min }}" min="0">
                                        </div>
                                        <div class="col-6">
                                            <input type="number" class="form-control" name="harga_max"
                                                placeholder="Max" value="{{ request()->harga_max }}" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fasilitas</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="fasilitas_wifi"
                                            name="fasilitas[]" value="Wifi"
                                            {{ in_array('Wifi', request('fasilitas', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="fasilitas_wifi">WiFi</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="fasilitas_ac"
                                            name="fasilitas[]" value="AC"
                                            {{ in_array('AC', request('fasilitas', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="fasilitas_ac">AC</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="fasilitas_kamar_mandi_dalam"
                                            name="fasilitas[]" value="Kamar Mandi Dalam"
                                            {{ in_array('Kamar Mandi Dalam', request('fasilitas', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="fasilitas_kamar_mandi_dalam">Kamar Mandi
                                            Dalam</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="fasilitas_parkir"
                                            name="fasilitas[]" value="Tempat Parkir"
                                            {{ in_array('Tempat Parkir', request('fasilitas', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="fasilitas_parkir">Parkir</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="fasilitas_kasur"
                                            name="fasilitas[]" value="Kasur"
                                            {{ in_array('Kasur', request('fasilitas', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="fasilitas_kasur">Kasur</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="fasilitas_lemari"
                                            name="fasilitas[]" value="Lemari"
                                            {{ in_array('Lemari', request('fasilitas', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="fasilitas_lemari">Lemari</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary"
                        onclick="document.getElementById('filterForm').submit()">Terapkan Filter</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        :root {
            --primary: #4f6f52;
            --primary-dark: #3a4d39;
            --primary-light: #a4c3a2;
            --secondary: #eef5e4;
            --accent: #f0a04b;
        }

        /* Kosan Header */
        .kosan-header {
            /* background-color: white; */
            background: linear-gradient(to right, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)), url('{{ asset('user/img/bg-pattern.jpg') }}');
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .kosan-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 5px;
        }

        .action-btn-equal {
            width: 135px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 767.98px) {
            .action-btn-equal {
                width: 110px;
                height: 38px;
                font-size: 0.875rem;
            }
        }

        /* Filter Tags */
        .filter-tags {
            background-color: white;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .filter-tag-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            flex: 1;
        }

        .filter-tag {
            display: inline-flex;
            align-items: center;
            background-color: var(--secondary);
            color: var(--primary-dark);
            font-size: 13px;
            padding: 5px 12px;
            border-radius: 50px;
        }

        .filter-tag-remove {
            margin-left: 8px;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background-color: rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
            text-decoration: none;
        }

        .filter-tag-remove:hover {
            background-color: var(--primary);
            color: white;
        }

        /* Kosan Card */
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

        /* Empty State */
        .empty-state {
            background-color: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .empty-state-image {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }
        .empty-state-image img {
            max-height: 180px;
            object-fit: contain;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Handle favorit/wishlist buttons
            const wishlistButtons = document.querySelectorAll('.wishlist-btn');

            wishlistButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    const kosanId = this.getAttribute('data-kosan-id');
                    const url = this.getAttribute('data-favorit-url');
                    const icon = this.querySelector('i');
                    const isActive = this.classList.contains('active');

                    // Send AJAX request
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            credentials: 'same-origin'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (data.action === 'added') {
                                    button.classList.add('active');
                                    icon.classList.remove('far');
                                    icon.classList.add('fas');
                                } else {
                                    button.classList.remove('active');
                                    icon.classList.remove('fas');
                                    icon.classList.add('far');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                });
            });
        });
    </script>
@endpush
