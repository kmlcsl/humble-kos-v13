@extends('layouts.user.app')

@section('title', 'Favorit Saya')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Kosan Favorit Saya</h1>
        </div>

        <div class="row">
            @forelse($favorites as $kosan)
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
                <div class="col-12" style="min-height: 60vh; display: flex; align-items: center; justify-content: center;">
                    <div class="empty-state py-5 d-flex flex-column align-items-center">
                        <img src="{{ asset('images/empty-search.svg') }}" alt="Tidak ada kosan favorit" class="img-fluid mb-4"
                            style="max-height: 100px;">
                        <h3>Belum Ada Favorit</h3>
                        <p class="text-muted text-center">Anda belum menambahkan kosan ke favorit. Mulai jelajahi dan simpan kosan yang Anda suka.</p>
                        <a href="{{ route('users.kosan.index') }}" class="btn btn-primary mt-3">Cari Kosan</a>
                    </div>
                </div>
            @endforelse
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
        
        .empty-state {
            background-color: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
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
                                    // Remove the card from the view
                                    button.closest('.col-xl-3').remove();
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