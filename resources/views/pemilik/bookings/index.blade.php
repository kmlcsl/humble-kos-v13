@extends('layouts.pemilik.app')

@section('title', 'Semua Booking')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Manajemen Booking</li>
@endsection

@section('page-title', 'Semua Booking')

@section('page-actions')
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('pemilik.bookings.export.excel', request()->all()) }}" class="btn btn-success">
            <i class="fas fa-file-excel me-1"></i> Export Excel
        </a>
        <a href="{{ route('pemilik.bookings.export.pdf', request()->all()) }}" class="btn btn-danger">
            <i class="fas fa-file-pdf me-1"></i> Export PDF
        </a>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white"><i class="fas fa-filter me-2"></i>Filter</h5>
                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse"
                    data-bs-target="#filterCollapse" aria-expanded="true" aria-controls="filterCollapse"
                    id="toggleFilterBtn">
                    <i class="fas fa-chevron-up me-1"></i> <span id="filterBtnText">Sembunyikan</span>
                </button>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="card-body">
                    <form action="{{ route('pemilik.bookings.index') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Status</label>
                            <select name="status" class="form-select">
                                <option value="all">Semua Status</option>
                                @foreach ($statusOptions ?? collect() as $status)
                                    @php
                                        $labels = [
                                            'pending' => 'Menunggu',
                                            'confirmed' => 'Dikonfirmasi',
                                            'cancelled' => 'Dibatalkan',
                                            'selesai' => 'Selesai',
                                        ];
                                        $label = $labels[$status] ?? ucfirst($status);
                                    @endphp
                                    <option value="{{ $status }}"
                                        {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="nomor_kamar" class="form-label small fw-bold">Kamar</label>
                            <select class="form-select" id="nomor_kamar" name="nomor_kamar">
                                <option value="">Semua Kamar</option>
                                @foreach ($bookingKamars ?? [] as $kamars)
                                    <option value="{{ $kamars }}"
                                        {{ request('nomor_kamar') == $kamars ? 'selected' : '' }}>
                                        {{ $kamars }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-auto">
                            <div class="d-flex gap-2 flex-wrap flex-md-nowrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('pemilik.bookings.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-sync-alt me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-white"><i class="fas fa-calendar-check me-2"></i>Daftar Semua
                            Booking</h5>
                    </div>
                    <div class="card-body p-0">
                        @if ($bookings->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Booking ID</th>
                                            <th>Pengguna</th>
                                            <th>Kosan</th>
                                            <th>Kamar</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Keluar</th>
                                            <th>Durasi</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bookings as $index => $booking)
                                            <tr>
                                                <td>{{ $bookings->firstItem() + $index }}</td>
                                                <td><strong>{{ $booking->booking_id }}</strong></td>
                                                <td>{{ $booking->user->name ?? '-' }}</td>
                                                <td>{{ $booking->kosan->nama_kosan ?? '-' }}</td>
                                                <td>{{ $booking->kamar->nomor_kamar ?? '-' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($booking->tanggal_checkin)->format('d/m/Y') }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($booking->tanggal_checkout)->format('d/m/Y') }}
                                                </td>
                                                <td>
                                                    @if ($booking->durasi >= 12)
                                                        {{ $booking->durasi / 12 }} Tahun
                                                    @else
                                                        {{ $booking->durasi }} Bulan
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($booking->status_booking == 'pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($booking->status_booking == 'confirmed')
                                                        <span class="badge bg-success">Dikonfirmasi</span>
                                                    @elseif($booking->status_booking == 'cancelled')
                                                        <span class="badge bg-danger">Dibatalkan</span>
                                                    @else
                                                        <span
                                                            class="badge bg-secondary">{{ $booking->status_booking }}</span>
                                                    @endif
                                                </td>
                                                <td>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $bookings->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>Belum ada data booking.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Filter Collapse */
        #filterCollapse {
            transition: all 0.3s ease-in-out;
        }

        #filterCollapse .card-body {
            display: block !important;
            visibility: visible !important;
        }

        #filterCollapse.collapsing {
            overflow: hidden;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle Filter Collapse Toggle
            const filterCollapse = document.getElementById('filterCollapse');
            const filterBtnText = document.getElementById('filterBtnText');
            const toggleFilterBtn = document.getElementById('toggleFilterBtn');

            if (filterCollapse && filterBtnText && toggleFilterBtn) {
                filterCollapse.addEventListener('show.bs.collapse', function() {
                    filterBtnText.textContent = 'Sembunyikan';
                    toggleFilterBtn.querySelector('i').className = 'fas fa-chevron-up me-1';
                });

                filterCollapse.addEventListener('hide.bs.collapse', function() {
                    filterBtnText.textContent = 'Tampilkan';
                    toggleFilterBtn.querySelector('i').className = 'fas fa-chevron-down me-1';
                });
            }
        });
    </script>
@endpush
