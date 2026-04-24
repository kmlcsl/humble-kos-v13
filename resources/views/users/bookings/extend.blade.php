@extends('layouts.user.app')

@section('title', 'Perpanjang Booking - ' . $booking->booking_id)

@section('content')
    <!-- Breadcrumb -->
    <div class="container-fluid mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('users.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.bookings.index') }}">Daftar Booking</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.bookings.show', $booking->booking_id) }}">Booking
                        #{{ $booking->booking_id }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Perpanjang Booking</li>
            </ol>
        </nav>
    </div>

    <div class="container-fluid mb-5">
        <div class="row">
            <!-- Form Section -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                        <h4 class="mb-0">Perpanjang Booking</h4>
                        <div>
                            {!! $booking->status_badge !!}
                        </div>
                    </div>
                    <div class="card-body">
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

                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i> Perpanjang durasi booking Anda dengan memilih durasi
                            tambahan di bawah ini.
                        </div>

                        <form action="{{ route('users.bookings.extend', $booking->booking_id) }}" method="POST" id="extendForm">
                            @csrf
                            @method('PUT')

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5 class="text-muted mb-3">Booking Saat Ini</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="ps-0 pe-2"><strong>ID Booking</strong></td>
                                            <td>: {{ $booking->booking_id }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 pe-2"><strong>Durasi</strong></td>
                                            <td>: {{ $booking->durasi_text }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 pe-2"><strong>Check-in</strong></td>
                                            <td>: {{ $booking->formatted_tanggal_mulai }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 pe-2"><strong>Check-out</strong></td>
                                            <td>: {{ $booking->formatted_tanggal_selesai }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="text-muted mb-3">Kosan</h5>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="kosan-image me-3">
                                            @if ($booking->kosan->fotoUtama)
                                                <img src="{{ asset('storage/' . $booking->kosan->fotoUtama->path_gambar) }}"
                                                    alt="{{ $booking->kosan->nama_kosan }}" class="img-fluid rounded"
                                                    style="width: 70px; height: 70px; object-fit: cover;">
                                            @else
                                                <img src="{{ asset('images/no-image.jpg') }}"
                                                    alt="{{ $booking->kosan->nama_kosan }}" class="img-fluid rounded"
                                                    style="width: 70px; height: 70px; object-fit: cover;">
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $booking->kosan->nama_kosan }}</h6>
                                            <p class="mb-0 small text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ $booking->kosan->kecamatan }}, {{ $booking->kosan->kota }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="jenis_durasi" class="form-label">Pilih Durasi Perpanjangan <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('jenis_durasi') is-invalid @enderror"
                                        id="jenis_durasi" name="jenis_durasi" required>
                                        <option value="bulanan" data-value="1"
                                            data-price="{{ $booking->kosan->getHargaSetelahDiskonAttribute() }}">1 Bulan
                                        </option>
                                        @if ($booking->kosan->harga_tiga_bulan)
                                            <option value="tiga_bulan" data-value="3"
                                                data-price="{{ $booking->kosan->harga_tiga_bulan }}">3 Bulan</option>
                                        @endif
                                        @if ($booking->kosan->harga_semester)
                                            <option value="semester" data-value="6"
                                                data-price="{{ $booking->kosan->harga_semester }}">6 Bulan</option>
                                        @endif
                                        @if ($booking->kosan->harga_tahunan)
                                            <option value="tahunan" data-value="12"
                                                data-price="{{ $booking->kosan->harga_tahunan }}">1 Tahun</option>
                                        @endif
                                    </select>
                                    <input type="hidden" name="nilai_durasi" id="nilai_durasi" value="1">
                                    @error('jenis_durasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Check-out Baru</label>
                                    <div class="p-2 border rounded bg-light">
                                        <span id="tanggal_selesai_baru">{{ $booking->formatted_tanggal_selesai }}</span>
                                    </div>
                                    <small class="text-muted">Tanggal check-out baru setelah perpanjangan</small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="mb-3">Ringkasan Biaya</h5>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Biaya Perpanjangan</span>
                                            <span id="harga_perpanjangan">Rp
                                                {{ number_format($booking->kosan->getHargaSetelahDiskonAttribute(), 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Harga per Kamar</span>
                                            <span>Rp
                                                {{ number_format($booking->kosan->getHargaSetelahDiskonAttribute(), 0, ',', '.') }}</span>
                                        </div>

                                        @if ($booking->kosan->persentase_diskon > 0)
                                            <div class="d-flex justify-content-between mb-2 text-success">
                                                <span>Diskon ({{ $booking->kosan->persentase_diskon }}%)</span>
                                                <span>-Rp
                                                    {{ number_format($booking->kosan->harga_bulanan - $booking->kosan->getHargaSetelahDiskonAttribute(), 0, ',', '.') }}</span>
                                            </div>
                                        @endif

                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total Pembayaran</span>
                                            <span id="total_harga" class="text-primary">Rp
                                                {{ number_format($booking->kosan->getHargaSetelahDiskonAttribute(), 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i> Dengan melakukan perpanjangan, Anda
                                    setuju untuk membayar biaya tambahan sesuai dengan durasi perpanjangan yang dipilih.
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('users.bookings.show', $booking->booking_id) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check-circle me-1"></i> Konfirmasi Perpanjangan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar Section -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Informasi Perpanjangan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i> Tentang Perpanjangan</h6>
                            <p class="text-muted small">Perpanjangan booking akan menambah durasi sewa dari tanggal
                                check-out yang sebelumnya telah ditentukan. Biaya perpanjangan akan ditambahkan ke total
                                pembayaran Anda.</p>
                        </div>

                        <div class="mb-4">
                            <h6 class="mb-3"><i class="fas fa-money-bill-wave me-2"></i> Pembayaran</h6>
                            <p class="text-muted small">Setelah mengkonfirmasi perpanjangan, Anda akan diminta untuk
                                melakukan pembayaran dalam waktu 24 jam. Jika tidak, perpanjangan akan dibatalkan secara
                                otomatis.</p>
                        </div>

                        <div>
                            <h6 class="mb-3"><i class="fas fa-question-circle me-2"></i> Pertanyaan?</h6>
                            <p class="text-muted small">Jika Anda memiliki pertanyaan tentang perpanjangan booking, silakan
                                hubungi pemilik kosan atau tim dukungan kami.</p>
                        </div>
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

        /* Card styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            border-bottom: 1px solid #f0f0f0;
            background-color: white;
            padding: 15px 20px;
        }

        /* Badge styles */
        .badge {
            padding: 6px 12px;
            font-weight: 500;
            border-radius: 6px;
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

        /* Table styles */
        .table-borderless td {
            padding: 6px 0;
        }

        /* Button styles */
        .btn {
            border-radius: 8px;
            padding: 10px 16px;
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

        /* Alert styles */
        .alert {
            border-radius: 8px;
        }

        .alert-info {
            background-color: var(--secondary);
            border-color: var(--primary-light);
            color: var(--primary-dark);
        }

        .alert-warning {
            background-color: #fff8e1;
            border-color: #ffe57f;
            color: #856404;
        }

        /* Text colors for status */
        .text-primary {
            color: var(--primary) !important;
        }

        .text-success {
            color: #40916c !important;
        }

        /* Custom background for the new checkout date */
        .bg-light {
            background-color: #f8f9fa !important;
        }

        /* Rounded image for kosan */
        .rounded {
            border-radius: 8px !important;
        }

        /* Custom styles for the price summary card */
        .card.bg-light {
            background-color: #f8f9fa !important;
            border: 1px solid #e9ecef;
        }

        /* Responsive styles */
        @media (max-width: 767.98px) {
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 10px;
            }

            .d-flex.justify-content-between .btn {
                width: 100%;
            }

            .d-flex.align-items-center {
                flex-direction: row;
                text-align: left;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get necessary elements
            const durationSelect = document.getElementById('jenis_durasi');
            const hiddenDurationValue = document.getElementById('nilai_durasi');
            const newCheckoutDate = document.getElementById('tanggal_selesai_baru');
            const extensionPriceDisplay = document.getElementById('harga_perpanjangan');
            const totalPriceDisplay = document.getElementById('total_harga');

            // Function to calculate and display new checkout date based on selected duration
            function updateCheckoutDate() {
                if (!durationSelect || !newCheckoutDate) return;

                // Get the current checkout date from the existing booking
                const currentCheckoutDate = new Date('{{ $booking->tanggal_checkout->format('Y-m-d') }}');
                const selectedOption = durationSelect.options[durationSelect.selectedIndex];
                const monthsToAdd = parseInt(selectedOption.getAttribute('data-value'));

                // Set the hidden input value
                if (hiddenDurationValue) {
                    hiddenDurationValue.value = monthsToAdd;
                }

                // Calculate new checkout date
                const newDate = new Date(currentCheckoutDate);
                newDate.setMonth(newDate.getMonth() + monthsToAdd);

                // Format and display new date
                newCheckoutDate.textContent = formatDate(newDate);
            }

            // Function to update price displays
            function updatePriceDisplays() {
                if (!durationSelect || !extensionPriceDisplay || !totalPriceDisplay) return;

                const selectedOption = durationSelect.options[durationSelect.selectedIndex];
                const price = parseFloat(selectedOption.getAttribute('data-price'));

                // Format and display prices
                const formattedPrice = formatCurrency(price);
                extensionPriceDisplay.textContent = formattedPrice;
                totalPriceDisplay.textContent = formattedPrice;
            }

            // Helper function to format date in Indonesian format
            function formatDate(date) {
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                const day = date.getDate();
                const month = months[date.getMonth()];
                const year = date.getFullYear();

                return `${day} ${month} ${year}`;
            }

            // Helper function to format currency in Indonesian Rupiah
            function formatCurrency(amount) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            }

            // Add event listener to duration select
            if (durationSelect) {
                durationSelect.addEventListener('change', function() {
                    updateCheckoutDate();
                    updatePriceDisplays();
                });

                // Initialize on page load
                updateCheckoutDate();
                updatePriceDisplays();
            }

            // Form submission handling
            const extendForm = document.getElementById('extendForm');
            if (extendForm) {
                extendForm.addEventListener('submit', function(e) {
                    // You can add validation here if needed

                    // Get the submit button
                    const submitButton = this.querySelector('button[type="submit"]');

                    if (submitButton && !submitButton.disabled) {
                        // Disable the button and change text to show processing
                        submitButton.disabled = true;
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';

                        // Enable the button after 3 seconds if the form hasn't been submitted yet
                        // This is a fallback in case the form submission is interrupted
                        setTimeout(function() {
                            if (document.body.contains(submitButton)) {
                                submitButton.disabled = false;
                                submitButton.innerHTML =
                                    '<i class="fas fa-check-circle me-1"></i> Konfirmasi Perpanjangan';
                            }
                        }, 3000);
                    }
                });
            }
        });
    </script>
@endpush
