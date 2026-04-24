@extends('layouts.user.app')

@section('title', 'Edit Ulasan')

@section('content')
    <!-- Breadcrumb -->
    <div class="container-fluid mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('users.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.reviews.index') }}">Ulasan Saya</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Ulasan</li>
            </ol>
        </nav>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Ulasan</h4>
                    </div>
                    <div class="card-body">
                        <!-- Kosan Info Summary -->
                        <div class="kosan-summary mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    @if ($review->kosan && $review->kosan->fotoUtama)
                                        <img src="{{ asset('storage/' . $review->kosan->fotoUtama->path_gambar) }}"
                                            alt="{{ $review->kosan->nama_kosan }}" class="img-fluid rounded">
                                    @else
                                        <img src="{{ asset('images/no-image.jpg') }}"
                                            alt="{{ $review->kosan ? $review->kosan->nama_kosan : 'Kosan' }}"
                                            class="img-fluid rounded">
                                    @endif
                                </div>
                                <div class="col-md-9">
                                    <h5>{{ $review->kosan ? $review->kosan->nama_kosan : 'Kosan tidak tersedia' }}</h5>
                                    @if ($review->kosan)
                                        <p class="text-muted mb-1"><i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $review->kosan->alamat }}, {{ $review->kosan->kecamatan }},
                                            {{ $review->kosan->kota }}</p>
                                        <div class="kosan-badges mb-2">
                                            <span
                                                class="badge {{ $review->kosan->jenis_kos == 'putra' ? 'bg-primary' : ($review->kosan->jenis_kos == 'putri' ? 'bg-danger' : 'bg-success') }}">
                                                Kos {{ ucfirst($review->kosan->jenis_kos) }}
                                            </span>
                                            @if ($review->kosan->kos_unggulan)
                                                <span class="badge bg-warning text-dark">Kos Unggulan</span>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="current-rating">
                                        <span class="text-muted">Rating saat ini: </span>
                                        <div class="d-inline-block">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $review->rating)
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-warning"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Review Edit Form -->
                        <form action="{{ route('users.reviews.update', $review->review_id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Rating Input -->
                            <div class="mb-4">
                                <label class="form-label">Beri Rating <span class="text-danger">*</span></label>
                                <div class="rating-input">
                                    <div class="star-rating">
                                        @for ($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="star{{ $i }}" name="rating"
                                                value="{{ $i }}"
                                                {{ old('rating', $review->rating) == $i ? 'checked' : '' }} required />
                                            <label for="star{{ $i }}" title="{{ $i }} bintang"><i
                                                    class="fas fa-star"></i></label>
                                        @endfor
                                    </div>
                                    <div class="rating-text mt-2">
                                        <span id="ratingText"></span>
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
                                    placeholder="Bagikan pengalaman Anda tinggal di kosan ini..." required>{{ old('komentar', $review->komentar) }}</textarea>
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
                                <a href="{{ route('users.reviews.index') }}" class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
        :root {
            --primary: #4f6f52;
            --primary-dark: #3a4d39;
            --primary-light: #a4c3a2;
            --secondary: #eef5e4;
            --accent: #f0a04b;
        }

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

        /* Card styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header {
            border-bottom: 1px solid #f0f0f0;
            background-color: white;
            padding: 15px 20px;
        }

        /* Form control styles */
        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
        }

        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.25rem rgba(164, 195, 162, 0.25);
        }

        /* Button styles */
        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-outline-secondary {
            transition: all 0.3s;
        }

        .btn-outline-secondary:hover {
            transform: translateY(-2px);
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .kosan-summary .row {
                flex-direction: column;
            }

            .kosan-summary .col-md-3 {
                margin-bottom: 15px;
            }

            .star-rating {
                justify-content: center;
            }
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

            // Set initial text based on current review rating
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
                // Initial validation
                validateTextarea(komentarTextarea);

                komentarTextarea.addEventListener('input', function() {
                    validateTextarea(this);
                });
            }

            // Form submission handler
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const submitButton = this.querySelector('button[type="submit"]');

                if (submitButton && !submitButton.disabled) {
                    // Disable the button and change text to show processing
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';

                    // Enable the button after 3 seconds if the form hasn't been submitted yet
                    setTimeout(function() {
                        if (document.body.contains(submitButton)) {
                            submitButton.disabled = false;
                            submitButton.innerHTML = 'Simpan Perubahan';
                        }
                    }, 3000);
                }
            });

            // Function to validate textarea
            function validateTextarea(textarea) {
                const charCount = textarea.value.length;
                const minChars = 10;

                if (charCount < minChars) {
                    textarea.classList.add('is-invalid');
                    textarea.classList.remove('is-valid');
                } else {
                    textarea.classList.remove('is-invalid');
                    textarea.classList.add('is-valid');
                }
            }
        });
    </script>
@endpush
