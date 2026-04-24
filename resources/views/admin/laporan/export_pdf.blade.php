<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Ringkasan Laporan Admin' }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #4e73df; color: #fff; text-align: left; }
        .muted { color: #666; font-size: 11px; }
    </style>
</head>
<body>
    <h1>{{ $title ?? 'Ringkasan Laporan Admin' }}</h1>
    <div class="muted">Tanggal: {{ $tanggal ?? date('d/m/Y') }}</div>
    <table>
        <thead>
            <tr>
                <th>Metrix</th>
                <th>Nilai</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Kosan</td>
                <td>{{ $total_kosan }}</td>
                <td>Jumlah seluruh kosan</td>
            </tr>
            <tr>
                <td>Booking Pending</td>
                <td>{{ $booking_pending }}</td>
                <td>Menunggu konfirmasi</td>
            </tr>
            <tr>
                <td>Booking Confirmed</td>
                <td>{{ $booking_confirmed }}</td>
                <td>Dikonfirmasi</td>
            </tr>
            <tr>
                <td>Booking Cancelled</td>
                <td>{{ $booking_cancelled }}</td>
                <td>Dibatalkan</td>
            </tr>
            <tr>
                <td>Pendapatan</td>
                <td>Rp {{ number_format($pendapatan ?? 0, 0, ',', '.') }}</td>
                <td>Pembayaran sukses</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
