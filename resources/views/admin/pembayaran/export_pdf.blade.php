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
                <th>ID Pembayaran</th>
                <th>Kode Booking</th>
                <th>Pengguna</th>
                <th>Metode</th>
                <th>Status</th>
                <th>Tipe</th>
                <th>Tanggal</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pembayaran as $payment)
                <tr>
                    <td>{{ $payment->pembayaran_id }}</td>
                    <td>{{ $payment->booking->kode_booking ?? '-' }}</td>
                    <td>{{ $payment->booking->user->nama_lengkap ?? '-' }}</td>
                    <td>{{ $payment->method_display_name }}</td>
                    <td>{{ $payment->status_label }}</td>
                    <td>{{ ucfirst($payment->tipe_pembayaran) }}</td>
                    <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-right">Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data pembayaran yang cocok dengan filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
