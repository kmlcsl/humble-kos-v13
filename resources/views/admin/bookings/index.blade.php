@extends('layouts.admin.app')

@section('title', 'Semua Booking')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Manajemen Booking</li>
@endsection

@section('page-title', 'Semua Booking')

@section('page-actions')
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('admin.bookings.export.excel', request()->all()) }}" class="btn btn-success">
            <i class="fas fa-file-excel me-1"></i> Export Excel
        </a>
        <a href="{{ route('admin.bookings.export.pdf', request()->all()) }}" class="btn btn-danger">
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
                    <form action="{{ route('admin.bookings.index') }}" method="GET" class="row g-3 align-items-end">
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
                                    @endphp
                                    <option value="{{ $status }}"
                                        {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ $labels[$status] ?? ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Kamar</label>
                            <select name="nomor_kamar" class="form-select">
                                <option value="">Semua Kamar</option>
                                @foreach ($bookingKamars ?? [] as $kamars)
                                    <option value="{{ $kamars }}"
                                        {{ request('nomor_kamar') == $kamars ? 'selected' : '' }}>
                                        {{ $kamars }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-auto">
                            <div class="d-flex gap-2 flex-wrap flex-md-nowrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white">
                    <i class="fas fa-calendar-check me-2"></i>Daftar Semua Booking
                </h5>
                <span>Total: {{ $bookings->total() }} booking</span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%">
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
                        @forelse ($bookings as $index => $booking)
                            <tr>
                                <td>{{ $bookings->firstItem() + $index }}</td>
                                <td><strong>{{ $booking->booking_id }}</strong></td>
                                <td>{{ $booking->user->name ?? '-' }}</td>
                                <td>{{ $booking->kosan->nama_kosan ?? '-' }}</td>
                                <td>{{ $booking->kamar->nomor_kamar ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->tanggal_checkin)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->tanggal_checkout)->format('d/m/Y') }}</td>
                                <td>
                                    {{ $booking->durasi >= 12 ? $booking->durasi / 12 . ' Tahun' : $booking->durasi . ' Bulan' }}
                                </td>
                                <td>
                                    @if ($booking->status_booking === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif ($booking->status_booking === 'confirmed')
                                        <span class="badge bg-success">Dikonfirmasi</span>
                                    @elseif ($booking->status_booking === 'cancelled')
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $booking->status_booking }}</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Tidak ada data booking.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            @if ($bookings->hasPages())
                <div class="mt-3">
                    {{ $bookings->links() }}
                </div>
            @endif
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

        /* Button Filter dan Reset dalam satu baris */
        .flex-md-nowrap {
            flex-wrap: wrap;
        }

        @media (min-width: 768px) {
            .flex-md-nowrap {
                flex-wrap: nowrap;
            }
        }

        /* Memastikan button alignment */
        .d-flex.gap-2 > * {
            height: fit-content;
        }

        /* Align button dengan form elements */
        .card-body .row.g-3 {
            align-items: flex-end;
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
