<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        h1 { font-size: 20px; text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .total-row td { font-weight: bold; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <h1>{{ $title }} - {{ date('d M Y') }}</h1>
    <table>
        <thead>
            <tr>
                <th>Tanggal Bayar</th>
                <th>Kosan</th>
                <th>Booking ID</th>
                <th class="text-right">Jumlah Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pembayaranList as $p)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d/m/Y') }}</td>
                    <td>{{ $p->booking->kamar->kosan->nama_kosan ?? '-' }}</td>
                    <td>{{ $p->booking->kode_booking ?? $p->booking_id }}</td>
                    <td class="text-right">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada data untuk ditampilkan.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right">Total Pendapatan</td>
                <td class="text-right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
