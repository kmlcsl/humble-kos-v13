<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; }
        h1 { font-size: 18px; text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h1>{{ $title }} - {{ date('d M Y') }}</h1>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Booking ID</th>
                <th>User</th>
                <th>Metode</th>
                <th>Status</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksiList as $t)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($t->created_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ $t->booking->kode_booking ?? $t->booking_id }}</td>
                    <td>{{ $t->booking->pengguna->name ?? '-' }}</td>
                    <td>{{ strtoupper($t->metode_pembayaran) }}</td>
                    <td>{{ ucfirst($t->status_pembayaran) }}</td>
                    <td class="text-right">Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data untuk ditampilkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
