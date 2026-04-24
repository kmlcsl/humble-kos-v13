@extends('layouts.user.app')

@section('title', 'Ulasan Saya')

@section('content')
    <!-- Breadcrumb -->
    <div class="container-fluid mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('users.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ulasan Saya</li>
            </ol>
        </nav>
    </div>

    <div class="container-fluid mb-5">
        <div class="reviews-header d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Ulasan Saya</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if ($reviews->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="empty-reviews">
                        <i class="far fa-comment-dots fa-4x text-muted mb-3"></i>
                        <h4>Belum Ada Ulasan</h4>
                        <p class="text-muted">Anda belum memberikan ulasan untuk kosan manapun.</p>
                        <a href="{{ route('users.kosan.index') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-search me-2"></i> Cari Kosan untuk Diulas
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                @foreach ($reviews as $review)
                    <div class="col-lg-6 mb-4">
                        <div class="card review-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="review-rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $review->rating)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                        <span
                                            class="ms-2 review-date text-muted">{{ $review->created_at->format('d M Y') }}</span>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item"
                                                    href="{{ route('users.reviews.update', $review->review_id) }}"><i
                                                        class="fas fa-edit me-2"></i> Edit</a></li>
                                            <li>
                                                <button class="dropdown-item text-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $review->review_id }}">
                                                    <i class="fas fa-trash-alt me-2"></i> Hapus
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="review-kosan mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="kosan-image me-3">
                                            @if ($review->kosan && $review->kosan->fotoUtama)
                                                <img src="{{ asset('storage/' . $review->kosan->fotoUtama->path_gambar) }}"
                                                    alt="{{ $review->kosan->nama_kosan }}" class="img-fluid rounded"
                                                    style="width: 70px; height: 70px; object-fit: cover;">
                                            @else
                                                <img src="{{ asset('images/no-image.jpg') }}"
                                                    alt="{{ $review->kosan ? $review->kosan->nama_kosan : 'Kosan' }}"
                                                    class="img-fluid rounded"
                                                    style="width: 70px; height: 70px; object-fit: cover;">
                                            @endif
                                        </div>
                                        <div>
                                            <h5 class="mb-1">
                                                {{ $review->kosan ? $review->kosan->nama_kosan : 'Kosan tidak tersedia' }}
                                            </h5>
                                            <p class="mb-0 text-muted small">
                                                @if ($review->kosan)
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ $review->kosan->kecamatan }}, {{ $review->kosan->kota }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="review-content">
                                    <p class="review-text mb-0">{{ $review->komentar }}</p>
                                </div>

                                @if ($review->terverifikasi)
                                    <div class="verified-badge mt-3">
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>
                                            Terverifikasi</span>
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer bg-white border-top-0">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('users.kosan.show', $review->kosan_id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> Lihat Kosan
                                    </a>
                                    <a href="{{ route('users.reviews.update', $review->review_id) }}"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit me-1"></i> Edit Ulasan
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal{{ $review->review_id }}" tabindex="-1"
                            aria-labelledby="deleteModalLabel{{ $review->review_id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel{{ $review->review_id }}">Konfirmasi Hapus
                                            Ulasan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Apakah Anda yakin ingin menghapus ulasan ini?</p>
                                        <p class="text-danger"><strong>Tindakan ini tidak dapat dibatalkan.</strong></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('users.reviews.delete', $review->review_id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Hapus Ulasan</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $reviews->links() }}
            </div>
        @endif
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

        /* Card styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .review-card {
            transition: transform 0.3s ease;
        }

        .review-card:hover {
            transform: translateY(-5px);
        }

        .review-text {
            color: #495057;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        /* Star rating styles */
        .review-rating {
            font-size: 1.1rem;
        }

        .review-date {
            font-size: 0.85rem;
        }

        /* Empty state styles */
        .empty-reviews {
            padding: 2rem 0;
        }

        /* Button styles */
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        /* Dropdown and modal styles */
        .dropdown-item:active {
            background-color: var(--primary);
        }

        .modal-content {
            border-radius: 12px;
            border: none;
        }

        .modal-header {
            border-bottom: 1px solid #f0f0f0;
            background-color: var(--secondary);
        }

        .modal-footer {
            border-top: 1px solid #f0f0f0;
        }

        /* Badge styles */
        .verified-badge .badge {
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 20px;
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .review-kosan {
                flex-direction: column;
                align-items: flex-start;
            }

            .kosan-image {
                margin-bottom: 1rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle modal confirmations
            const deleteButtons = document.querySelectorAll('[data-bs-toggle="modal"]');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // You can add additional logic here if needed
                    console.log('Delete modal opened for review: ' + this.dataset.reviewId);
                });
            });

            // Handle form submissions to prevent accidental double submissions
            const forms = document.querySelectorAll('form');

            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Get the submit button
                    const submitButton = this.querySelector('button[type="submit"]');

                    if (submitButton && !submitButton.disabled) {
                        // Disable the button and change text to show processing
                        submitButton.disabled = true;
                        submitButton.innerHTML =
                            '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';

                        // Enable the button after 3 seconds if the form hasn't been submitted yet
                        // This is a fallback in case the form submission is interrupted
                        setTimeout(function() {
                            if (document.body.contains(submitButton)) {
                                submitButton.disabled = false;
                                submitButton.innerHTML = 'Hapus Ulasan';
                            }
                        }, 3000);
                    }
                });
            });
        });
    </script>
@endpush
