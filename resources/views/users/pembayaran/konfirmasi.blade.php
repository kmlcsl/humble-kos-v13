@extends('layouts.user.app')

@section('title', 'Konfirmasi Pembayaran #' . $bookings->booking_id)

@section('content')
    <!-- Breadcrumb -->
    <div class="container-fluid mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('users.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.bookings.index') }}">Daftar Booking</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.bookings.show', $bookings->booking_id) }}">Booking
                        #{{ $bookings->booking_id }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.pembayaran.index', $bookings->booking_id) }}">Pembayaran</a></li>
                <li class="breadcrumb-item active" aria-current="page">Konfirmasi</li>
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

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body text-center p-5">
                        @if ($pembayaran->status_pembayaran == 'paid')
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="mb-3">Pembayaran Berhasil!</h2>
                            <p class="lead mb-4">Terima kasih, pembayaran Anda telah kami terima dan booking telah
                                dikonfirmasi.</p>

                            @if ($pembayaran->tipe_pembayaran === 'gateway')
                                <div class="alert alert-success mb-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Pembayaran Otomatis Berhasil!</strong> Transaksi telah diverifikasi secara
                                    otomatis oleh sistem Midtrans.
                                </div>
                            @endif
                        @elseif($pembayaran->status_pembayaran == 'processing')
                            @if ($pembayaran->tipe_pembayaran === 'gateway')
                                <!-- Midtrans Processing (tunggu callback) -->
                                <div class="mb-4">
                                    <i class="fas fa-credit-card text-info" style="font-size: 5rem;"></i>
                                </div>
                                <h2 class="mb-3">Memproses Pembayaran</h2>
                                <p class="lead mb-4">Pembayaran Anda sedang diproses oleh Midtrans. Mohon tunggu sebentar...
                                </p>

                                <div class="alert alert-info mb-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Sedang Memverifikasi:</strong> Sistem sedang memverifikasi pembayaran Anda.
                                    Halaman ini akan otomatis diperbarui saat verifikasi selesai.
                                </div>

                                <div class="text-center mb-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Memverifikasi pembayaran...</p>
                                </div>
                            @else
                                <!-- Manual Payment Processing -->
                                <div class="mb-4">
                                    <i class="fas fa-clock text-warning" style="font-size: 5rem;"></i>
                                </div>
                                <h2 class="mb-3">Menunggu Verifikasi Admin</h2>
                                <p class="lead mb-4">Bukti pembayaran Anda sedang diverifikasi oleh admin.</p>

                                <div class="alert alert-info mb-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Informasi Penting:</strong> Pembayaran Anda akan diverifikasi dalam waktu 24 jam
                                    kerja. Kami akan memberi tahu Anda melalui email ketika pembayaran telah dikonfirmasi.
                                </div>

                            @endif
                        @elseif($pembayaran->status_pembayaran == 'failed')
                            <div class="mb-4">
                                <i class="fas fa-times-circle text-danger" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="mb-3">Pembayaran Gagal</h2>
                            <p class="lead mb-4">Maaf, pembayaran Anda tidak dapat diproses.</p>

                            @if ($pembayaran->tipe_pembayaran === 'gateway')
                                <div class="alert alert-warning mb-4">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Pembayaran melalui Midtrans gagal. Silakan coba lagi dengan metode pembayaran yang
                                    berbeda.
                                </div>
                            @endif
                        @elseif($pembayaran->status_pembayaran == 'expired')
                            <div class="mb-4">
                                <i class="fas fa-exclamation-circle text-secondary" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="mb-3">Pembayaran Kadaluarsa</h2>
                            <p class="lead mb-4">Batas waktu pembayaran telah berakhir.</p>
                        @else
                            <div class="mb-4">
                                <i class="fas fa-question-circle text-info" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="mb-3">Status Pembayaran</h2>
                            <p class="lead mb-4">Status pembayaran Anda: {{ ucfirst($pembayaran->status_pembayaran ?? 'pending') }}</p>
                        @endif

                        <!-- Detail Pembayaran -->
                        <div class="payment-details p-4 bg-light rounded mb-4 text-start">
                            <h5 class="mb-3">Detail Pembayaran</h5>
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td style="width: 40%"><strong>ID Booking</strong></td>
                                    <td>: {{ $bookings->booking_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kode Pembayaran</strong></td>
                                    <td>: {{ $pembayaran->transaction_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Metode Pembayaran</strong></td>
                                    <td>:
                                        {{ $pembayaran->tipe_pembayaran === 'gateway' ? 'Pembayaran Otomatis (Midtrans)' : 'Transfer Manual' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Jumlah</strong></td>
                                    <td>: {{ $pembayaran->formatted_jumlah }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>: {!! $pembayaran->status_badge !!}</td>
                                </tr>
                                @if ($pembayaran->tanggal_bayar)
                                    <tr>
                                        <td><strong>Waktu Pembayaran</strong></td>
                                        <td>: {{ $pembayaran->formatted_payment_time }}</td>
                                    </tr>
                                @endif
                                @if ($pembayaran->no_referensi)
                                    <tr>
                                        <td><strong>No. Referensi</strong></td>
                                        <td>: {{ $pembayaran->no_referensi }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>

                        <!-- Bukti Pembayaran Manual -->
                        @if ($pembayaran->tipe_pembayaran === 'manual' && $pembayaran->bukti_transfer)
                            <div class="bukti-pembayaran p-4 bg-light rounded mb-4">
                                <h5 class="mb-3">Bukti Pembayaran yang Diupload</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>File Bukti:</strong></p>
                                        <p class="text-muted small">Bukti transfer telah diupload dan menunggu verifikasi admin.</p>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="{{ asset('storage/' . $pembayaran->bukti_transfer) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-2"></i> Lihat Bukti Transfer
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            @if ($pembayaran->status_pembayaran == 'paid')
                                <a href="{{ route('users.bookings.show', $bookings->booking_id) }}" class="btn btn-success btn-lg">
                                    <i class="fas fa-home me-2"></i> Lihat Detail Booking
                                </a>
                            @elseif($pembayaran->status_pembayaran == 'pending')
                                @if ($pembayaran->tipe_pembayaran === 'gateway')
                                    <button id="btnCheckStatus" class="btn btn-primary btn-lg">
                                        <i class="fas fa-sync-alt me-2" id="iconCheckStatus"></i> Cek Status Pembayaran
                                    </button>
                                @else
                                    <a href="{{ route('users.pembayaran.index', $bookings->booking_id) }}"
                                        class="btn btn-outline-secondary">
                                        <i class="fas fa-credit-card me-2"></i> Ubah Metode Pembayaran
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('users.pembayaran.index', $bookings->booking_id) }}" class="btn btn-primary">
                                    <i class="fas fa-redo me-2"></i> Coba Lagi
                                </a>
                            @endif

                            <a href="{{ route('users.bookings.show', $bookings->booking_id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Kembali ke Detail Booking
                            </a>
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

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .countdown-timer {
            font-size: 1.5rem;
            font-weight: 700;
            font-family: monospace;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

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

        .btn-success:hover {
            transform: translateY(-2px);
        }

        .btn-lg {
            padding: 12px 24px;
            font-size: 1.1rem;
        }

        .text-warning {
            color: #f59f00 !important;
        }

        .text-success {
            color: #40916c !important;
        }

        .text-danger {
            color: #d9534f !important;
        }

        .payment-details table td {
            padding: 8px 0;
        }

        @media (max-width: 767.98px) {
            .card-body {
                padding: 1.5rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pembayaranStatus = '{{ $pembayaran->status_pembayaran }}';
            const tipePembayaran = '{{ $pembayaran->tipe_pembayaran }}';
            const btnCheckStatus = document.getElementById('btnCheckStatus');
            const iconCheckStatus = document.getElementById('iconCheckStatus');

            function checkPaymentStatus(isManual = false) {
                if (isManual && btnCheckStatus) {
                    btnCheckStatus.disabled = true;
                    iconCheckStatus.classList.add('fa-spin');
                }

                return fetch("{{ route('users.pembayaran.check-status', $bookings->booking_id) }}")
                    .then(response => response.json())
                    .then(data => {
                        if (data.status_pembayaran === 'paid' || data.status_pembayaran === 'failed') {
                            window.location.reload();
                            return true;
                        }
                        return false;
                    })
                    .catch(error => {
                        console.log('Status check error:', error);
                        return false;
                    })
                    .finally(() => {
                        if (isManual && btnCheckStatus) {
                            btnCheckStatus.disabled = false;
                            iconCheckStatus.classList.remove('fa-spin');
                        }
                    });
            }

            // Auto-refresh untuk Midtrans yang pending
            if (pembayaranStatus === 'pending' && tipePembayaran === 'gateway') {
                // Check status setiap 2 detik secara otomatis
                const checkInterval = setInterval(function() {
                    checkPaymentStatus(false).then(isDone => {
                        if (isDone) clearInterval(checkInterval);
                    });
                }, 2000);

                // Manual check button
                if (btnCheckStatus) {
                    btnCheckStatus.addEventListener('click', function() {
                        checkPaymentStatus(true);
                    });
                }

                // Stop automatic checking after 5 minutes
                setTimeout(function() {
                    clearInterval(checkInterval);
                }, 300000);
            }
        });
    </script>
@endpush
