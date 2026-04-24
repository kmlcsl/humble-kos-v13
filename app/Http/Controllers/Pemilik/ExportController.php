<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\BookingKosan;
use App\Models\Kosan;
use App\Models\Kamar;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    // --- STYLING & HELPERS (Existing code) ... ---
    private function getStyles(): array
    {
        return [
            'title' => ['font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF4F4F4F']]],
            'header' => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4E73DF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            'subheader' => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9E1F2']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            'border' => [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD0D0D0']]],
            ],
            'total' => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE8E8E8']],
            ],
            'currency' => ['formatCode' => '"Rp" #,##0'],
            'date' => ['formatCode' => 'dd/mm/yyyy'],
            'datetime' => ['formatCode' => 'dd/mm/yyyy hh:mm'],
            'percentage' => ['formatCode' => '0.00"%"'],
        ];
    }

    private function setHeaders(Spreadsheet &$spreadsheet, string $filename)
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
    }
    
    // --- DATA FETCHING (Existing and new) ---
    private function getOwnedKamarIds() {
        return Kamar::whereIn('kosan_id', Kosan::where('owner_id', Auth::id())->pluck('kosan_id'))->pluck('kamar_id');
    }

    // ... Other existing data fetching methods
    private function getOwnedBookingIds() {
        return BookingKosan::whereIn('kamar_id', $this->getOwnedKamarIds())->pluck('booking_id');
    }
    private function getLaporanData() {
        // ... (code from previous turn)
    }
    private function getOkupansiData() {
        // ... (code from previous turn)
    }
    private function getPendapatanData(Request $request) {
        // ... (code from previous turn)
    }
    private function getTransaksiData(Request $request) {
        // ... (code from previous turn)
    }

    private function getPembayaranData(Request $request)
    {
        $bookingIds = $this->getOwnedBookingIds();
        $query = Pembayaran::with(['booking.user'])
            ->whereIn('booking_id', $bookingIds);

        // Filters from the index page
        if ($request->has('status') && $request->status != '' && $request->status != 'all') {
            $statusMap = [
                'successful' => 'paid',
                'processing' => 'pending',
            ];
            $dbStatus = $statusMap[$request->status] ?? $request->status;
            $query->where('status_pembayaran', $dbStatus);
        }
        if ($request->has('metode') && $request->metode != '') {
            $query->where('metode_pembayaran', $request->metode);
        }
        if ($request->has('tanggal_dari') && $request->tanggal_dari != '') {
            $query->whereDate('tanggal_bayar', '>=', $request->tanggal_dari);
        }
        if ($request->has('tanggal_sampai') && $request->tanggal_sampai != '') {
            $query->whereDate('tanggal_bayar', '<=', $request->tanggal_sampai);
        }
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('transaction_id', 'LIKE', '%' . $request->search . '%')
                    ->orWhereHas('booking', function ($bookingQuery) use ($request) {
                        $bookingQuery->where('kode_booking', 'LIKE', '%' . $request->search . '%');
                    });
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
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

    // --- EXPORT METHODS (Implemented) ---
    public function exportBookingExcel(Request $request)
    {
        $bookings = $this->getBookingData($request);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Booking');
        $styles = $this->getStyles();

        $sheet->mergeCells('A1:I1')->setCellValue('A1', 'LAPORAN SEMUA BOOKING');
        $sheet->getStyle('A1')->applyFromArray($styles['title'])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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

        $sheet->getStyle('E4:E'.$lastRow)->getNumberFormat()->applyFromArray($styles['date']);
        $sheet->getStyle('F4:F'.$lastRow)->getNumberFormat()->applyFromArray($styles['date']);
        $sheet->getStyle('H4:H'.$lastRow)->getNumberFormat()->applyFromArray($styles['currency']);
        $sheet->getStyle('J4:J'.$lastRow)->getNumberFormat()->applyFromArray($styles['datetime']);
        $sheet->getStyle('A3:J'.$lastRow)->applyFromArray($styles['border']);

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->setHeaders($spreadsheet, 'Laporan_Booking_Pemilik_' . now()->format('Ymd_His') . '.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportBookingPdf(Request $request)
    {
        $data['bookings'] = $this->getBookingData($request);
        $data['title'] = 'Laporan Semua Booking';
        $pdf = Pdf::loadView('pemilik.bookings.export_pdf', $data);
        return $pdf->setPaper('a4', 'landscape')->download('Laporan_Booking_Pemilik_'.date('Y-m-d').'.pdf');
    }

    public function exportKosanExcel(Request $request)
    {
        $query = Kosan::where('owner_id', Auth::id());

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

        $kosans = $query->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Kosan');
        $styles = $this->getStyles();

        $sheet->mergeCells('A1:G1')->setCellValue('A1', 'DATA KOSAN SAYA');
        $sheet->getStyle('A1')->applyFromArray($styles['title'])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = ['No', 'Nama Kosan', 'Jenis', 'Kota', 'Alamat', 'Rating', 'Status'];
        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle('A3:G3')->applyFromArray($styles['header']);

        $dataRows = [];
        $no = 1;
        foreach ($kosans as $kosan) {
            $dataRows[] = [
                $no++,
                $kosan->nama_kosan ?? '-',
                ucfirst($kosan->tipe_kosan ?? '-'),
                $kosan->kota ?? '-',
                $kosan->alamat ?? '-',
                number_format($kosan->rating_rata ?? 0, 1),
                ucfirst($kosan->status_validasi ?? '-'),
            ];
        }
        $sheet->fromArray($dataRows, null, 'A4');
        $lastRow = 3 + count($dataRows);

        $sheet->getStyle('A3:G'.$lastRow)->applyFromArray($styles['border']);

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->setHeaders($spreadsheet, 'Data_Kosan_Pemilik_' . now()->format('Ymd_His') . '.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // --- Other Export Methods (as implemented before) ---
    public function exportOkupansiExcel() { /* ... */ }
    public function exportOkupansiPdf() { /* ... */ }
    public function exportPendapatanExcel(Request $request) { /* ... */ }
    public function exportPendapatanPdf(Request $request) { /* ... */ }
    public function exportTransaksiExcel(Request $request) { /* ... */ }
    public function exportTransaksiPdf(Request $request) { /* ... */ }
    public function exportLaporanExcel(Request $request) { /* ... */ }
    public function exportLaporanPdf(Request $request) { /* ... */ }
    public function exportPembayaranExcel(Request $request)
    {
        $pembayaran = $this->getPembayaranData($request);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pembayaran');
        $styles = $this->getStyles();

        $sheet->mergeCells('A1:H1')->setCellValue('A1', 'LAPORAN SEMUA PEMBAYARAN');
        $sheet->getStyle('A1')->applyFromArray($styles['title'])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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

        $this->setHeaders($spreadsheet, 'Laporan_Pembayaran_Pemilik_' . now()->format('Ymd_His') . '.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPembayaranPdf(Request $request)
    {
        $data['pembayaran'] = $this->getPembayaranData($request);
        $data['title'] = 'Laporan Pembayaran';
        $pdf = Pdf::loadView('pemilik.pembayaran.export_pdf', $data);
        return $pdf->download('Laporan_Pembayaran_Pemilik_'.date('Y-m-d').'.pdf');
    }
}
