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
    </style>
</head>
<body>
    <h1>{{ $title }} - {{ date('d M Y') }}</h1>
    <table>
        <thead>
            <tr>
                <th>Nama Kosan</th>
                <th>Total Kamar</th>
                <th>Kamar Terisi</th>
                <th>Kamar Kosong</th>
                <th>Tingkat Okupansi (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($okupansiData as $data)
                <tr>
                    <td>{{ $data['nama_kosan'] }}</td>
                    <td>{{ $data['total_kamar'] }}</td>
                    <td>{{ $data['kamar_terisi'] }}</td>
                    <td>{{ $data['kamar_kosong'] }}</td>
                    <td>{{ number_format($data['tingkat_okupansi'], 2) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data untuk ditampilkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
