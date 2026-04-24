<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Kosan;
use App\Models\Kamar;
use App\Models\BookingKosan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PemilikLaporanController extends Controller
{
    /**
     * Get helper methods for owned properties
     */
    private function getOwnedKosanIds()
    {
        return Kosan::where('owner_id', Auth::id())->pluck('kosan_id');
    }

    private function getOwnedKamarIds()
    {
        return Kamar::whereIn('kosan_id', $this->getOwnedKosanIds())->pluck('kamar_id');
    }

    private function getOwnedBookingIds()
    {
        return BookingKosan::whereIn('kamar_id', $this->getOwnedKamarIds())->pluck('booking_id');
    }

    /**
     * Display main laporan page with overview
     */
    public function index()
    {
        $kosanIds = $this->getOwnedKosanIds();
        $kamarIds = $this->getOwnedKamarIds();
        $bookingIds = $this->getOwnedBookingIds();

        // Top-level stats
        $totalKosan = $kosanIds->count();
        $totalBookings = $bookingIds->count();
        $totalUsers = BookingKosan::whereIn('booking_id', $bookingIds)->distinct('user_id')->count('user_id');
        $totalRevenue = Pembayaran::whereIn('booking_id', $bookingIds)->where('status_pembayaran', 'paid')->sum('jumlah_bayar');

        // Booking status breakdown
        $pendingBookings = BookingKosan::whereIn('kamar_id', $kamarIds)->where('status_booking', 'pending')->count();
        $confirmedBookings = BookingKosan::whereIn('kamar_id', $kamarIds)->where('status_booking', 'confirmed')->count();
        $cancelledBookings = BookingKosan::whereIn('kamar_id', $kamarIds)->where('status_booking', 'cancelled')->count();

        // Payment status
        $pendingPayments = Pembayaran::whereIn('booking_id', $bookingIds)->whereIn('status_pembayaran', ['pending', 'processing'])->count();

        // Last 6 months revenue
        $monthlyRevenue = Pembayaran::whereIn('booking_id', $bookingIds)
            ->where('status_pembayaran', 'paid')
            ->where('tanggal_bayar', '>=', Carbon::now()->subMonths(6))
            ->select(DB::raw('YEAR(tanggal_bayar) year, MONTH(tanggal_bayar) month, SUM(jumlah_bayar) total'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Recent bookings
        $recentBookings = BookingKosan::with(['kamar.kosan'])
            ->whereIn('kamar_id', $kamarIds)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('pemilik.laporan.index', [
            'totalBookings' => $totalBookings,
            'totalRevenue' => $totalRevenue,
            'totalKosan' => $totalKosan,
            'totalUsers' => $totalUsers,
            'pendingBookings' => $pendingBookings,
            'confirmedBookings' => $confirmedBookings,
            'cancelledBookings' => $cancelledBookings,
            'pendingPayments' => $pendingPayments,
            'monthlyRevenue' => $monthlyRevenue,
            'recentBookings' => $recentBookings
        ]);
    }

    /**
     * Display occupancy report (Laporan Okupansi)
     */
    public function okupansi(Request $request)
    {
        $user = Auth::user();
        $kosanIds = $this->getOwnedKosanIds();

        // Date range filter
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        // Get occupancy data per kosan
        $okupansiData = Kosan::whereIn('kosan_id', $kosanIds)
            ->with(['kamars' => function($query) {
                $query->select('kosan_id', 'kamar_id', 'status_kamar');
            }])
            ->get()
            ->map(function($kosan) {
                $totalKamar = $kosan->kamars->count();
                $kamarTerisi = $kosan->kamars->where('status_kamar', 'terisi')->count();
                $kamarKosong = $kosan->kamars->where('status_kamar', 'tersedia')->count();
                $tingkatOkupansi = $totalKamar > 0 ? round(($kamarTerisi / $totalKamar) * 100, 2) : 0;

                return [
                    'kosan' => $kosan,
                    'total_kamar' => $totalKamar,
                    'kamar_terisi' => $kamarTerisi,
                    'kamar_kosong' => $kamarKosong,
                    'tingkat_okupansi' => $tingkatOkupansi,
                ];
            });

        // Overall statistics
        $totalKamarSemua = $okupansiData->sum('total_kamar');
        $totalKamarTerisi = $okupansiData->sum('kamar_terisi');
        $rataRataOkupansi = $totalKamarSemua > 0 ? round(($totalKamarTerisi / $totalKamarSemua) * 100, 2) : 0;

        return view('pemilik.laporan.okupansi', [
            'okupansiData' => $okupansiData,
            'totalKamarSemua' => $totalKamarSemua,
            'totalKamarTerisi' => $totalKamarTerisi,
            'rataRataOkupansi' => $rataRataOkupansi,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);
    }

    /**
     * Display revenue report (Laporan Pendapatan)
     */
    public function pendapatan(Request $request)
    {
        $bookingIds = $this->getOwnedBookingIds();

        // Date range filter
        $periode = $request->get('periode', 'bulan'); // bulan, tahun, custom
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);
        $tanggalDari = $request->get('tanggal_dari');
        $tanggalSampai = $request->get('tanggal_sampai');

        $query = Pembayaran::whereIn('booking_id', $bookingIds)
            ->where('status_pembayaran', 'paid')
            ->with('booking.kamar.kosan');

        // Apply date filters
        if ($periode == 'bulan') {
            $query->whereYear('tanggal_bayar', $tahun)
                  ->whereMonth('tanggal_bayar', $bulan);
        } elseif ($periode == 'tahun') {
            $query->whereYear('tanggal_bayar', $tahun);
        } elseif ($periode == 'custom' && $tanggalDari && $tanggalSampai) {
            $query->whereBetween('tanggal_bayar', [$tanggalDari, $tanggalSampai]);
        }

        $pembayaranList = $query->orderBy('tanggal_bayar', 'desc')->get();

        // Calculate totals
        $totalPendapatan = $pembayaranList->sum('jumlah_bayar');
        $totalTransaksi = $pembayaranList->count();

        // Group by kosan
        $pendapatanPerKosan = $pembayaranList->groupBy(function($item) {
            return $item->booking->kamar->kosan->kosan_id;
        })->map(function($group, $kosanId) {
            $kosan = $group->first()->booking->kamar->kosan;
            return [
                'kosan' => $kosan,
                'total_pendapatan' => $group->sum('jumlah_bayar'),
                'jumlah_transaksi' => $group->count(),
            ];
        });

        // Monthly chart data
        if ($periode == 'tahun') {
            $chartData = $this->getMonthlyRevenueData($bookingIds, $tahun);
        } else {
            $chartData = null;
        }

        return view('pemilik.laporan.pendapatan', [
            'totalPendapatan' => $totalPendapatan,
            'totalTransaksi' => $totalTransaksi,
            'pendapatanPerKosan' => $pendapatanPerKosan,
            'pembayaranList' => $pembayaranList,
            'periode' => $periode,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'tanggalDari' => $tanggalDari,
            'tanggalSampai' => $tanggalSampai,
            'chartData' => $chartData
        ]);
    }

    /**
     * Display transaction history (Riwayat Transaksi)
     */
    public function transaksi(Request $request)
    {
        $bookingIds = $this->getOwnedBookingIds();

        $query = Pembayaran::whereIn('booking_id', $bookingIds)
            ->with(['booking.kamar.kosan', 'booking.user']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status_pembayaran', $request->status);
        }

        // Filter by payment method
        if ($request->has('metode') && $request->metode != '') {
            $query->where('metode_pembayaran', $request->metode);
        }

        // Date range
        if ($request->has('tanggal_dari') && $request->tanggal_dari != '') {
            $query->whereDate('tanggal_bayar', '>=', $request->tanggal_dari);
        }
        if ($request->has('tanggal_sampai') && $request->tanggal_sampai != '') {
            $query->whereDate('tanggal_bayar', '<=', $request->tanggal_sampai);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('transaction_id', 'LIKE', '%' . $request->search . '%')
                  ->orWhereHas('booking', function($bookingQuery) use ($request) {
                      $bookingQuery->where('kode_booking', 'LIKE', '%' . $request->search . '%');
                  });
            });
        }

        $transaksiList = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $totalTransaksi = Pembayaran::whereIn('booking_id', $bookingIds)->count();
        $transaksiSukses = Pembayaran::whereIn('booking_id', $bookingIds)
            ->where('status_pembayaran', 'paid')
            ->count();
        $transaksiPending = Pembayaran::whereIn('booking_id', $bookingIds)
            ->whereIn('status_pembayaran', ['pending', 'processing'])
            ->count();

        return view('pemilik.laporan.transaksi', [
            'transaksiList' => $transaksiList,
            'totalTransaksi' => $totalTransaksi,
            'transaksiSukses' => $transaksiSukses,
            'transaksiPending' => $transaksiPending
        ]);
    }

    /**
     * Export laporan to Excel
     */
    public function export(Request $request, $type)
    {
        $bookingIds = $this->getOwnedBookingIds();

        switch ($type) {
            case 'okupansi':
                return $this->exportOkupansi();
            case 'pendapatan':
                return $this->exportPendapatan($request);
            case 'transaksi':
                return $this->exportTransaksi($request);
            default:
                return back()->with('error', 'Tipe laporan tidak valid.');
        }
    }

    /**
     * Get monthly revenue data for chart
     */
    private function getMonthlyRevenueData($bookingIds, $tahun)
    {
        $months = [];
        $revenues = [];

        for ($m = 1; $m <= 12; $m++) {
            $revenue = Pembayaran::whereIn('booking_id', $bookingIds)
                ->where('status_pembayaran', 'paid')
                ->whereYear('tanggal_bayar', $tahun)
                ->whereMonth('tanggal_bayar', $m)
                ->sum('jumlah_bayar');

            $months[] = Carbon::create()->month($m)->format('M');
            $revenues[] = $revenue;
        }

        return [
            'labels' => $months,
            'data' => $revenues
        ];
    }

    /**
     * Export methods (simplified - would use Laravel Excel in production)
     */
    private function exportOkupansi()
    {
        // Implementation with CSV or Excel export
        return back()->with('info', 'Fitur export sedang dalam pengembangan.');
    }

    private function exportPendapatan($request)
    {
        // Implementation with CSV or Excel export
        return back()->with('info', 'Fitur export sedang dalam pengembangan.');
    }

    private function exportTransaksi($request)
    {
        // Implementation with CSV or Excel export
        return back()->with('info', 'Fitur export sedang dalam pengembangan.');
    }
}
