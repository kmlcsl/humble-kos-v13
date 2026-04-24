@extends('layouts.user.app')

@section('title', 'Tulis Ulasan - ' . $kosan->nama_kosan)

@section('content')
    <!-- Breadcrumb -->
    <div class="container-fluid mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('users.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.kosan.index') }}">Daftar Kosan</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.kosan.show', $kosan->kosan_id) }}">{{ $kosan->nama_kosan }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Tulis Ulasan</li>
            </ol>
        </nav>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Tulis Ulasan untuk {{ $kosan->nama_kosan }}</h4>
                    </div>
                    <div class="card-body">
                        <!-- Kosan Info Summary -->
                        <div class="kosan-summary mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    @if ($kosan->fotoUtama)
                                        <img src="{{ asset('storage/' . $kosan->fotoUtama->path_gambar) }}"
                                            alt="{{ $kosan->nama_kosan }}" class="img-fluid rounded">
                                    @else
                                        <img src="{{ asset('images/no-image.jpg') }}" alt="{{ $kosan->nama_kosan }}"
                                            class="img-fluid rounded">
                                    @endif
                                </div>
                                <div class="col-md-9">
                                    <h5>{{ $kosan->nama_kosan }}</h5>
                                    <p class="text-muted mb-1"><i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $kosan->alamat }}, {{ $kosan->kecamatan }}, {{ $kosan->kota }}</p>
                                    <div class="kosan-badges mb-2">
                                        <span
                                            class="badge {{ $kosan->jenis_kos == 'putra' ? 'bg-primary' : ($kosan->jenis_kos == 'putri' ? 'bg-danger' : 'bg-success') }}">
                                            Kos {{ ucfirst($kosan->jenis_kos) }}
                                        </span>
                                        @if ($kosan->kos_unggulan)
                                            <span class="badge bg-warning text-dark">Kos Unggulan</span>
                                        @endif
                                    </div>
                                    <div class="current-rating">
                                        <span class="text-muted">Rating saat ini: </span>
                                        <div class="d-inline-block">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= round($kosan->getRataRataRatingAttribute()))
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-warning"></i>
                                                @endif
                                            @endfor
                                            <span
                                                class="ms-1">{{ number_format($kosan->getRataRataRatingAttribute(), 1) }}
                                                / 5</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Review Form -->
                        <form action="{{ route('users.kosan.store-review', $kosan->kosan_id) }}" method="POST">
                            @csrf

                            <!-- Rating Input -->
                            <div class="mb-4">
                                <label class="form-label">Beri Rating <span class="text-danger">*</span></label>
                                <div class="rating-input">
                                    <div class="star-rating">
                                        @for ($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="star{{ $i }}" name="rating"
                                                value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}
                                                required />
                                            <label for="star{{ $i }}" title="{{ $i }} bintang"><i
                                                    class="fas fa-star"></i></label>
                                        @endfor
                                    </div>
                                    <div class="rating-text mt-2">
                                        <span id="ratingText">Silakan pilih rating</span>
                                    </div>
                                    @error('rating')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Komentar Input -->
                            <div class="mb-4">
                                <label for="komentar" class="form-label">Komentar <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="komentar" name="komentar" rows="6"
                                    placeholder="Bagikan pengalaman Anda tinggal di kosan ini..." required>{{ old('komentar') }}</textarea>
                                <div class="form-text">Minimal 10 karakter. Silakan berikan ulasan jujur tentang pengalaman
                                    Anda.</div>
                                @error('komentar')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Review Guidelines -->
                            <div class="review-guidelines mb-4">
                                <h6>Panduan Menulis Review:</h6>
                                <ul>
                                    <li>Berikan ulasan yang jujur dan objektif</li>
                                    <li>Jelaskan pengalaman Anda secara detail</li>
                                    <li>Sebutkan kelebihan dan kekurangan kosan</li>
                                    <li>Hindari bahasa yang tidak pantas atau konten yang menyinggung</li>
                                </ul>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('users.kosan.show', $kosan->kosan_id) }}"
                                    class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            cursor: pointer;
            font-size: 30px;
            color: #e1e1e1;
            margin: 0 5px;
            transition: color 0.2s;
        }

        .star-rating input:checked~label,
        .star-rating label:hover,
        .star-rating label:hover~label {
            color: #ffc107;
        }

        .rating-text {
            color: #6c757d;
            font-size: 14px;
        }

        .review-guidelines {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
        }

        .review-guidelines ul {
            margin-bottom: 0;
            padding-left: 20px;
            font-size: 14px;
        }

        .kosan-summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
        }

        .current-rating {
            margin-top: 5px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ratingInputs = document.querySelectorAll('.star-rating input');
            const ratingText = document.getElementById('ratingText');

            const ratingDescriptions = {
                5: 'Sangat Baik - Kosan luar biasa',
                4: 'Baik - Kosan direkomendasikan',
                3: 'Cukup - Kosan rata-rata',
                2: 'Kurang - Ada beberapa masalah',
                1: 'Buruk - Tidak direkomendasikan'
            };

            // Set initial text if a rating is already selected
            const initialChecked = document.querySelector('.star-rating input:checked');
            if (initialChecked) {
                const initialValue = parseInt(initialChecked.value);
                ratingText.textContent = ratingDescriptions[initialValue];
            }

            // Update text when rating is selected
            ratingInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const value = parseInt(this.value);
                    ratingText.textContent = ratingDescriptions[value];
                });
            });

            // Character counter for komentar
            const komentarTextarea = document.getElementById('komentar');

            if (komentarTextarea) {
                komentarTextarea.addEventListener('input', function() {
                    const charCount = this.value.length;
                    const minChars = 10;

                    if (charCount < minChars) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });
            }
        });
    </script>
@endpush
