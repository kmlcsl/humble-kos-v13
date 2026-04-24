<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
        h1 { font-size: 22px; text-align: center; margin-bottom: 5px; }
        h2 { font-size: 16px; margin-top: 25px; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;}
        .subtitle { text-align: center; font-size: 12px; color: #666; margin-bottom: 25px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 7px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .summary-container { margin-bottom: 20px; }
        .summary-box {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            border: 1px solid #eee;
            padding: 10px;
            box-sizing: border-box;
        }
        .summary-box h3 { font-size: 14px; margin-top: 0; margin-bottom: 10px; }
        .summary-box table td { border: none; padding: 4px; }
        .summary-box table tr td:first-child { font-weight: bold; color: #555; }
        .page-break { page-break-after: always; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p class="subtitle">Generated on: {{ date('d F Y H:i') }}</p>
    
    <div class="summary-container">
        <div class="summary-box">
            <h3>Statistik Umum</h3>
            <table>
                <tr>
                    <td>Total Kosan Anda:</td>
                    <td>{{ $totalKosan }}</td>
                </tr>
                <tr>
                    <td>Total Penyewa:</td>
                    <td>{{ $totalUsers }}</td>
                </tr>
                <tr>
                    <td>Total Pendapatan:</td>
                    <td>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
        <div class="summary-box" style="margin-left: 3%;">
            <h3>Statistik Booking</h3>
            <table>
                <tr>
                    <td>Total Booking:</td>
                    <td>{{ $totalBookings }}</td>
                </tr>
                <tr>
                    <td>Booking Pending:</td>
                    <td>{{ $pendingBookings }}</td>
                </tr>
                 <tr>
                    <td>Booking Dikonfirmasi:</td>
                    <td>{{ $confirmedBookings }}</td>
                </tr>
                <tr>
                    <td>Booking Dibatalkan:</td>
                    <td>{{ $cancelledBookings }}</td>
                </tr>
            </table>
        </div>
    </div>

    <h2>Pendapatan 6 Bulan Terakhir</h2>
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="text-right">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monthlyRevenue as $revenue)
            <tr>
                <td>{{ \Carbon\Carbon::create($revenue->year, $revenue->month)->format('F Y') }}</td>
                <td class="text-right">Rp {{ number_format($revenue->total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="2" style="text-align: center;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Booking Terbaru</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Kosan</th>
                <th>Status</th>
                <th class="text-right">Total</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentBookings as $booking)
            <tr>
                <td>{{ $booking->booking_id }}</td>
                <td>{{ $booking->kosan->nama_kosan ?? '-' }}</td>
                <td>{{ ucfirst($booking->status_booking) }}</td>
                <td class="text-right">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                <td>{{ $booking->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align: center;">Tidak ada booking terbaru.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
