@extends('layouts.admin.app')

@section('title', 'Detail Pembayaran #' . $pembayaran->pembayaran_id)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.pembayaran.index') }}">Manajemen Pembayaran</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail Pembayaran</li>
@endsection

@section('page-title', 'Detail Pembayaran #' . $pembayaran->pembayaran_id)

@section('page-actions')
    <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>

    @if ($pembayaran->status_pembayaran == 'pending' && $pembayaran->is_manual)
        <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#approveModalDetail">
            <i class="fas fa-check-circle me-1"></i> Setujui
        </button>
        <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#rejectModalDetail">
            <i class="fas fa-times-circle me-1"></i> Tolak
        </button>
    @endif
@endsection

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <!-- Pembayaran Details Card -->
                <div class="admin-pemilik-card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Informasi Pembayaran</h5>
                            <div>
                                {!! $pembayaran->status_badge !!}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="text-muted" width="180">ID Pembayaran</td>
                                        <td class="fw-medium">{{ $pembayaran->pembayaran_id }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Kode Transaksi</td>
                                        <td class="fw-medium">{{ $pembayaran->transaction_id ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Status</td>
                                        <td class="fw-medium">
                                            {!! $pembayaran->status_badge !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Metode Pembayaran</td>
                                        <td class="fw-medium">{{ $pembayaran->method_display_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Jumlah</td>
                                        <td class="fw-medium">{{ $pembayaran->formatted_jumlah }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="text-muted" width="180">Tanggal Dibuat</td>
                                        <td class="fw-medium">
                                            {{ $pembayaran->created_at->format('d M Y, H:i') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Waktu Kadaluarsa</td>
                                        <td class="fw-medium">
                                            {{ $pembayaran->formatted_expiry }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Waktu Pembayaran</td>
                                        <td class="fw-medium">
                                            {{ $pembayaran->formatted_payment_time }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">No. Referensi</td>
                                        <td class="fw-medium">{{ $pembayaran->transaction_id ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <hr>

                        @if ($pembayaran->status_pembayaran == 'pending' && $pembayaran->is_manual)
                            <div class="alert alert-info mb-4">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="fas fa-info-circle fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="alert-heading">Pembayaran Menunggu Verifikasi</h5>
                                        <p class="mb-0">Pembayaran ini adalah pembayaran manual dan sedang menunggu
                                            verifikasi admin. Silakan periksa bukti pembayaran dan verifikasi pembayaran
                                            ini.</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($pembayaran->bukti_transfer)
                            <div class="admin-pemilik-card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">Bukti Transfer</h5>
                                </div>
                                <div class="card-body text-center">
                                    <img src="{{ asset('storage/' . $pembayaran->bukti_transfer) }}" alt="Bukti Transfer"
                                        class="img-fluid rounded" style="max-height: 500px;">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Booking Details Card -->
                @if ($pembayaran->booking)
                    <div class="admin-pemilik-card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">Detail Booking</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        @if ($pembayaran->booking->kosan && $pembayaran->booking->kosan->fotoUtama)
                                            <img src="{{ asset('storage/' . $pembayaran->booking->kosan->fotoUtama->path_gambar) }}"
                                                alt="Kosan" class="img-fluid rounded"
                                                style="width: 100px; height: 100px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                style="width: 100px; height: 100px;">
                                                <i class="fas fa-home fa-2x text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h5 class="mb-1">
                                            {{ $pembayaran->booking->kosan->nama_kosan ?? 'Kosan tidak tersedia' }}</h5>
                                        <p class="mb-1 text-muted">
                                            @if ($pembayaran->booking->kosan)
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ $pembayaran->booking->kosan->alamat ?? '' }},
                                                {{ $pembayaran->booking->kosan->kecamatan ?? '' }},
                                                {{ $pembayaran->booking->kosan->kota ?? '' }}
                                            @else
                                                Data kosan tidak tersedia
                                            @endif
                                        </p>
                                        @if ($pembayaran->booking->kosan)
                                            <span
                                                class="badge bg-primary">{{ ucfirst($pembayaran->booking->kosan->jenis_kos ?? 'Unknown') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-3 text-muted">Informasi Booking</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="ps-0 pe-2"><strong>ID Booking</strong></td>
                                            <td>: {{ $pembayaran->booking->booking_id }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 pe-2"><strong>Tanggal Booking</strong></td>
                                            <td>: {{ $pembayaran->booking->created_at->format('d M Y, H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 pe-2"><strong>Status</strong></td>
                                            <td>: {{ ucfirst($pembayaran->booking->status_booking) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 pe-2"><strong>Durasi</strong></td>
                                            <td>: {{ $pembayaran->booking->durasi_text ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-3 text-muted">Jadwal</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="ps-0 pe-2"><strong>Check-in</strong></td>
                                            <td>: {{ \Carbon\Carbon::parse($pembayaran->booking->tanggal_mulai)->format('d M Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 pe-2"><strong>Check-out</strong></td>
                                            <td>: {{ \Carbon\Carbon::parse($pembayaran->booking->tanggal_selesai)->format('d M Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <!-- User Information Card -->
                @if ($pembayaran->booking && $pembayaran->booking->pengguna)
                    <div class="admin-pemilik-card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">Informasi Pengguna</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    @if ($pembayaran->booking->pengguna->foto_profil)
                                        <img src="{{ $pembayaran->booking->pengguna->profile_photo_url }}"
                                            alt="{{ $pembayaran->booking->pengguna->name }}" class="rounded-circle"
                                            style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 60px; height: 60px; font-size: 24px;">
                                            {{ substr($pembayaran->booking->pengguna->name ?? 'U', 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="mb-1">{{ $pembayaran->booking->pengguna->name }}</h5>
                                    <p class="text-muted mb-0">
                                        <small>Pengguna</small>
                                    </p>
                                </div>
                            </div>
                            <hr>
                            <p class="mb-2">
                                <i class="fas fa-envelope me-2 text-muted"></i>
                                {{ $pembayaran->booking->email ?? 'Tidak tersedia (booking lama)' }}
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-phone me-2 text-muted"></i>
                                {{ $pembayaran->booking->pengguna->no_hp ?? 'Tidak tersedia' }}
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Payment Timeline Card -->
                <div class="admin-pemilik-card">
                    <div class="card-header">
                        <h5 class="card-title">Timeline Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item pb-3">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-0">Pembayaran Dibuat</h6>
                                    <p class="small text-muted mb-0">
                                        {{ $pembayaran->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>

                            @if ($pembayaran->is_successful || $pembayaran->status_pembayaran == 'failed')
                                <div class="timeline-item pb-3">
                                    <div class="timeline-marker {{ $pembayaran->is_successful ? 'bg-success' : 'bg-danger' }}">
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="mb-0">
                                            {{ $pembayaran->is_successful ? 'Pembayaran Berhasil' : 'Pembayaran Gagal' }}
                                        </h6>
                                        <p class="small text-muted mb-0">
                                            {{ $pembayaran->updated_at->format('d M Y, H:i') }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            @if ($pembayaran->is_successful)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-0">Booking Dikonfirmasi</h6>
                                        <p class="small text-muted mb-0">
                                            {{ $pembayaran->formatted_payment_time }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Persetujuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyetujui pembayaran ini?</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Pembayaran akan diubah statusnya menjadi "Sukses" dan
                        booking akan dikonfirmasi.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('admin.pembayaran.approve', $pembayaran->pembayaran_id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">Setujui</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Penolakan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menolak pembayaran ini?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> Pembayaran akan diubah statusnya menjadi "Gagal".
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('admin.pembayaran.reject', $pembayaran->pembayaran_id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">Tolak</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Timeline styles */
        .timeline {
            position: relative;
            padding-left: 1.5rem;
        }

        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            left: 7px;
            height: 100%;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }

        .timeline-marker {
            position: absolute;
            left: -1.5rem;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            top: 4px;
        }

        .timeline-content {
            padding-left: 0.5rem;
        }
    </style>
@endpush