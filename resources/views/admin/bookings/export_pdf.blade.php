<!DOCTYPE html>
<html>

<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; }
        h1 { font-size: 18px; text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000000; padding: 6px; text-align: left; }
        th { background-color: #a7a7a7; font-weight: bold; }
        .text-right { text-align: right; }
    </style>
</head>

<body>
    <h1>{{ $title }}</h1>
    <table>
        <thead>
            <tr>
                <th>Kode Booking</th>
                <th>Pengguna</th>
                <th>Kosan</th>
                <th>Kamar</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Keluar</th>
                <th>Durasi</th>
                <th>Status</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>{{ $booking->kode_booking }}</td>
                    <td>{{ $booking->user->nama_lengkap ?? '-' }}</td>
                    <td>{{ $booking->kamar->kosan->nama_kosan ?? '-' }}</td>
                    <td>{{ $booking->kamar->nomor_kamar ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->tanggal_checkin)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->tanggal_checkout)->format('d/m/Y') }}</td>
                    <td>
                        @if ($booking->durasi >= 12)
                            {{ $booking->durasi / 12 }} Tahun
                        @else
                            {{ $booking->durasi }} Bulan
                        @endif
                    </td>
                    <td>
                        @if ($booking->status_booking == 'pending')
                            <span>Pending</span>
                        @elseif($booking->status_booking == 'confirmed')
                            <span>Dikonfirmasi</span>
                        @elseif($booking->status_booking == 'cancelled')
                            <span>Dibatalkan</span>
                        @else
                            <span>{{ $booking->status_booking }}</span>
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data booking yang cocok dengan filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
