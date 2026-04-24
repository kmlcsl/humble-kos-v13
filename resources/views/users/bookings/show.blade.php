@extends('layouts.user.app')

@section('title', 'Detail Booking #' . $bookings->booking_id)

@section('content')
    <!-- Breadcrumb -->
    <div class="container-fluid mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('users.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.bookings.index') }}">Daftar Booking</a></li>
                <li class="breadcrumb-item active" aria-current="page">Booking #{{ $bookings->booking_id }}</li>
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

        <div class="row">
            <!-- Booking Details -->
            <div class="col-lg-8 mb-4">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                        <h4 class="mb-0">Detail Booking</h4>
                        <div>
                            {!! $bookings->status_badge !!}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="text-muted mb-3">Informasi Booking</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="ps-0 pe-2"><strong>ID Booking</strong></td>
                                        <td>: {{ $bookings->booking_id }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0 pe-2"><strong>Tanggal Booking</strong></td>
                                        <td>: {{ $bookings->created_at->translatedFormat('d F Y, H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0 pe-2"><strong>Status</strong></td>
                                        <td>:
                                            {{ $bookings->status_booking == 'confirmed' ? 'Dikonfirmasi' : ($bookings->status_booking == 'cancelled' ? 'Dibatalkan' : ($bookings->status_booking == 'pending' ? 'Menunggu' : ucfirst($bookings->status_booking))) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0 pe-2"><strong>Durasi</strong></td>
                                        <td>: {{ $bookings->durasi_text }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-muted mb-3">Jadwal</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="ps-0 pe-2"><strong>Check-in</strong></td>
                                        <td>: {{ $bookings->formatted_tanggal_mulai }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0 pe-2"><strong>Check-out</strong></td>
                                        <td>: {{ $bookings->formatted_tanggal_selesai }}</td>
                                    </tr>
                                    @if ($bookings->is_active)
                                        <tr>
                                            <td class="ps-0 pe-2"><strong>Sisa Waktu</strong></td>
                                            <td>: {{ $bookings->days_remaining }} hari</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <hr>

                        @if ($bookings->kosan)
                            <div class="d-flex align-items-center mb-4">
                                <div class="kosan-image me-3">
                                    @if ($bookings->kosan->foto_kosan)
                                        <img src="{{ asset('storage/' . $bookings->kosan->foto_kosan) }}"
                                            alt="{{ $bookings->kosan->nama_kosan }}" class="img-fluid rounded"
                                            style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/no-image.jpg') }}"
                                            alt="{{ $bookings->kosan->nama_kosan }}" class="img-fluid rounded"
                                            style="width: 100px; height: 100px; object-fit: cover;">
                                    @endif
                                </div>
                                <div>
                                    @php
                                        $namaKosan = $bookings->kosan->nama_kosan ?? $bookings->kosan->nama_kosan ?? 'Kosan';
                                        $detailId = $bookings->kosan->kosan_id ?? $bookings->kosan->kosan_id ?? null;
                                    @endphp
                                    <h4 class="mb-1">{{ $namaKosan }}</h4>
                                    <p class="mb-1 text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $bookings->kosan->alamat }}, {{ $bookings->kosan->kecamatan }},
                                        {{ $bookings->kosan->kota }}
                                    </p>
                                    <span
                                        class="badge {{ $bookings->kosan->jenis_kos == 'putra' ? 'bg-primary' : ($bookings->kosan->jenis_kos == 'putri' ? 'bg-danger' : 'bg-success') }}">
                                        Kos {{ ucfirst($bookings->kosan->jenis_kos) }}
                                    </span>
                                    @if($detailId)
                                    <a href="{{ route('users.kosan.show', $detailId) }}"
                                        class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="fas fa-external-link-alt me-1"></i> Lihat Kosan
                                    </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <hr>

                        @if ($bookings->catatan)
                            <div class="mb-4">
                                <h5 class="text-muted mb-3">Catatan</h5>
                                <div class="p-3 bg-light rounded">
                                    {{ $booking->catatan }}
                                </div>
                            </div>
                            <hr>
                        @endif

                        <div class="row">
                            <!-- Perbaikan tampilan harga pada bagian Harga di show.blade.php -->
                            <div class="col-md-6">
                                <h5 class="text-muted mb-3">Harga</h5>
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
                                <h5 class="text-muted mb-3">Status Pembayaran</h5>
                                <div class="p-3 rounded
                                    @if ($bookings->status_booking == 'pending') bg-warning bg-opacity-10 text-warning
                                    @elseif($bookings->status_booking == 'confirmed') bg-success bg-opacity-10 text-success
                                    @elseif($bookings->status_booking == 'cancelled') bg-danger bg-opacity-10 text-danger
                                    @elseif($bookings->status_booking == 'selesai') bg-secondary bg-opacity-10 text-secondary
                                    @else bg-secondary bg-opacity-10 text-secondary @endif">
                                    @if ($bookings->status_booking == 'pending')
                                        <i class="fas fa-clock me-2"></i> Menunggu Konfirmasi
                                    @elseif($bookings->status_booking == 'confirmed')
                                        <i class="fas fa-check-circle me-2"></i> Pembayaran Dikonfirmasi
                                    @elseif($bookings->status_booking == 'cancelled')
                                        <i class="fas fa-times-circle me-2"></i> Booking Dibatalkan
                                    @elseif($bookings->status_booking == 'selesai')
                                        <i class="fas fa-check-double me-2"></i> Booking Selesai
                                    @endif
                                </div>

                                @if ($bookings->status_booking == 'pending')
                                    @php
                                        $pembayaranCheck = \App\Models\Pembayaran::where('booking_id', $bookings->booking_id)
                                            ->latest()
                                            ->first();
                                    @endphp

                                    @if ($pembayaranCheck && $pembayaranCheck->status_pembayaran === 'pending')
                                        <div class="alert alert-warning mt-3">
                                            <small>
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Pembayaran Belum Selesai!</strong><br>
                                                Booking Anda belum aktif karena pembayaran belum selesai. Klik tombol "Lanjutkan Pembayaran" di bawah untuk menyelesaikan pembayaran Anda.
                                            </small>
                                        </div>
                                    @elseif ($pembayaranCheck && $pembayaranCheck->tipe_pembayaran === 'manual' && $pembayaranCheck->bukti_transfer)
                                        <div class="alert alert-info mt-3">
                                            <small>
                                                <i class="fas fa-clock me-2"></i>
                                                Bukti pembayaran Anda sedang diverifikasi oleh admin. Booking akan otomatis dikonfirmasi setelah pembayaran disetujui.
                                            </small>
                                        </div>
                                    @else
                                        <div class="alert alert-info mt-3">
                                            <small>
                                                <i class="fas fa-info-circle me-2"></i>
                                                Silakan selesaikan pembayaran untuk mengaktifkan booking Anda.
                                            </small>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-muted mb-3">Aksi</h5>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('users.bookings.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                            </a>

                            @if ($bookings->can_be_canceled)
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#cancelModal">
                                    <i class="fas fa-times me-1"></i> Batalkan Booking
                                </button>
                            @endif

                            @if ($bookings->can_be_extended)
                                <a href="{{ route('users.bookings.extend-form', $bookings->booking_id) }}"
                                    class="btn btn-outline-primary">
                                    <i class="fas fa-calendar-plus me-1"></i> Perpanjang Booking
                                </a>
                            @endif

                            <!-- Di show.blade.php pada bagian action buttons -->
                            @if ($bookings->status_booking == 'pending')
                                @php
                                    $pembayaran = \App\Models\Pembayaran::where('booking_id', $bookings->booking_id)
                                        ->whereIn('status_pembayaran', ['pending'])
                                        ->latest()
                                        ->first();
                                @endphp

                                @if ($pembayaran)
                                    {{-- Jika sudah ada pembayaran pending, tunjukkan tombol untuk lanjutkan --}}
                                    @if ($pembayaran->tipe_pembayaran === 'manual' && $pembayaran->bukti_transfer)
                                        {{-- Jika manual dan sudah upload bukti, arahkan ke halaman konfirmasi --}}
                                        <a href="{{ route('users.pembayaran.konfirmasi', $bookings->booking_id) }}"
                                            class="btn btn-info">
                                            <i class="fas fa-info-circle me-1"></i> Lihat Status Pembayaran
                                        </a>
                                    @else
                                        {{-- Jika belum bayar atau belum upload bukti, arahkan ke halaman pembayaran --}}
                                        <a href="{{ route('users.pembayaran.index', $bookings->booking_id) }}"
                                            class="btn btn-warning">
                                            <i class="fas fa-credit-card me-1"></i> Lanjutkan Pembayaran
                                        </a>
                                    @endif
                                @else
                                    {{-- Jika belum ada pembayaran sama sekali, arahkan langsung ke halaman pembayaran --}}
                                    <a href="{{ route('users.pembayaran.index', $bookings->booking_id) }}"
                                        class="btn btn-primary">
                                        <i class="fas fa-credit-card me-1"></i> Bayar Sekarang
                                    </a>
                                @endif
                            @endif

                            @if ($bookings->can_be_completed)
                                <form action="{{ route('users.bookings.complete', $bookings->booking_id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check-circle me-1"></i> Selesaikan Booking
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-4">
                <!-- Contact Information -->
                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Informasi Kontak</h5>
                    </div>
                    <div class="card-body">
                        @if ($bookings->kosan && $bookings->kosan->pemilik)
                            <div class="d-flex align-items-center mb-3">
                                <div class="owner-avatar me-3">
                                @php
                                    $ownerPhoto = optional($bookings->kosan->pemilik)->foto_profil
                                    ? asset(path: 'storage/' . $bookings->kosan->pemilik->foto_profil)
                                    : asset(path: 'images/user-avatar.png');
                                @endphp
                                    <img src="{{ $ownerPhoto }}" alt="Owner"
                                        class="rounded-circle" width="80">
                                    {{-- <img src="{{ asset('images/user-avatar.png') }}" alt="Pemilik"
                                        class="rounded-circle" width="60"> --}}
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $bookings->kosan->pemilik->name }}</h6>
                                    <p class="text-muted mb-0"><small>Pemilik Kosan</small></p>
                                </div>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <p class="mb-2"><i class="fas fa-phone-alt me-2"></i>
                                    {{ $bookings->kosan->pemilik->no_telepon ?? 'Tidak tersedia' }}</p>
                                <p class="mb-2"><i class="fas fa-envelope me-2"></i>
                                    {{ $bookings->kosan->pemilik->email }}</p>
                            </div>

                            <div class="d-grid">
                                @php
                                    $rawPhone = $bookings->kosan->pemilik->no_telepon ?? '';
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
                                    // Jika nomor mulai dengan 0, ubah jadi 62
                                    if (strpos($cleanPhone, '0') === 0) {
                                        $cleanPhone = '62' . substr($cleanPhone, 1);
                                    }

                                    $status = $bookings->status_booking;
                                    if ($status === 'confirmed') {
                                        $message =
                                            'Halo Bapak/Ibu ' .
                                            $bookings->kosan->pemilik->name .
                                            ', saya ' .
                                            Auth::user()->name .
                                            '. Saya telah menyelesaikan pembayaran booking di ' .
                                            $bookings->kosan->nama_kosan .
                                            ' dengan ID Booking #' .
                                            $bookings->booking_id .
                                            '. Mohon informasi selanjutnya untuk proses masuk kos. Terima kasih.';
                                    } else {
                                        $message =
                                            'Halo Bapak/Ibu ' .
                                            $bookings->kosan->pemilik->name .
                                            ', saya ' .
                                            Auth::user()->name .
                                            '. Saya baru saja melakukan booking di ' .
                                            $bookings->kosan->nama_kosan .
                                            ' dengan ID Booking #' .
                                            $bookings->booking_id .
                                            '. Mohon konfirmasinya. Terima kasih.';
                                    }

                                    $waUrl = 'https://wa.me/' . $cleanPhone . '?text=' . urlencode($message);
                                @endphp
                                <a href="{{ $waUrl }}" class="btn btn-success" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-whatsapp me-2"></i> Hubungi via WhatsApp
                                </a>
                            </div>
                        @else
                            <div class="text-center text-muted py-3">
                                <p>Informasi kontak tidak tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Important Information -->
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Informasi Penting</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="mb-2"><i class="fas fa-clock me-2"></i> Jam Check-in</h6>
                            <p class="ms-4 text-muted">12.00 - 18.00 WIB</p>
                        </div>

                        <div class="mb-3">
                            <h6 class="mb-2"><i class="fas fa-clock me-2"></i> Jam Check-out</h6>
                            <p class="ms-4 text-muted">Maksimal 12.00 WIB</p>
                        </div>

                        <div class="mb-3">
                            <h6 class="mb-2"><i class="fas fa-file-alt me-2"></i> Kebijakan Pembatalan</h6>
                            <ul class="ms-4 text-muted small">
                                <li>Pembatalan 7 hari atau lebih sebelum check-in: pengembalian dana 90%</li>
                                <li>Pembatalan 3-7 hari sebelum check-in: pengembalian dana 50%</li>
                                <li>Pembatalan kurang dari 3 hari sebelum check-in: tidak ada pengembalian dana</li>
                            </ul>
                        </div>

                        <div>
                            <h6 class="mb-2"><i class="fas fa-exclamation-circle me-2"></i> Peraturan Kosan</h6>
                            <p class="ms-4 text-muted small">Penghuni wajib mematuhi peraturan kosan yang ditetapkan oleh
                                pemilik. Jika ada pertanyaan, silakan hubungi pemilik kosan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    @if ($bookings->can_be_canceled)
        <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelModalLabel">Konfirmasi Pembatalan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> Pembatalan booking mungkin dikenakan biaya
                            sesuai dengan kebijakan pembatalan.
                        </div>
                        <p>Apakah Anda yakin ingin membatalkan booking ini?</p>
                        <p class="text-danger"><strong>Tindakan ini tidak dapat dibatalkan.</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <form action="{{ route('users.bookings.cancel', $bookings->booking_id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-danger">Batalkan Booking</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
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

        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .btn-success {
            background-color: #198754;
            border-color: #198754;
        }

        .btn-success:hover {
            background-color: #157347;
            border-color: #146c43;
            transform: translateY(-2px);
        }

        .btn-outline-secondary,
        .btn-outline-danger {
            transition: all 0.3s;
        }

        .btn-outline-secondary:hover,
        .btn-outline-danger:hover {
            transform: translateY(-2px);
        }

        /* Avatar styles */
        .owner-avatar img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .rounded-circle {
            border: 1px solid #f0f0f0;
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

        .bg-opacity-10 {
            --bs-bg-opacity: 0.1;
        }

        /* Gap utility for flex containers */
        .gap-2 {
            gap: 0.5rem !important;
        }

        /* Modal styles */
        .modal-content {
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }

        .modal-header {
            background-color: var(--secondary);
            border-bottom: 1px solid #f0f0f0;
        }

        .modal-footer {
            border-top: 1px solid #f0f0f0;
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .d-flex.flex-wrap.gap-2 {
                flex-direction: column;
                width: 100%;
            }

            .d-flex.flex-wrap.gap-2 .btn,
            .d-flex.flex-wrap.gap-2 form {
                width: 100%;
                margin-bottom: 8px;
            }

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
            // Initialize any tooltips
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            // Set up cancel booking confirmation
            const cancelButton = document.querySelector('[data-bs-target="#cancelModal"]');
            if (cancelButton) {
                cancelButton.addEventListener('click', function() {
                    // Additional logic can be added here if needed
                    console.log('Opening cancel modal for booking');
                });
            }

            // Handle form submissions to prevent accidental double submissions
            const forms = document.querySelectorAll('form');
            forms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    // Get the submit button
                    const submitButton = this.querySelector('button[type="submit"]');

                    if (submitButton && !submitButton.disabled) {
                        // Disable the button and change text to show processing
                        submitButton.disabled = true;

                        // Store original button text
                        const originalText = submitButton.innerHTML;

                        // Update button text to show loading state
                        if (submitButton.classList.contains('btn-danger')) {
                            // For cancel button
                            submitButton.innerHTML =
                                '<i class="fas fa-spinner fa-spin me-1"></i> Membatalkan...';
                        } else if (submitButton.classList.contains('btn-primary')) {
                            // For payment button
                            submitButton.innerHTML =
                                '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';
                        } else if (submitButton.classList.contains('btn-success')) {
                            // For complete button
                            submitButton.innerHTML =
                                '<i class="fas fa-spinner fa-spin me-1"></i> Menyelesaikan...';
                        }

                        // Enable the button after 3 seconds if the form hasn't been submitted yet
                        // This is a fallback in case the form submission is interrupted
                        setTimeout(function() {
                            if (document.body.contains(submitButton)) {
                                submitButton.disabled = false;
                                submitButton.innerHTML = originalText;
                            }
                        }, 3000);
                    }
                });
            });

            // Add confirmation for certain actions
            const confirmActionButtons = document.querySelectorAll('[data-confirm="true"]');
            confirmActionButtons.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    if (!confirm('Apakah Anda yakin ingin melakukan tindakan ini?')) {
                        e.preventDefault();
                    }
                });
            });

            // WhatsApp link handler - format the phone number if needed
            const whatsappLink = document.querySelector('a[href^="https://wa.me/"]');
            if (whatsappLink) {
                whatsappLink.addEventListener('click', function(e) {
                    const phoneNumber = this.getAttribute('href').replace('https://wa.me/', '');

                    // If phone number is empty or invalid, show an alert and prevent default action
                    if (!phoneNumber || phoneNumber === '') {
                        e.preventDefault();
                        alert(
                            'Nomor telepon pemilik kosan tidak tersedia. Silakan gunakan metode kontak lain.'
                        );
                    }
                });
            }
        });
    </script>
@endpush
