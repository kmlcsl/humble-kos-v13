@extends('layouts.user.app')

@section('title', 'Daftar Booking Saya')

@section('content')
    <!-- Breadcrumb -->
    <div class="container-fluid mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('users.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Booking Saya</li>
            </ol>
        </nav>
    </div>

    <div class="container-fluid mb-5">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title mb-4">Daftar Booking Saya</h2>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($bookings->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-4x mb-3 text-muted"></i>
                        <h4>Belum Ada Booking</h4>
                        <p class="text-muted">Anda belum melakukan booking kosan apa pun.</p>
                        <a href="{{ route('users.kosan.index') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-search me-2"></i> Cari Kosan
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID Booking</th>
                                    <th>Kosan</th>
                                    <th>Tanggal Check-In</th>
                                    <th>Durasi</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookings as $booking)
                                    <tr>
                                        <td>{{ $booking->booking_id }}</td>
                                        <td>
                                            @if ($booking->kosan)
                                                <div class="d-flex align-items-center">
                                                    <div class="kosan-image me-2">
                                                        @if ($booking->kosan->fotoUtama)
                                                            <img src="{{ asset('storage/' . $booking->kosan->fotoUtama->path_gambar) }}"
                                                                alt="{{ $booking->kosan->nama_kosan }}"
                                                                class="img-fluid rounded"
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                        @else
                                                            <img src="{{ asset('images/no-image.jpg') }}"
                                                                alt="{{ $booking->kosan->nama_kosan }}"
                                                                class="img-fluid rounded"
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                        @endif
                                                    </div>
                                                    <div>
                                                        @if($booking->kosan)
                                                            <a href="{{ route('users.kosan.show', $booking->kosan->kosan_id) }}"
                                                                class="text-decoration-none">
                                                                {{ $booking->kosan->nama_kosan }}
                                                            </a>
                                                            <div class="small text-muted">
                                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                                {{ $booking->kosan->kecamatan }}, {{ $booking->kosan->kota }}
                                                            </div>
                                                        @else
                                                            <span class="text-muted">Kosan tidak tersedia</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">Kosan tidak tersedia</span>
                                            @endif
                                        </td>
                                        <td>{{ $booking->formatted_tanggal_mulai }}</td>
                                        <td>{{ $booking->durasi_text }}</td>
                                        <td>{{ $booking->formatted_total_harga }}</td>
                                        <td>{!! $booking->status_badge !!}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('users.bookings.show', $booking->booking_id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                                @if ($booking->can_be_canceled)
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#cancelModal{{ $booking->booking_id }}">
                                                        <i class="fas fa-times"></i> Batalkan
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Cancel Modal -->
                                    @if ($booking->can_be_canceled)
                                        <div class="modal fade" id="cancelModal{{ $booking->booking_id }}" tabindex="-1"
                                            aria-labelledby="cancelModalLabel{{ $booking->booking_id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="cancelModalLabel{{ $booking->booking_id }}">
                                                            Konfirmasi Pembatalan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-warning">
                                                            <i class="fas fa-exclamation-triangle me-2"></i> Pembatalan
                                                            booking mungkin dikenakan biaya
                                                            sesuai dengan kebijakan pembatalan.
                                                        </div>
                                                        <p>Apakah Anda yakin ingin membatalkan booking ini?</p>
                                                        <p class="text-danger"><strong>Tindakan ini tidak dapat
                                                                dibatalkan.</strong></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Tutup</button>
                                                        <form action="{{ route('users.bookings.cancel', $booking->booking_id) }}"
                                                            method="POST" class="d-inline cancel-form">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-danger">Batalkan
                                                                Booking</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $bookings->links() }}
                    </div>
                @endif
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

        /* Card styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* Section titles */
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 15px;
        }

        /* Table styles */
        .table {
            vertical-align: middle;
        }

        .table thead th {
            border-top: none;
            border-bottom: 2px solid var(--primary-light);
            background-color: var(--secondary);
            color: var(--primary-dark);
            font-weight: 600;
            padding: 12px 8px;
        }

        .table tbody tr:hover {
            background-color: rgba(238, 245, 228, 0.3);
        }

        .table td {
            padding: 12px 8px;
        }

        /* Form control styles */
        .form-control,
        .form-select {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.25rem rgba(164, 195, 162, 0.25);
        }

        /* Badge styles */
        .badge {
            padding: 6px 10px;
            font-weight: 500;
            border-radius: 6px;
        }

        /* Button styles */
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-outline-primary:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .btn-outline-secondary,
        .btn-outline-danger {
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-outline-secondary:hover,
        .btn-outline-danger:hover {
            transform: translateY(-2px);
        }

        /* Status badges */
        .badge.bg-warning {
            background-color: #fff3cd !important;
            color: #664d03 !important;
        }

        .badge.bg-success {
            background-color: #d1e7dd !important;
            color: #0f5132 !important;
        }

        .badge.bg-danger {
            background-color: #f8d7da !important;
            color: #842029 !important;
        }

        .badge.bg-secondary {
            background-color: #e2e3e5 !important;
            color: #41464b !important;
        }

        /* Summary card styles */
        .booking-details,
        .price-calculation {
            color: #6c757d;
        }

        .total-price {
            background-color: var(--secondary) !important;
            border-radius: 8px;
        }

        /* Modal styles */
        .modal-content {
            border-radius: 12px;
            border: none;
        }

        .modal-header {
            border-bottom: 1px solid #e0e0e0;
            background-color: var(--secondary);
        }

        .modal-footer {
            border-top: 1px solid #e0e0e0;
        }

        /* Alert styles */
        .alert-info,
        .alert-warning {
            border-radius: 8px;
        }

        .alert-info {
            background-color: var(--secondary);
            border-color: var(--primary-light);
            color: var(--primary-dark);
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .sticky-top {
                position: relative;
                top: 0 !important;
            }

            .btn-group {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }

            .btn-group .btn {
                width: 100%;
                margin-left: 0 !important;
            }
        }

        /* Modal fix - minimal */
        .modal-static {
            transform: none !important;
        }

        /* Spinner animation */
        @keyframes spinner {
            to {
                transform: rotate(360deg);
            }
        }

        .spinner {
            display: inline-block;
            width: 1em;
            height: 1em;
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top-color: currentColor;
            animation: spinner 0.6s linear infinite;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips if Bootstrap is available
            if (typeof bootstrap !== 'undefined') {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            // Function to calculate end date based on start date and duration
            function calculateEndDate() {
                const startDateInput = document.getElementById('tanggal_mulai');
                const durationSelect = document.getElementById('jenis_durasi');
                const endDateInput = document.getElementById('tanggal_selesai');
                const summaryStartDate = document.getElementById('summaryStartDate');
                const summaryEndDate = document.getElementById('summaryEndDate');
                const summaryDuration = document.getElementById('summaryCost');

                if (!startDateInput || !durationSelect || !endDateInput) return;

                const startDate = new Date(startDateInput.value);
                const selectedOption = durationSelect.options[durationSelect.selectedIndex];
                const monthsToAdd = parseInt(selectedOption.getAttribute('data-value'));
                const durationText = selectedOption.textContent.trim();

                // Set the hidden field value
                document.getElementById('nilai_durasi').value = monthsToAdd;

                // Update duration display
                if (summaryDuration) {
                    summaryDuration.textContent = durationText;
                }

                // Calculate end date
                const endDate = new Date(startDate);
                endDate.setMonth(endDate.getMonth() + monthsToAdd);

                // Format date for form field (YYYY-MM-DD)
                const formattedEndDate = endDate.toISOString().split('T')[0];
                endDateInput.value = formattedEndDate;

                // Format dates for summary display
                if (summaryStartDate && summaryEndDate) {
                    const options = {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    };
                    summaryStartDate.textContent = formatDate(startDate);
                    summaryEndDate.textContent = formatDate(endDate);
                }
            }

            // Function to format date in Indonesian format
            function formatDate(date) {
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                const day = date.getDate();
                const month = months[date.getMonth()];
                const year = date.getFullYear();

                return `${day} ${month} ${year}`;
            }

            // Function to update price calculation
            function updatePriceCalculation() {
                const durationSelect = document.getElementById('jenis_durasi');
                const roomCountSelect = document.getElementById('jumlah_kamar');
                const summaryRoomPrice = document.getElementById('summaryRoomPrice');
                const summaryDuration = document.getElementById('summaryDuration');
                const summarySubtotal = document.getElementById('summarySubtotal');
                const summaryTotal = document.getElementById('summaryTotal');
                const summaryRoomCount = document.getElementById('summaryRoomCount');

                if (!durationSelect || !roomCountSelect) return;

                const selectedDuration = durationSelect.options[durationSelect.selectedIndex];
                const durationText = selectedDuration.textContent.trim();
                const pricePerRoom = parseFloat(selectedDuration.getAttribute('data-price'));
                const roomCount = parseInt(roomCountSelect.value);

                // Update room count display
                if (summaryRoomCount) {
                    summaryRoomCount.textContent = roomCount + ' Kamar';
                }

                // Format currency
                const formatCurrency = (value) => {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                };

                // Update price displays
                if (summaryRoomPrice) {
                    summaryRoomPrice.textContent = formatCurrency(pricePerRoom);
                }

                // Update duration display
                if (summaryDuration) {
                    summaryDuration.textContent = `${durationText} x ${roomCount} Kamar`;
                }

                // Calculate subtotal and total
                const subtotal = pricePerRoom * roomCount;

                // Update subtotal and total displays
                if (summarySubtotal) {
                    summarySubtotal.textContent = formatCurrency(subtotal);
                }

                if (summaryTotal) {
                    summaryTotal.textContent = formatCurrency(subtotal);
                }
            }

            // Add event listeners to form elements
            const startDateInput = document.getElementById('tanggal_mulai');
            const durationSelect = document.getElementById('jenis_durasi');
            const roomCountSelect = document.getElementById('jumlah_kamar');

            if (startDateInput) {
                startDateInput.addEventListener('change', function() {
                    calculateEndDate();
                });
            }

            if (durationSelect) {
                durationSelect.addEventListener('change', function() {
                    calculateEndDate();
                    updatePriceCalculation();
                });
            }

            if (roomCountSelect) {
                roomCountSelect.addEventListener('change', function() {
                    updatePriceCalculation();
                });
            }

            // Form validation
            const bookingForm = document.getElementById('bookingForm');
            if (bookingForm) {
                bookingForm.addEventListener('submit', function(event) {
                    const agreeCheckbox = document.getElementById('setuju');
                    if (agreeCheckbox && !agreeCheckbox.checked) {
                        event.preventDefault();
                        alert('Anda harus menyetujui syarat dan ketentuan untuk melanjutkan.');
                    }
                });
            }

            // Initialize calculations on page load
            if (startDateInput && durationSelect) {
                calculateEndDate();
            }

            if (durationSelect && roomCountSelect) {
                updatePriceCalculation();
            }

            // ==== MODAL FIX - SIMPLE VERSION ====

            // Handle cancel buttons
            const cancelButtons = document.querySelectorAll('[data-bs-target^="#cancelModal"]');
            cancelButtons.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    // Simple fix to prevent flickering
                    const modalId = this.getAttribute('data-bs-target');
                    const modalElement = document.querySelector(modalId);

                    if (modalElement) {
                        // Ensure backdrop is static
                        const modalOptions = {
                            backdrop: 'static',
                            keyboard: false
                        };

                        // Create a new modal instance
                        const modal = new bootstrap.Modal(modalElement, modalOptions);

                        // Show modal
                        modal.show();

                        // Add static class to prevent transform
                        modalElement.querySelector('.modal-dialog').classList.add('modal-static');

                        // Prevent default action
                        e.preventDefault();
                    }
                });
            });

            // Handle cancel forms
            const cancelForms = document.querySelectorAll('.cancel-form');
            cancelForms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    // Get submit button
                    const submitButton = this.querySelector('button[type="submit"]');

                    if (submitButton && !submitButton.disabled) {
                        // Disable button
                        submitButton.disabled = true;

                        // Change text to loading
                        submitButton.innerHTML = '<span class="spinner me-2"></span> Memproses...';

                        // Enable after timeout (fallback)
                        setTimeout(function() {
                            if (document.body.contains(submitButton)) {
                                submitButton.disabled = false;
                                submitButton.innerHTML = 'Batalkan Booking';
                            }
                        }, 5000);
                    }
                });
            });
        });
    </script>
@endpush
