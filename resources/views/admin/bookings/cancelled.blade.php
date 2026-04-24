@extends('layouts.admin.app')

@section('title', 'Booking Dibatalkan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Manajemen Booking</a></li>
    <li class="breadcrumb-item active">Dibatalkan</li>
@endsection

@section('page-title', 'Booking Dibatalkan')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-times-circle me-2"></i>Booking Dibatalkan</h5>
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
                                    <th>Durasi</th>
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
                                        <td>{{ \Carbon\Carbon::parse($booking->tanggal_mulai)->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            @if ($booking->durasi >= 12)
                                                {{ $booking->durasi / 12 }} Tahun
                                            @else
                                                {{ $booking->durasi }} Bulan
                                            @endif
                                        </td>
                                        <td>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Tidak ada booking yang dibatalkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination --}}
                    @if ($bookings->hasPages())
                        <div class="p-3">
                            {{ $bookings->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
