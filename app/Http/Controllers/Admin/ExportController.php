<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingKosan;
use App\Models\Pembayaran;
use App\Models\Kosan;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportController extends Controller
{
    // --- STYLING & HELPERS ---
    private function getStyles(): array
    {
        return [
            'title' => ['font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF4F4F4F']]],
            'header' => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4E73DF']],
            ],
            'border' => [
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['argb' => 'FFD0D0D0']]],
            ],
            'currency' => ['formatCode' => '"Rp" #,##0'],
            'date' => ['formatCode' => 'dd/mm/yyyy'],
            'datetime' => ['formatCode' => 'dd/mm/yyyy hh:mm'],
        ];
    }

    private function setHeaders(Spreadsheet &$spreadsheet, string $filename)
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
    }

    // --- DATA FETCHING ---
    private function getReportData()
    {
        // ... (existing code)
    }

    private function getPembayaranData(Request $request)
    {
        $query = Pembayaran::with(['booking.user']);

        // Filter berdasarkan status
        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $statusMap = [
                'successful' => 'paid',
                'processing' => 'pending',
            ];
            $dbStatus = $statusMap[$request->status] ?? $request->status;
            $query->where('status_pembayaran', $dbStatus);
        }

        // Filter berdasarkan metode pembayaran
        if ($request->has('metode') && !empty($request->metode) && $request->metode !== 'all') {
            $query->where('metode_pembayaran', $request->metode);
        }

        // Filter berdasarkan tanggal
        if ($request->has('date_start') && !empty($request->date_start)) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }

        if ($request->has('date_end') && !empty($request->date_end)) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        $sortField = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->get();
    }

    private function getBookingData(Request $request)
    {
        $query = BookingKosan::with(['user', 'kamar.kosan'])->orderBy('booking_id', 'asc');
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status_booking', $request->input('status'));
        }
        if ($request->filled('nomor_kamar')) {
            $query->whereHas('kamar', function ($q) use ($request) {
                $q->where('nomor_kamar', $request->input('nomor_kamar'));
            });
        }
        return $query->get();
    }

    // --- EXPORT METHODS ---

    public function exportLaporanPdf()
    {
        $data = [
            'total_kosan' => Kosan::count(),
            'booking_pending' => BookingKosan::where('status_booking', 'pending')->count(),
            'booking_confirmed' => BookingKosan::where('status_booking', 'confirmed')->count(),
            'booking_cancelled' => BookingKosan::where('status_booking', 'cancelled')->count(),
            'pendapatan' => Pembayaran::where('status_pembayaran', 'sukses')->sum('jumlah_bayar'),
            'tanggal' => now()->format('d/m/Y'),
            'title' => 'Ringkasan Laporan Admin',
        ];
        $pdf = Pdf::loadView('admin.laporan.export_pdf', $data);
        return $pdf->setPaper('a4', 'portrait')->download('Ringkasan_Laporan_Admin_' . date('Y-m-d') . '.pdf');
    }

    public function exportLaporanExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ringkasan Laporan');
        $styles = $this->getStyles();

        $sheet->mergeCells('A1:D1')->setCellValue('A1', 'RINGKASAN LAPORAN ADMIN');
        $sheet->getStyle('A1')->applyFromArray($styles['title'])->getAlignment()->setHorizontal('center');

        $headers = ['Metrix', 'Nilai', 'Keterangan', 'Tanggal'];
        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle('A3:D3')->applyFromArray($styles['header']);

        $totalKosan = Kosan::count();
        $bookingPending = BookingKosan::where('status_booking', 'pending')->count();
        $bookingConfirmed = BookingKosan::where('status_booking', 'confirmed')->count();
        $bookingCancelled = BookingKosan::where('status_booking', 'cancelled')->count();
        $pendapatan = Pembayaran::where('status_pembayaran', 'sukses')->sum('jumlah_bayar');

        $dataRows = [
            ['Total Kosan', $totalKosan, 'Jumlah seluruh kosan', now()->format('d/m/Y')],
            ['Booking Pending', $bookingPending, 'Menunggu konfirmasi', now()->format('d/m/Y')],
            ['Booking Confirmed', $bookingConfirmed, 'Dikonfirmasi', now()->format('d/m/Y')],
            ['Booking Cancelled', $bookingCancelled, 'Dibatalkan', now()->format('d/m/Y')],
            ['Pendapatan', $pendapatan, 'Pembayaran sukses', now()->format('d/m/Y')],
        ];
        $sheet->fromArray($dataRows, null, 'A4');
        $lastRow = 3 + count($dataRows);

        $sheet->getStyle('D4:D' . $lastRow)->getNumberFormat()->applyFromArray($styles['date']);
        $sheet->getStyle('B4:B' . $lastRow)->applyFromArray($styles['border']);
        $sheet->getStyle('A3:D' . $lastRow)->applyFromArray($styles['border']);
        $sheet->getStyle('B7')->getNumberFormat()->applyFromArray($styles['currency']);

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->setHeaders($spreadsheet, 'Ringkasan_Laporan_Admin_' . now()->format('Ymd_His') . '.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportBookingExcel(Request $request)
    {
        $bookings = $this->getBookingData($request);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Booking');
        $styles = $this->getStyles();

        $sheet->mergeCells('A1:I1')->setCellValue('A1', 'LAPORAN SEMUA BOOKING');
        $sheet->getStyle('A1')->applyFromArray($styles['title'])->getAlignment()->setHorizontal('center');

        $headers = ['Kode Booking', 'Pengguna', 'Kosan', 'Kamar', 'Tgl Mulai', 'Tgl Keluar', 'Durasi', 'Total', 'Status', 'Tgl Booking'];
        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle('A3:J3')->applyFromArray($styles['header']);

        $dataRows = [];

        $durasiLabel = function ($durasi) {
            $durasi = (int) $durasi;

            if ($durasi >= 12) {
                $tahun = intdiv($durasi, 12); // bulat ke bawah
                return $tahun . ' Tahun';
            }

            return $durasi . ' Bulan';
        };

        $statusBookingMap = [
            'pending' => 'Menunggu',
            'confirmed' => 'Dikonfirmasi',
            'cancelled' => 'Dibatalkan',
        ];

        foreach ($bookings as $booking) {
            $dataRows[] = [
                $booking->kode_booking,
                $booking->user->nama_lengkap ?? '-',
                $booking->kamar->kosan->nama_kosan ?? '-',
                $booking->kamar->nomor_kamar ?? '-',
                \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($booking->tanggal_checkin),
                \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($booking->tanggal_checkout),
                $durasiLabel($booking->durasi),
                $booking->total_harga,
                $statusBookingMap[$booking->status_booking] ?? ucfirst($booking->status_booking),
                \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($booking->created_at),
            ];
        }
        $sheet->fromArray($dataRows, null, 'A4');
        $lastRow = 3 + count($dataRows);

        $sheet->getStyle('E4:E' . $lastRow)->getNumberFormat()->applyFromArray($styles['date']);
        $sheet->getStyle('F4:F' . $lastRow)->getNumberFormat()->applyFromArray($styles['date']);
        $sheet->getStyle('H4:H' . $lastRow)->getNumberFormat()->applyFromArray($styles['currency']);
        $sheet->getStyle('J4:J' . $lastRow)->getNumberFormat()->applyFromArray($styles['datetime']);
        $sheet->getStyle('A3:J' . $lastRow)->applyFromArray($styles['border']);

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->setHeaders($spreadsheet, 'Laporan_Booking_Admin_' . now()->format('Ymd_His') . '.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportBookingPdf(Request $request)
    {
        $data['bookings'] = $this->getBookingData($request);
        $data['title'] = 'Laporan Semua Booking (Admin)';
        $pdf = Pdf::loadView('admin.bookings.export_pdf', $data);
        return $pdf->setPaper('a4', 'landscape')->download('Laporan_Booking_Admin_' . date('Y-m-d') . '.pdf');
    }

    public function exportKosanExcel(Request $request)
    {
        $query = Kosan::with(['pemilik']);

        // Apply filters from request
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_kosan', 'LIKE', '%' . $request->search . '%');
        }
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status_validasi', $request->status);
        }
        if ($request->has('jenis_kos') && $request->jenis_kos != 'all') {
            $query->where('tipe_kosan', $request->jenis_kos);
        }
        if ($request->has('kota') && $request->kota != '') {
            $query->where('kota', $request->kota);
        }

        $kosans = $query->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Kosan');
        $styles = $this->getStyles();

        $sheet->mergeCells('A1:H1')->setCellValue('A1', 'DATA KOSAN');
        $sheet->getStyle('A1')->applyFromArray($styles['title'])->getAlignment()->setHorizontal('center');

        $headers = ['No', 'Nama Kosan', 'Pemilik', 'Jenis', 'Kota', 'Alamat', 'Rating', 'Status'];
        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle('A3:H3')->applyFromArray($styles['header']);

        $dataRows = [];
        $no = 1;
        foreach ($kosans as $kosan) {
            $dataRows[] = [
                $no++,
                $kosan->nama_kosan ?? '-',
                $kosan->pemilik->nama_lengkap ?? '-',
                ucfirst($kosan->tipe_kosan ?? '-'),
                $kosan->kota ?? '-',
                $kosan->alamat ?? '-',
                number_format($kosan->rating_rata ?? 0, 1),
                ucfirst($kosan->status_validasi ?? '-'),
            ];
        }
        $sheet->fromArray($dataRows, null, 'A4');
        $lastRow = 3 + count($dataRows);

        $sheet->getStyle('A3:H' . $lastRow)->applyFromArray($styles['border']);

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->setHeaders($spreadsheet, 'Data_Kosan_Admin_' . now()->format('Ymd_His') . '.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPembayaranExcel(Request $request)
    {
        $pembayaran = $this->getPembayaranData($request);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pembayaran');
        $styles = $this->getStyles();

        $sheet->mergeCells('A1:H1')->setCellValue('A1', 'LAPORAN SEMUA PEMBAYARAN');
        $sheet->getStyle('A1')->applyFromArray($styles['title'])->getAlignment()->setHorizontal('center');

        $headers = ['ID Pembayaran', 'Kode Booking', 'Pengguna', 'Jumlah', 'Metode', 'Status', 'Tipe', 'Tanggal'];
        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle('A3:H3')->applyFromArray($styles['header']);

        $dataRows = [];
        foreach ($pembayaran as $payment) {
            $dataRows[] = [
                $payment->pembayaran_id,
                $payment->booking->kode_booking ?? '-',
                $payment->booking->user->nama_lengkap ?? '-',
                $payment->jumlah_bayar,
                $payment->method_display_name,
                $payment->status_label,
                ucfirst($payment->tipe_pembayaran),
                \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($payment->created_at),
            ];
        }
        $sheet->fromArray($dataRows, null, 'A4');
        $lastRow = 3 + count($dataRows);

        $sheet->getStyle('D4:D'.$lastRow)->getNumberFormat()->applyFromArray($styles['currency']);
        $sheet->getStyle('H4:H'.$lastRow)->getNumberFormat()->applyFromArray($styles['datetime']);
        $sheet->getStyle('A3:H'.$lastRow)->applyFromArray($styles['border']);

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->setHeaders($spreadsheet, 'Laporan_Pembayaran_Admin_' . now()->format('Ymd_His') . '.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPembayaranPdf(Request $request)
    {
        $data['pembayaran'] = $this->getPembayaranData($request);
        $data['title'] = 'Laporan Pembayaran (Admin)';
        $pdf = Pdf::loadView('admin.pembayaran.export_pdf', $data);
        return $pdf->download('Laporan_Pembayaran_Admin_'.date('Y-m-d').'.pdf');
    }
}
