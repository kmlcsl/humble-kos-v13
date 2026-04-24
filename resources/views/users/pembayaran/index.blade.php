@extends('layouts.user.app')

@section('title', 'Pembayaran Booking #' . $bookings->booking_id)

@section('content')
    <!-- Breadcrumb -->
    <div class="container-fluid mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('users.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.bookings.index') }}">Daftar Booking</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.bookings.show', $bookings->booking_id) }}">Booking
                        #{{ $bookings->booking_id }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pembayaran</li>
            </ol>
        </nav>
    </div>

    <div class="container-fluid mb-5">
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

        {{-- Informasi alur pembayaran --}}
        @if ($bookings->status_booking === 'pending' && (!$pembayaran || $pembayaran->status_pembayaran === 'pending'))
            <div class="alert alert-info mb-4">
                <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Selesaikan Pembayaran Anda</h5>
                <hr>
                <p class="mb-0">
                    <strong>Langkah berikutnya:</strong><br>
                    1. Pilih metode pembayaran di bawah ini<br>
                    2. Setelah pembayaran berhasil, booking Anda akan <strong>otomatis dikonfirmasi</strong><br>
                    3. Anda akan menerima detail booking dan dapat mulai menghuni kosan sesuai jadwal check-in
                </p>
            </div>
        @endif

        <div class="row">
            <!-- Payment Details -->
            <div class="col-lg-8 mb-4">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                        <h4 class="mb-0">Pembayaran</h4>
                        <div>
                            @if ($pembayaran)
                                {!! $pembayaran->status_badge !!}
                            @else
                                <span class="badge bg-secondary">Belum ada pembayaran</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="text-muted mb-3">Informasi Pembayaran</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="ps-0 pe-2"><strong>ID Booking</strong></td>
                                        <td>: {{ $bookings->booking_id }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0 pe-2"><strong>Kode Pembayaran</strong></td>
                                        <td>: {{ optional($pembayaran)->transaction_id ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0 pe-2"><strong>Status</strong></td>
                                        <td>: {{ ucfirst(optional($pembayaran)->status_pembayaran ?? 'pending') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0 pe-2"><strong>Batas Waktu</strong></td>
                                        <td>: -</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-muted mb-3">Detail Booking</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="ps-0 pe-2"><strong>Nama Kosan</strong></td>
                                        <td>: {{ $bookings->kosan->nama_kosan ?? 'Tidak tersedia' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0 pe-2"><strong>Durasi</strong></td>
                                        <td>: {{ $bookings->durasi_text }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0 pe-2"><strong>Check-in</strong></td>
                                        <td>: {{ $bookings->formatted_tanggal_mulai }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0 pe-2"><strong>Check-out</strong></td>
                                        <td>: {{ $bookings->formatted_tanggal_selesai }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <hr>

                        @if ($bookings->kosan)
                            <div class="d-flex align-items-center mb-4">
                                <div class="kosan-image me-3">
                                    @if ($bookings->kosan->fotoUtama)
                                        <img src="{{ asset('storage/' . $bookings->kosan->fotoUtama->path_gambar) }}"
                                            alt="{{ $bookings->kosan->nama_kosan }}" class="img-fluid rounded"
                                            style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/no-image.jpg') }}"
                                            alt="{{ $bookings->kosan->nama_kosan }}" class="img-fluid rounded"
                                            style="width: 100px; height: 100px; object-fit: cover;">
                                    @endif
                                </div>
                                <div>
                                    <h4 class="mb-1">{{ $bookings->kosan->nama_kosan }}</h4>
                                    <p class="mb-1 text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $bookings->kosan->alamat }}, {{ $bookings->kosan->kecamatan }},
                                        {{ $bookings->kosan->kota }}
                                    </p>
                                    <span
                                        class="badge {{ $bookings->kosan->jenis_kos == 'putra' ? 'bg-primary' : ($bookings->kosan->jenis_kos == 'putri' ? 'bg-danger' : 'bg-success') }}">
                                        Kos {{ ucfirst($bookings->kosan->jenis_kos) }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-muted mb-3">Rincian Pembayaran</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="ps-0 pe-2"><strong>Harga per Kamar</strong></td>
                                        <td class="text-end">{{ $bookings->formatted_harga_kamar }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0 pe-2"><strong>Durasi</strong></td>
                                        <td class="text-end">{{ $bookings->durasi_text }}</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="ps-0 pe-2"><strong>Total Pembayaran</strong></td>
                                        <td class="text-end fw-bold text-primary">
                                            {{ $bookings->formatted_corrected_total_harga }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h5 class="text-muted mb-3">Pilih Metode Pembayaran</h5>

                                <form action="{{ route('users.pembayaran.process', $bookings->booking_id) }}" method="POST"
                                    id="paymentForm">
                                    @csrf

                                    <div class="payment-methods mb-4">
                                        <!-- Pembayaran Otomatis - Midtrans -->
                                        <div class="card mb-2 payment-method-card">
                                            <div class="card-body p-3">
                                                <div class="form-check d-flex align-items-center">
                                                    <input class="form-check-input" type="radio" name="metode_pembayaran"
                                                        id="methodMidtrans" value="midtrans" checked>
                                                    <label
                                                        class="form-check-label ms-2 d-flex align-items-center justify-content-between w-100"
                                                        for="methodMidtrans">
                                                        <div>
                                                            <strong>Pembayaran Otomatis</strong>
                                                            <small class="d-block text-muted">Credit Card, Bank Transfer,
                                                                E-Wallet via Midtrans</small>
                                                        </div>
                                                        <i class="fas fa-credit-card text-primary"></i>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Pembayaran Manual -->
                                        <div class="card mb-2 payment-method-card">
                                            <div class="card-body p-3">
                                                <div class="form-check d-flex align-items-center">
                                                    <input class="form-check-input" type="radio" name="metode_pembayaran"
                                                        id="methodManual" value="manual">
                                                    <label
                                                        class="form-check-label ms-2 d-flex align-items-center justify-content-between w-100"
                                                        for="methodManual">
                                                        <div>
                                                            <strong>Pembayaran Manual</strong>
                                                            <small class="d-block text-muted">Transfer Bank & Upload Bukti
                                                                Pembayaran</small>
                                                        </div>
                                                        <i class="fas fa-university text-success"></i>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Info berdasarkan metode yang dipilih -->
                                    <div id="paymentInfo" class="alert mb-4">
                                        <div id="midtransInfo">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Pembayaran akan diproses melalui Midtrans. Anda akan diarahkan ke halaman
                                            pembayaran yang aman.
                                        </div>
                                        <div id="manualInfo" style="display: none;">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Anda akan melihat informasi rekening untuk transfer manual dan dapat upload
                                            bukti pembayaran.
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary btn-lg" id="payNowBtn">
                                            <i class="fas fa-credit-card me-2"></i> Bayar Sekarang
                                        </button>
                                        <a href="{{ route('users.bookings.show', $bookings->booking_id) }}"
                                            class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-2"></i> Kembali
                                        </a>
                                    </div>
                                </form>

                                <!-- Auto-trigger Midtrans when snap_token exists -->
                                @if (session('snap_token'))
                                    <div class="alert alert-info mt-4" id="midtransLoader">
                                        <div class="d-flex align-items-center">
                                            <div class="spinner-border spinner-border-sm me-3" role="status"></div>
                                            <div>
                                                <strong>Membuka halaman pembayaran...</strong>
                                                <small class="d-block">Jika tidak otomatis terbuka, <a href="#"
                                                        id="manual-pay">klik di sini</a></small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Midtrans Snap JS -->
                                    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
                                        data-client-key="{{ config('services.midtrans.clientKey') }}"></script>

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            // Auto-trigger payment after 2 seconds
                                            setTimeout(function() {
                                                if (typeof snap !== 'undefined') {
                                                    triggerMidtransPayment();
                                                } else {
                                                    // If snap not loaded, wait a bit more
                                                    setTimeout(triggerMidtransPayment, 2000);
                                                }
                                            }, 2000);

                                            // Manual trigger
                                            document.getElementById('manual-pay').addEventListener('click', function(e) {
                                                e.preventDefault();
                                                triggerMidtransPayment();
                                            });

                                            function triggerMidtransPayment() {
                                                if (typeof snap === 'undefined') {
                                                    alert('Sistem pembayaran belum siap. Refresh halaman dan coba lagi.');
                                                    return;
                                                }

                                                snap.pay('{{ session('snap_token') }}', {
                                                    onSuccess: function(result) {
                                                        updatePaymentStatus('paid', result.transaction_id, result.payment_type)
                                                            .finally(() => {
                                                                window.location.href =
                                                                    "{{ route('users.pembayaran.konfirmasi', $bookings->booking_id) }}";
                                                            });
                                                    },
                                                    onPending: function(result) {
                                                        updatePaymentStatus('pending', result.transaction_id, result.payment_type)
                                                            .finally(() => {
                                                                window.location.href =
                                                                    "{{ route('users.pembayaran.konfirmasi', $bookings->booking_id) }}";
                                                            });
                                                    },
                                                    onError: function(result) {
                                                        console.log('Midtrans error:', result);
                                                        // Update loader to show error
                                                        const loader = document.getElementById('midtransLoader');
                                                        loader.className = 'alert alert-danger mt-4';
                                                        loader.innerHTML = '<strong>Pembayaran gagal atau dibatalkan.</strong> Silakan klik "Bayar Sekarang" untuk mencoba lagi.';
                                                    },
                                                    onClose: function() {
                                                        // Update loader to show cancelled status
                                                        const loader = document.getElementById('midtransLoader');
                                                        loader.className = 'alert alert-warning mt-4';
                                                        loader.innerHTML = '<strong>Pembayaran dibatalkan.</strong> Anda dapat melanjutkan pembayaran dengan mengklik tombol "Bayar Sekarang" di atas.';
                                                    }
                                                });

                                                function updatePaymentStatus(status, transactionId, paymentType) {
                                                    return fetch("{{ route('users.pembayaran.update-status', $bookings->booking_id) }}", {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                        },
                                                        body: JSON.stringify({
                                                            status: status,
                                                            transaction_id: transactionId,
                                                            payment_type: paymentType
                                                        })
                                                    }).catch(error => console.log('Update status error:', error));
                                                }
                                            }
                                        });
                                    </script>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Panduan Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6 class="mb-3"><i class="fas fa-info-circle me-2 text-primary"></i> Langkah-langkah
                                Pembayaran</h6>
                            <ol class="ps-3">
                                <li class="mb-2">Pilih metode pembayaran yang diinginkan.</li>
                                <li class="mb-2">Klik tombol "Bayar Sekarang".</li>
                                <li class="mb-2">Ikuti instruksi pembayaran yang muncul.</li>
                                <li class="mb-2">Selesaikan pembayaran sesuai metode yang dipilih.</li>
                                <li class="mb-2">Setelah pembayaran berhasil, Anda akan diarahkan kembali ke halaman
                                    konfirmasi.</li>
                            </ol>
                        </div>

                        <div class="mb-4">
                            <h6 class="mb-3"><i class="fas fa-clock me-2 text-warning"></i> Batas Waktu Pembayaran</h6>
                            <p class="mb-1">Harap selesaikan pembayaran dalam waktu <strong>24 jam</strong> setelah
                                menekan tombol "Bayar Sekarang". Jika tidak, booking akan otomatis dibatalkan.</p>
                        </div>

                        <div>
                            <h6 class="mb-3"><i class="fas fa-shield-alt me-2 text-success"></i> Pembayaran Aman</h6>
                            <p class="mb-1">Semua transaksi dilindungi dengan enkripsi SSL. Data Anda 100% aman dan tidak
                                akan dibagikan kepada pihak ketiga.</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Butuh Bantuan?</h5>
                    </div>
                    <div class="card-body">
                        <p><i class="fas fa-envelope me-2"></i> Email: <a
                                href="mailto:support@kosanku.id">support@kosanku.id</a></p>
                        <p><i class="fas fa-phone me-2"></i> Telp: <a href="tel:+6281234567890">+62 812-3456-7890</a></p>
                        <p class="mb-0"><i class="fas fa-comments me-2"></i> Live Chat: <a href="#"
                                class="text-primary">Mulai Chat</a></p>
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

        /* Payment method card styling */
        .payment-method-card {
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .payment-method-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .payment-method-card .form-check-input:checked~.form-check-label {
            font-weight: 600;
        }

        .payment-method-card .form-check-input:checked+.form-check-label::after {
            content: "";
            display: inline-block;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background-color: var(--primary);
        }

        .payment-logo {
            max-height: 30px;
            object-fit: contain;
        }

        /* Badge styles */
        .badge {
            padding: 6px 12px;
            font-weight: 500;
            border-radius: 6px;
        }

        /* Table styles */
        .table-borderless td {
            padding: 6px 0;
        }

        /* Button styles */
        .btn {
            border-radius: 8px;
            padding: 8px 16px;
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

        .btn-lg {
            padding: 12px 24px;
            font-size: 1.1rem;
        }

        .btn-outline-secondary {
            color: #6c757d;
            border-color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
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

        /* Status colors */
        .text-warning {
            color: #f59f00 !important;
        }

        .text-success {
            color: #40916c !important;
        }

        .text-danger {
            color: #d9534f !important;
        }

        .text-primary {
            color: var(--primary) !important;
        }

        .bg-opacity-10 {
            --bs-bg-opacity: 0.1;
        }

        /* Gap utility for flex containers */
        .gap-2 {
            gap: 0.5rem !important;
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .d-flex.align-items-center {
                flex-direction: column;
                text-align: center;
            }

            .d-flex.align-items-center .kosan-image {
                margin-right: 0 !important;
                margin-bottom: 15px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all payment method cards
            const paymentCards = document.querySelectorAll('.payment-method-card');

            // Add click event to each payment card
            paymentCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Find the radio input inside this card and check it
                    const radioInput = this.querySelector('input[type="radio"]');
                    if (radioInput) {
                        radioInput.checked = true;
                        // Trigger change event to update info
                        radioInput.dispatchEvent(new Event('change'));
                    }
                });
            });

            // Handle form submission - show loading state
            const paymentForm = document.getElementById('paymentForm');
            const payNowBtn = document.getElementById('payNowBtn');

            if (paymentForm && payNowBtn) {
                paymentForm.addEventListener('submit', function() {
                    // Disable the button to prevent double submission
                    payNowBtn.disabled = true;
                    // Change button text to show processing
                    payNowBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
                });
            }

            // Handle payment method change
            const methodRadios = document.querySelectorAll('input[name="metode_pembayaran"]');
            const midtransInfo = document.getElementById('midtransInfo');
            const manualInfo = document.getElementById('manualInfo');

            methodRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'midtrans') {
                        midtransInfo.style.display = 'block';
                        manualInfo.style.display = 'none';
                    } else {
                        midtransInfo.style.display = 'none';
                        manualInfo.style.display = 'block';
                    }
                });
            });
        });
    </script>
@endpush
