@extends('layouts.user.app')

@section('title', 'Booking Kosan - ' . $kosan->nama_kosan)

@section('content')
    <!-- Breadcrumb -->
    <div class="container-fluid mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('users.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.kosan.index') }}">Daftar Kosan</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.kosan.show', $kosan->kosan_id) }}">{{ $kosan->nama_kosan }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Booking</li>
            </ol>
        </nav>
    </div>

    <!-- Booking Form -->
    <div class="container-fluid mb-5">
        <div class="row">
            <!-- Booking Form Left Side -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Form Booking Kosan</h2>

                        <form action="{{ route('users.kosan.process-booking', $kosan->kosan_id) }}" method="POST"
                            id="bookingForm">
                            @csrf

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

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <!-- Data Booking Section -->
                            <div class="booking-section mb-4">
                                <h4 class="section-title">Data Booking</h4>
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        {{-- Row 1: Durasi and Jumlah Kamar --}}
                                        {{-- Row 1: Durasi, Custom Quantity, Jumlah Kamar --}}
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="jenis_durasi" class="form-label">Durasi <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select @error('jenis_durasi') is-invalid @enderror"
                                                    id="jenis_durasi" name="jenis_durasi" required>
                                                    <option value="harian" data-value="1"
                                                        data-price="{{ (float) $kosan->getHargaSetelahDiskonAttribute() / 30 }}"
                                                        {{ old('jenis_durasi', request('durasi', 'bulanan')) == 'harian' ? 'selected' : '' }}>
                                                        Harian (custom)</option>
                                                    <option value="mingguan" data-value="1"
                                                        data-price="{{ (float) $kosan->getHargaSetelahDiskonAttribute() / 4 }}"
                                                        {{ old('jenis_durasi', request('durasi', 'bulanan')) == 'mingguan' ? 'selected' : '' }}>
                                                        Mingguan (custom)</option>
                                                    <option value="bulanan" data-value="1"
                                                        data-price="{{ $kosan->getHargaSetelahDiskonAttribute() }}"
                                                        {{ old('jenis_durasi', request('durasi', 'bulanan')) == 'bulanan' ? 'selected' : '' }}>
                                                        Bulanan (custom)</option>
                                                    @if ($kosan->harga_tiga_bulan)
                                                        <option value="tiga_bulan" data-value="3"
                                                            data-price="{{ $kosan->harga_tiga_bulan }}"
                                                            {{ old('jenis_durasi', request('durasi')) == 'tiga_bulan' ? 'selected' : '' }}>
                                                            Paket 3 Bulan</option>
                                                    @endif
                                                    @if ($kosan->harga_semester)
                                                        <option value="semester" data-value="6"
                                                            data-price="{{ $kosan->harga_semester }}"
                                                            {{ old('jenis_durasi', request('durasi')) == 'semester' ? 'selected' : '' }}>
                                                            Paket 6 Bulan</option>
                                                    @endif
                                                    <option value="tahunan" data-value="12"
                                                        data-price="{{ $kosan->getHargaTahunanSetelahDiskonAttribute() }}"
                                                        {{ old('jenis_durasi', request('durasi')) == 'tahunan' ? 'selected' : '' }}>
                                                        Tahunan (custom)</option>
                                                </select>
                                                <input type="hidden" name="nilai_durasi" id="nilai_durasi"
                                                    value="{{ old('nilai_durasi', request('nilai_durasi', 1)) }}">
                                                @error('jenis_durasi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                {{-- Custom duration inputs, now integrated into the column --}}
                                                <div id="input_harian_custom_col" style="display:none;">
                                                    <label for="hari_custom" class="form-label">Jumlah Hari (≥1)</label>
                                                    <input type="number" class="form-control" id="hari_custom" min="1" value="{{ old('hari_custom', 1) }}">
                                                </div>
                                                <div id="input_mingguan_custom_col" style="display:none;">
                                                    <label for="minggu_custom" class="form-label">Jumlah Minggu (≥1)</label>
                                                    <input type="number" class="form-control" id="minggu_custom" min="1" value="{{ old('minggu_custom', 1) }}">
                                                </div>
                                                <div id="input_bulanan_custom_col" style="display:none;">
                                                    <label for="bulan_custom" class="form-label">Jumlah Bulan (1–11)</label>
                                                    <input type="number" class="form-control" id="bulan_custom" min="1" max="11" value="{{ old('bulan_custom', 1) }}">
                                                </div>
                                                <div id="input_tahunan_custom_col" style="display:none;">
                                                    <label for="tahun_custom" class="form-label">Jumlah Tahun (≥1)</label>
                                                    <input type="number" class="form-control" id="tahun_custom" min="1" value="{{ old('tahun_custom', 1) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Jumlah Kamar</label>
                                                <div class="form-control" readonly>
                                                    {{ old('jumlah_kamar', request('jumlah_kamar', 1)) }} Kamar
                                                </div>
                                                <input type="hidden" id="jumlah_kamar_hidden" name="jumlah_kamar" value="{{ old('jumlah_kamar', request('jumlah_kamar', 1)) }}">
                                            </div>
                                            <input type="hidden" name="kamar_id" value="{{ old('kamar_id', request('kamar_id')) }}">
                                        </div>

                                        {{-- Row 2: Tanggal Mulai and Tanggal Selesai --}}
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span
                                                        class="text-danger">*</span></label>
                                                <input type="date"
                                                    class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                                    id="tanggal_mulai" name="tanggal_mulai"
                                                    value="{{ old('tanggal_mulai', request('tanggal_mulai', date('Y-m-d'))) }}"
                                                    min="{{ date('Y-m-d') }}" required>
                                                @error('tanggal_mulai')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                                <input type="date" class="form-control" id="tanggal_selesai"
                                                    name="tanggal_selesai" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Pemesan Section -->
                            <div class="booking-section mb-4">
                                <h4 class="section-title">Data Pemesan</h4>
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="nama" class="form-label">Nama Lengkap <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('nama') is-invalid @enderror" id="nama"
                                                    name="nama" value="{{ old('nama', auth()->user()->name) }}"
                                                    required>
                                                @error('nama')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="telepon" class="form-label">No. Telepon <span
                                                        class="text-danger">*</span></label>
                                                <input type="tel"
                                                    class="form-control @error('telepon') is-invalid @enderror"
                                                    id="telepon" name="telepon"
                                                    value="{{ old('telepon', auth()->user()->no_hp ?? '') }}" required>
                                                @error('telepon')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email"
                                                class="form-control @error('email') is-invalid @enderror" id="email"
                                                name="email" value="{{ old('email', auth()->user()->email) }}"
                                                required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="catatan" class="form-label">Catatan Tambahan</label>
                                            <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="3">{{ old('catatan') }}</textarea>
                                            @error('catatan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Agreement and Terms Section -->
                            <div class="booking-section mb-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input @error('setuju') is-invalid @enderror"
                                                type="checkbox" id="setuju" name="setuju" required>
                                            <label class="form-check-label" for="setuju">
                                                Saya menyetujui <a href="#" data-bs-toggle="modal"
                                                    data-bs-target="#termsModal">syarat dan ketentuan</a> berlaku
                                            </label>
                                            @error('setuju')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i> Setelah mengirimkan formulir ini,
                                            permintaan booking Anda akan diproses oleh pemilik kosan dalam 24 jam. Anda akan
                                            menerima notifikasi untuk konfirmasi pembayaran.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('users.kosan.show', $kosan->kosan_id) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check-circle me-1"></i> Konfirmasi Booking
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Booking Summary Right Side -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 90px;">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Ringkasan Booking</h4>
                        @php
                            $monthly = (float) ($monthlyRoomPrice ?? (float) $kosan->getHargaSetelahDiskonAttribute());
                            $mode = $durasi ?? request('durasi', 'bulanan');
                            $nilai = (int) ($nilaiDurasi ?? request('nilai_durasi', 1));
                            $roomsInit = (int) ($jumlahKamar ?? request('jumlah_kamar', 1));
                            $durationTextInit = '1 Bulan';
                            if ($mode === 'harian') {
                                $durationTextInit = max(1, $nilai) . ' Hari';
                            } elseif ($mode === 'mingguan') {
                                $durationTextInit = max(1, $nilai) . ' Minggu';
                            } elseif ($mode === 'bulanan') {
                                $durationTextInit = max(1, $nilai) . ' Bulan';
                            } elseif ($mode === 'tahunan') {
                                $durationTextInit = max(1, $nilai) . ' Tahun';
                            } elseif ($mode === 'tiga_bulan') {
                                $durationTextInit = '3 Bulan';
                            } elseif ($mode === 'semester') {
                                $durationTextInit = '6 Bulan';
                            }
                            $subtotalInit = 0.0;
                            if ($mode === 'harian') {
                                $subtotalInit = ($monthly / 30.0) * max(1, $nilai) * $roomsInit;
                            } elseif ($mode === 'mingguan') {
                                $subtotalInit = ($monthly / 4.0) * max(1, $nilai) * $roomsInit;
                            } elseif ($mode === 'bulanan') {
                                $subtotalInit = $monthly * max(1, $nilai) * $roomsInit;
                            } elseif ($mode === 'tahunan') {
                                $subtotalInit = $monthly * (12 * max(1, $nilai)) * $roomsInit;
                            } elseif ($mode === 'tiga_bulan') {
                                $subtotalInit = ((float) $kosan->harga_tiga_bulan ?: ($monthly * 3)) * $roomsInit;
                            } elseif ($mode === 'semester') {
                                $subtotalInit = ((float) $kosan->harga_semester ?: ($monthly * 6)) * $roomsInit;
                            } else {
                                $subtotalInit = $monthly * $roomsInit;
                            }
                        @endphp

                        <!-- Kosan Info -->
                        <div class="kosan-summary mb-3">
                            <div class="d-flex mb-3">
                                <div class="kosan-image me-3">
                                    @if ($kosan->fotoUtama)
                                        <img src="{{ asset('storage/' . $kosan->fotoUtama->path_gambar) }}"
                                            alt="{{ $kosan->nama_kosan }}" class="img-fluid rounded"
                                            style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/no-image.jpg') }}" alt="{{ $kosan->nama_kosan }}"
                                            class="img-fluid rounded"
                                            style="width: 80px; height: 80px; object-fit: cover;">
                                    @endif
                                </div>
                                <div>
                                    <h5 class="mb-1">{{ $kosan->nama_kosan }}</h5>
                                    <p class="text-muted small mb-1">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $kosan->kecamatan }}, {{ $kosan->kota }}
                                    </p>
                                    <span
                                        class="badge {{ $kosan->jenis_kos == 'putra' ? 'bg-primary' : ($kosan->jenis_kos == 'putri' ? 'bg-danger' : 'bg-success') }}">
                                        Kos {{ ucfirst($kosan->jenis_kos) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Booking Details -->
                        <div class="booking-details mb-3">
                            <div class="mb-2 d-flex justify-content-between">
                                <span>Durasi</span>
                                <span id="summaryCost">{{ $durationTextInit }}</span>
                            </div>
                            @if(isset($selectedKamar))
                            <div class="mb-2 d-flex justify-content-between">
                                <span>Nomor Kamar</span>
                                <span id="summaryRoomNumber">Kamar {{ $selectedKamar->nomor_kamar }}</span>
                            </div>
                            @endif
                            <div class="mb-2 d-flex justify-content-between">
                                <span>Tanggal Mulai</span>
                                <span id="summaryStartDate">{{ date('d M Y') }}</span>
                            </div>
                            <div class="mb-2 d-flex justify-content-between">
                                <span>Tanggal Selesai</span>
                                <span id="summaryEndDate">-</span>
                            </div>
                            <div class="mb-2 d-flex justify-content-between">
                                <span>Jumlah Kamar</span>
                                <span id="summaryRoomCount">1 Kamar</span>
                            </div>
                        </div>

                        <hr>

                        <!-- Price Calculation -->
                        <div class="price-calculation mb-3">
                            <div class="mb-2 d-flex justify-content-between">
                                <span>Harga per Kamar</span>
                                <span id="summaryRoomPrice">Rp
                                    {{ number_format(($monthlyRoomPrice ?? (float) $kosan->getHargaSetelahDiskonAttribute()), 0, ',', '.') }}</span>
                            </div>
                            <div class="mb-2 d-flex justify-content-between">
                                <span id="summaryDuration">{{ $durationTextInit }} x {{ $roomsInit }} Kamar</span>
                                <span id="summarySubtotal">Rp
                                    {{ number_format($subtotalInit, 0, ',', '.') }}</span>
                            </div>

                            @if ($kosan->persentase_diskon > 0)
                                <div class="mb-2 d-flex justify-content-between text-success">
                                    <span>Diskon ({{ $kosan->persentase_diskon }}%)</span>
                                    <span>-Rp
                                        {{ number_format(($kosan->harga_bulanan - $kosan->getHargaSetelahDiskonAttribute()) * request('jumlah_kamar', 1), 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Total -->
                        <div class="total-price p-3 bg-light rounded mb-3">
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total Pembayaran</span>
                                <span id="summaryTotal" class="text-primary">
                                    Rp
                                    {{ number_format($subtotalInit, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Payment Info -->
                        <div class="payment-info small">
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Setelah booking disetujui, Anda akan diminta melakukan pembayaran dalam waktu 24 jam untuk
                                mengamankan pemesanan.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Syarat dan Ketentuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>1. Pemesanan dan Pembayaran</h6>
                    <ul>
                        <li>Pembayaran harus dilakukan dalam waktu 24 jam setelah konfirmasi dari pemilik kosan.</li>
                        <li>Pemesanan dianggap tidak sah jika pembayaran tidak dilakukan dalam jangka waktu tersebut.</li>
                        <li>Pembayaran dilakukan melalui transfer bank atau metode pembayaran lain yang disediakan.</li>
                    </ul>

                    <h6>2. Pembatalan dan Pengembalian Dana</h6>
                    <ul>
                        <li>Pembatalan 7 hari atau lebih sebelum tanggal check-in: pengembalian dana 90%.</li>
                        <li>Pembatalan 3-7 hari sebelum tanggal check-in: pengembalian dana 50%.</li>
                        <li>Pembatalan kurang dari 3 hari sebelum tanggal check-in: tidak ada pengembalian dana.</li>
                    </ul>

                    <h6>3. Check-in dan Check-out</h6>
                    <ul>
                        <li>Waktu check-in: 12.00 - 18.00 WIB.</li>
                        <li>Waktu check-out: maksimal pukul 12.00 WIB.</li>
                        <li>Keterlambatan check-out dapat dikenakan biaya tambahan.</li>
                    </ul>

                    <h6>4. Peraturan Kosan</h6>
                    <ul>
                        <li>Penghuni wajib mematuhi peraturan kosan yang ditetapkan oleh pemilik.</li>
                        <li>Dilarang membawa tamu menginap tanpa izin pemilik kosan.</li>
                        <li>Dilarang membuat keributan yang mengganggu penghuni lain.</li>
                        <li>Dilarang menggunakan atau mengedarkan obat-obatan terlarang dan minuman keras.</li>
                    </ul>

                    <h6>5. Tanggung Jawab</h6>
                    <ul>
                        <li>Penghuni bertanggung jawab atas kerusakan fasilitas kosan yang disebabkan oleh kelalaian.</li>
                        <li>Pemilik kosan tidak bertanggung jawab atas kehilangan atau kerusakan barang pribadi penghuni.
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Saya Mengerti</button>
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
        }

        /* Section titles */
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 15px;
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

        /* Button styles */
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-outline-secondary {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
        }

        /* Summary section */
        .kosan-summary img {
            border-radius: 8px;
        }

        .price-calculation,
        .booking-details {
            font-size: 14px;
        }

        .total-price {
            font-size: 16px;
        }

        /* Alert styles */
        .alert-info {
            background-color: var(--secondary);
            border-color: var(--primary-light);
            color: var(--primary-dark);
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

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .sticky-top {
                position: relative;
                top: 0 !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to calculate end date based on start date and duration
            function calculateEndDate() {
                const startDateInput = document.getElementById('tanggal_mulai');
                const durationSelect = document.getElementById('jenis_durasi');
                const endDateInput = document.getElementById('tanggal_selesai');
                const summaryStartDate = document.getElementById('summaryStartDate');
                const summaryEndDate = document.getElementById('summaryEndDate');
                const hariCustomInput = document.getElementById('hari_custom');
                const mingguCustomInput = document.getElementById('minggu_custom');
                const bulanCustomInput = document.getElementById('bulan_custom');
                const tahunCustomInput = document.getElementById('tahun_custom');

                if (!startDateInput || !durationSelect || !endDateInput) return;

                const startDate = new Date(startDateInput.value);
                const selectedOption = durationSelect.options[durationSelect.selectedIndex];
                let monthsToAdd = parseInt(selectedOption.getAttribute('data-value'));
                let nilaiDurasi = 1; // nilai durasi yg dikirim ke backend

                // Durasi custom override
                if (durationSelect.value === 'harian') {
                    const d = hariCustomInput && hariCustomInput.value ? parseInt(hariCustomInput.value) : 1;
                    nilaiDurasi = isNaN(d) || d < 1 ? 1 : d;
                } else if (durationSelect.value === 'mingguan') {
                    const w = mingguCustomInput && mingguCustomInput.value ? parseInt(mingguCustomInput.value) : 1;
                    nilaiDurasi = isNaN(w) || w < 1 ? 1 : w;
                } else if (durationSelect.value === 'bulanan') {
                    const m = bulanCustomInput && bulanCustomInput.value ? parseInt(bulanCustomInput.value) : monthsToAdd;
                    if (!isNaN(m) && m >= 1 && m <= 11) { monthsToAdd = m; }
                    nilaiDurasi = monthsToAdd;
                } else if (durationSelect.value === 'tahunan') {
                    const y = tahunCustomInput && tahunCustomInput.value ? parseInt(tahunCustomInput.value) : 1;
                    const validY = isNaN(y) || y < 1 ? 1 : y;
                    monthsToAdd = validY * 12;
                    nilaiDurasi = validY; // tahunan kirim jumlah tahun
                } else {
                    // paket tiga_bulan / semester: end date pakai monthsToAdd, nilai_durasi anggap 1 paket
                    nilaiDurasi = 1;
                }

                // Set the hidden field value
                document.getElementById('nilai_durasi').value = nilaiDurasi;

                // Calculate end date
                const endDate = new Date(startDate);
                if (durationSelect.value === 'harian') {
                    endDate.setDate(endDate.getDate() + nilaiDurasi);
                } else if (durationSelect.value === 'mingguan') {
                    endDate.setDate(endDate.getDate() + (nilaiDurasi * 7));
                } else {
                    endDate.setMonth(endDate.getMonth() + monthsToAdd);
                }

                // Format date for form field (YYYY-MM-DD)
                const formattedEndDate = [
                    endDate.getFullYear(),
                    String(endDate.getMonth() + 1).padStart(2, '0'),
                    String(endDate.getDate()).padStart(2, '0')
                ].join('-');
                endDateInput.value = formattedEndDate;

                // Format dates for summary display
                const options = {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                };
                summaryStartDate.textContent = startDate.toLocaleDateString('id-ID', options);
                summaryEndDate.textContent = endDate.toLocaleDateString('id-ID', options);
            }

            // Function to update price calculation
            function updatePriceCalculation() {
                const durationSelect = document.getElementById('jenis_durasi');
                const roomCountSelect = document.getElementById('jumlah_kamar');
                const roomCountHidden = document.getElementById('jumlah_kamar_hidden');
                const summaryRoomPrice = document.getElementById('summaryRoomPrice');
                const summaryDuration = document.getElementById('summaryDuration');
                const summarySubtotal = document.getElementById('summarySubtotal');
                const summaryTotal = document.getElementById('summaryTotal');
                const summaryCost = document.getElementById('summaryCost');
                const summaryRoomCount = document.getElementById('summaryRoomCount');
                const bulanCustomInput = document.getElementById('bulan_custom');
                const tahunCustomInput = document.getElementById('tahun_custom');
                const hariCustomInput = document.getElementById('hari_custom');
                const mingguCustomInput = document.getElementById('minggu_custom');

                if (!durationSelect) return;

                const selectedDuration = durationSelect.options[durationSelect.selectedIndex];
                let durationText = selectedDuration.textContent;

                // Tambahkan console.log untuk debugging
                console.log("Selected duration value:", selectedDuration.value);
                console.log("Selected data-value:", selectedDuration.getAttribute('data-value'));

                // Pastikan ini mengambil nilai price dengan benar
                const monthlyPrice = parseFloat({{ (float) ($monthlyRoomPrice ?? $kosan->getHargaSetelahDiskonAttribute()) }});
                const yearlyPrice  = parseFloat({{ (float) $kosan->getHargaTahunanSetelahDiskonAttribute() }});
                const dailyPrice   = monthlyPrice / 30.0;
                const weeklyPrice  = monthlyPrice / 4.0;

                const roomCount = roomCountSelect && roomCountSelect.value ? parseInt(roomCountSelect.value) : (roomCountHidden && roomCountHidden.value ? parseInt(roomCountHidden.value) : 1);
                console.log("Room count:", roomCount);

                // Update room count display
                summaryRoomCount.textContent = roomCount + ' Kamar';

                // Update duration display
                summaryCost.textContent = durationText;

                // Format price for display - gunakan fungsi format yang konsisten
                const formatCurrency = (value) => {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                };

                // Update price displays
                // Harga per kamar tergantung mode
                let months = parseInt(selectedDuration.getAttribute('data-value'));
                if (durationSelect.value === 'bulanan' && bulanCustomInput && bulanCustomInput.value) {
                    months = parseInt(bulanCustomInput.value);
                    durationText = `${months} Bulan`;
                }
                if (durationSelect.value === 'tahunan' && tahunCustomInput && tahunCustomInput.value) {
                    const years = parseInt(tahunCustomInput.value);
                    months = years * 12;
                    durationText = `${years} Tahun`;
                }
                if (durationSelect.value === 'harian' && hariCustomInput && hariCustomInput.value) {
                    const days = parseInt(hariCustomInput.value);
                    durationText = `${days} Hari`;
                }
                if (durationSelect.value === 'mingguan' && mingguCustomInput && mingguCustomInput.value) {
                    const weeks = parseInt(mingguCustomInput.value);
                    durationText = `${weeks} Minggu`;
                }

                summaryRoomPrice.textContent = formatCurrency(monthlyPrice);

                // Update duration display
                summaryDuration.textContent = `${durationText} x ${roomCount} Kamar`;

                // Calculate subtotal
                let subtotal = 0;
                if (durationSelect.value === 'harian') {
                    const days = hariCustomInput && hariCustomInput.value ? parseInt(hariCustomInput.value) : 1;
                    subtotal = dailyPrice * days * roomCount;
                } else if (durationSelect.value === 'mingguan') {
                    const weeks = mingguCustomInput && mingguCustomInput.value ? parseInt(mingguCustomInput.value) : 1;
                    subtotal = weeklyPrice * weeks * roomCount;
                } else if (durationSelect.value === 'bulanan') {
                    subtotal = monthlyPrice * months * roomCount;
                } else if (durationSelect.value === 'tahunan') {
                    subtotal = monthlyPrice * months * roomCount;
                } else {
                    const packagePrice = parseFloat(selectedDuration.getAttribute('data-price') || '0');
                    subtotal = (packagePrice > 0 ? packagePrice : monthlyPrice * months) * roomCount;
                }
                console.log("Calculated subtotal:", subtotal);

                // Update subtotal display
                summarySubtotal.textContent = formatCurrency(subtotal);

                // Update total price display
                summaryTotal.textContent = formatCurrency(subtotal);
            }

            async function checkAvailability() {
                const kosanId = {{ $kosan->kosan_id }};
                const startDate = document.getElementById('tanggal_mulai').value;
                const durationSelect = document.getElementById('jenis_durasi');
                const bulanCustomInput = document.getElementById('bulan_custom');
                const tahunCustomInput = document.getElementById('tahun_custom');
                const hariCustomInput = document.getElementById('hari_custom');
                const mingguCustomInput = document.getElementById('minggu_custom');
                const roomCountSelect = document.getElementById('jumlah_kamar');
                const roomCountHidden = document.getElementById('jumlah_kamar_hidden');
                const submitBtn = document.querySelector('button[type="submit"]');
                const availabilityBase = "{{ route('api.kosan.availability', $kosan->kosan_id) }}";

                let type = durationSelect.value;
                let value = parseInt(durationSelect.options[durationSelect.selectedIndex].getAttribute('data-value'));
                if (type === 'harian' && hariCustomInput && hariCustomInput.value) {
                    value = parseInt(hariCustomInput.value);
                }
                if (type === 'mingguan' && mingguCustomInput && mingguCustomInput.value) {
                    value = parseInt(mingguCustomInput.value);
                }
                if (type === 'bulanan' && bulanCustomInput && bulanCustomInput.value) {
                    value = parseInt(bulanCustomInput.value);
                }
                if (type === 'tahunan' && tahunCustomInput && tahunCustomInput.value) {
                    value = parseInt(tahunCustomInput.value);
                }
                const roomCount = roomCountSelect && roomCountSelect.value ? parseInt(roomCountSelect.value) : (roomCountHidden && roomCountHidden.value ? parseInt(roomCountHidden.value) : 1);

                try {
                    const url = `${availabilityBase}?start=${encodeURIComponent(startDate)}&duration_type=${encodeURIComponent(type)}&duration_value=${value}`;
                    const res = await fetch(url);
                    const data = await res.json();

                    const availableCount = parseInt(data.available_count || 0);
                    const statusBadge = document.querySelector('.availability-badge');
                    if (availableCount >= roomCount) {
                        if (statusBadge) {
                            statusBadge.classList.remove('unavailable');
                            statusBadge.classList.add('available');
                            statusBadge.innerHTML = `<i class="fas fa-check-circle me-2"></i>${availableCount} kamar tersedia`;
                        }
                        if (submitBtn) submitBtn.disabled = false;
                    } else {
                        if (statusBadge) {
                            statusBadge.classList.remove('available');
                            statusBadge.classList.add('unavailable');
                            statusBadge.innerHTML = `<i class="fas fa-times-circle me-2"></i>Tidak tersedia`;
                        }
                        if (submitBtn) submitBtn.disabled = true;
                    }
                } catch (e) {
                    const statusBadge = document.querySelector('.availability-badge');
                    if (statusBadge) {
                        statusBadge.classList.remove('available');
                        statusBadge.classList.add('unavailable');
                        statusBadge.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i>Gagal cek ketersediaan`;
                    }
                    if (submitBtn) submitBtn.disabled = true;
                }
            }

            // Add event listeners
            const startDateInput = document.getElementById('tanggal_mulai');
            const durationSelect = document.getElementById('jenis_durasi');
            const roomCountSelect = document.getElementById('jumlah_kamar');

            if (startDateInput) {
                startDateInput.addEventListener('change', function() {
                    calculateEndDate();
                });
                // Initialize with local date (YYYY-MM-DD) to avoid timezone shifting
                if (!startDateInput.value) {
                    const now = new Date();
                    const localYMD = [
                        now.getFullYear(),
                        String(now.getMonth() + 1).padStart(2, '0'),
                        String(now.getDate()).padStart(2, '0')
                    ].join('-');
                    startDateInput.value = localYMD;
                }
            }

            function toggleCustomInputs() {
                const durSel = document.getElementById('jenis_durasi');
                const harianDivCol = document.getElementById('input_harian_custom_col');
                const mingguanDivCol = document.getElementById('input_mingguan_custom_col');
                const bulananDivCol = document.getElementById('input_bulanan_custom_col');
                const tahunanDivCol = document.getElementById('input_tahunan_custom_col');
                if (!durSel) return;
                // Hide all custom inputs
                if (harianDivCol) harianDivCol.style.display = 'none';
                if (mingguanDivCol) mingguanDivCol.style.display = 'none';
                if (bulananDivCol) bulananDivCol.style.display = 'none';
                if (tahunanDivCol) tahunanDivCol.style.display = 'none';

                // Show the relevant custom input
                if (durSel.value === 'harian') {
                    if (harianDivCol) harianDivCol.style.display = 'block';
                } else if (durSel.value === 'mingguan') {
                    if (mingguanDivCol) mingguanDivCol.style.display = 'block';
                } else if (durSel.value === 'bulanan') {
                    if (bulananDivCol) bulananDivCol.style.display = 'block';
                } else if (durSel.value === 'tahunan') {
                    if (tahunanDivCol) tahunanDivCol.style.display = 'block';
                }
            }

            if (durationSelect) {
                durationSelect.addEventListener('change', function() {
                    toggleCustomInputs();
                    calculateEndDate();
                    updatePriceCalculation();
                    checkAvailability();
                });
            }

            if (roomCountSelect) {
                roomCountSelect.addEventListener('change', function() {
                    updatePriceCalculation();
                    checkAvailability();
                });
            }

            const hariCustomInput = document.getElementById('hari_custom');
            const mingguCustomInput = document.getElementById('minggu_custom');
            const bulanCustomInput = document.getElementById('bulan_custom');
            const tahunCustomInput = document.getElementById('tahun_custom');

            if (hariCustomInput) {
                hariCustomInput.addEventListener('input', function() {
                    calculateEndDate();
                    updatePriceCalculation();
                    checkAvailability();
                });
                hariCustomInput.addEventListener('change', function() {
                    calculateEndDate();
                    updatePriceCalculation();
                    checkAvailability();
                });
            }
            if (mingguCustomInput) {
                mingguCustomInput.addEventListener('input', function() {
                    calculateEndDate();
                    updatePriceCalculation();
                    checkAvailability();
                });
                mingguCustomInput.addEventListener('change', function() {
                    calculateEndDate();
                    updatePriceCalculation();
                    checkAvailability();
                });
            }
            if (bulanCustomInput) {
                bulanCustomInput.addEventListener('input', function() {
                    calculateEndDate();
                    updatePriceCalculation();
                    checkAvailability();
                });
                bulanCustomInput.addEventListener('change', function() {
                    calculateEndDate();
                    updatePriceCalculation();
                    checkAvailability();
                });
            }
            if (tahunCustomInput) {
                tahunCustomInput.addEventListener('input', function() {
                    calculateEndDate();
                    updatePriceCalculation();
                    checkAvailability();
                });
                tahunCustomInput.addEventListener('change', function() {
                    calculateEndDate();
                    updatePriceCalculation();
                    checkAvailability();
                });
            }

            // Initialize custom input values from query
            const initialDurasi = "{{ request('durasi', 'bulanan') }}";
            const initialNilai = parseInt("{{ request('nilai_durasi', 1) }}");
            if (initialDurasi === 'harian' && hariCustomInput) {
                hariCustomInput.value = Math.max(1, initialNilai);
            }
            if (initialDurasi === 'mingguan' && mingguCustomInput) {
                mingguCustomInput.value = Math.max(1, initialNilai);
            }
            if (initialDurasi === 'bulanan' && bulanCustomInput) {
                bulanCustomInput.value = initialNilai;
            }
            if (initialDurasi === 'tahunan' && tahunCustomInput) {
                tahunCustomInput.value = Math.max(1, initialNilai);
            }

            // Initialize calculations on page load
            toggleCustomInputs();
            calculateEndDate();
            updatePriceCalculation();
            checkAvailability();

            setInterval(checkAvailability, 15000);
        });
    </script>
@endpush
