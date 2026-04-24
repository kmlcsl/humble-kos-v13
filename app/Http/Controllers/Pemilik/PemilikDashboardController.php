<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Kosan;
use App\Models\BookingKosan;
use App\Models\Pembayaran;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemilikDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'pemilik_kos') {
                return redirect()->route('root')->with('error', 'Akses ditolak.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $user = Auth::user();
        $ownerId = $user->user_id;

        // Ambil semua ID kosan milik owner
        $kosanIds = Kosan::where('owner_id', $ownerId)->pluck('kosan_id');

        // Statistik Kos
        $totalKos = $kosanIds->count();
        $kosDisetujui = Kosan::whereIn('kosan_id', $kosanIds)->where('status_validasi', 'approved')->count();
        $kosMenungguValidasi = Kosan::whereIn('kosan_id', $kosanIds)->where('status_validasi', 'pending')->count();
        $kosDitolak = Kosan::whereIn('kosan_id', $kosanIds)->where('status_validasi', 'rejected')->count();

        // Statistik Kamar
        $totalKamar = Kamar::whereIn('kosan_id', $kosanIds)->count();
        $kamarTerisi = Kamar::whereIn('kosan_id', $kosanIds)->where('status_kamar', 'terisi')->count();
        $kamarKosong = $totalKamar - $kamarTerisi;
        $tingkatOkupansi = $totalKamar > 0 ? round(($kamarTerisi / $totalKamar) * 100, 2) : 0;

        // Statistik Pendapatan
        $totalPendapatan = Pembayaran::whereIn('booking_id', function ($query) use ($kosanIds) {
            $query->select('booking_id')->from('booking')->whereIn('kamar_id', function ($subQuery) use ($kosanIds) {
                $subQuery->select('kamar_id')->from('kamar')->whereIn('kosan_id', $kosanIds);
            });
        })
            ->where('status_pembayaran', 'paid')->sum('jumlah_bayar');

        $pendapatanBulanIni = Pembayaran::whereIn('booking_id', function ($query) use ($kosanIds) {
            $query->select('booking_id')->from('booking')->whereIn('kamar_id', function ($subQuery) use ($kosanIds) {
                $subQuery->select('kamar_id')->from('kamar')->whereIn('kosan_id', $kosanIds);
            });
        })
            ->where('status_pembayaran', 'paid')
            ->whereYear('tanggal_bayar', now()->year)
            ->whereMonth('tanggal_bayar', now()->month)
            ->sum('jumlah_bayar');

        // Statistik Booking
        $bookingsQuery = BookingKosan::whereIn('kamar_id', function ($query) use ($kosanIds) {
            $query->select('kamar_id')->from('kamar')->whereIn('kosan_id', $kosanIds);
        });

        $totalBooking = $bookingsQuery->count();
        $bookingPending = $bookingsQuery->clone()->where('status_booking', 'pending')->count();
        $bookingConfirmed = $bookingsQuery->clone()->where('status_booking', 'confirmed')->count();
        $bookingCancelled = $bookingsQuery->clone()->where('status_booking', 'cancelled')->count();

        // Statistik Pembayaran
        $pembayaranQuery = Pembayaran::whereIn('booking_id', function ($query) use ($kosanIds) {
            $query->select('booking_id')->from('booking')->whereIn('kamar_id', function ($subQuery) use ($kosanIds) {
                $subQuery->select('kamar_id')->from('kamar')->whereIn('kosan_id', $kosanIds);
            });
        });

        $totalPembayaran = $pembayaranQuery->count();
        $pembayaranPending = $pembayaranQuery->clone()->where('status_pembayaran', 'pending')->count();
        $pembayaranSukses = $pembayaranQuery->clone()->where('status_pembayaran', 'paid')->count();
        $pembayaranGagal = $pembayaranQuery->clone()->whereIn('status_pembayaran', ['failed', 'expired'])->count();

        // Data untuk Grafik Pendapatan (6 bulan terakhir)
        $chartData = [
            'labels' => [],
            'data' => [],
        ];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartData['labels'][] = $month->format('M Y');
            $monthlyRevenue = Pembayaran::whereIn('booking_id', function ($query) use ($kosanIds) {
                $query->select('booking_id')->from('booking')->whereIn('kamar_id', function ($subQuery) use ($kosanIds) {
                    $subQuery->select('kamar_id')->from('kamar')->whereIn('kosan_id', $kosanIds);
                });
            })
                ->where('status_pembayaran', 'paid')
                ->whereYear('tanggal_bayar', $month->year)
                ->whereMonth('tanggal_bayar', $month->month)
                ->sum('jumlah_bayar');
            $chartData['data'][] = $monthlyRevenue;
        }

        // Booking Terbaru
        $recentBookings = BookingKosan::with(['user', 'kamar.kosan'])
            ->whereIn('kamar_id', function ($query) use ($kosanIds) {
                $query->select('kamar_id')->from('kamar')->whereIn('kosan_id', $kosanIds);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('pemilik.dashboard', [
            'totalKos' => $totalKos,
            'kosDisetujui' => $kosDisetujui,
            'kosMenungguValidasi' => $kosMenungguValidasi,
            'kosDitolak' => $kosDitolak,
            'totalKamar' => $totalKamar,
            'kamarTerisi' => $kamarTerisi,
            'kamarKosong' => $kamarKosong,
            'tingkatOkupansi' => $tingkatOkupansi,
            'totalPendapatan' => $totalPendapatan,
            'pendapatanBulanIni' => $pendapatanBulanIni,
            'totalBooking' => $totalBooking,
            'bookingPending' => $bookingPending,
            'bookingConfirmed' => $bookingConfirmed,
            'bookingCancelled' => $bookingCancelled,
            'totalPembayaran' => $totalPembayaran,
            'pembayaranPending' => $pembayaranPending,
            'pembayaranSukses' => $pembayaranSukses,
            'pembayaranGagal' => $pembayaranGagal,
            'chartData' => $chartData,
            'recentBookings' => $recentBookings,
        ]);
    }

    public function saveUserLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $request->session()->put('user_latitude', $request->latitude);
        $request->session()->put('user_longitude', $request->longitude);

        return response()->json([
            'success' => true,
            'message' => 'Lokasi berhasil disimpan'
        ]);
    }
}