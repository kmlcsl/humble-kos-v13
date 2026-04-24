<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembayaran::query();

        // Filter berdasarkan status
        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            // Map user-friendly status to database status
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

        // Urutkan
        $sortField = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        // Eager load booking and pengguna without custom selects to match current schema
        $query->with(['booking', 'booking.user']);

        // Get data with pagination
        $pembayaran = $query->paginate(10);

        // Calculate stats
        $stats = [
            'total_pembayaran' => Pembayaran::count(),
            'pending_pembayaran' => Pembayaran::where('status_pembayaran', 'pending')->count(),
            'processing_pembayaran' => Pembayaran::where('status_pembayaran', 'pending')->count(), // pending = processing/menunggu verifikasi
            'paid_pembayaran' => Pembayaran::where('status_pembayaran', 'paid')->count(),
            'successful_pembayaran' => Pembayaran::where('status_pembayaran', 'paid')->count(), // paid = successful/sukses
            'failed_pembayaran' => Pembayaran::where('status_pembayaran', 'failed')->count(),
            'expired_pembayaran' => Pembayaran::where('status_pembayaran', 'expired')->count(),
            'total_pendapatan' => Pembayaran::where('status_pembayaran', 'paid')->sum('jumlah_bayar'),
        ];

        return view('admin.pembayaran.index', [
            'pembayaran' => $pembayaran,
            'stats' => $stats,
        ]);
    }

    /**
     * Menampilkan daftar pembayaran yang menunggu verifikasi admin
     */
    public function pending()
    {
        $pembayaran = Pembayaran::with([
            'booking',
            'booking.user'
        ])
            ->where('status_pembayaran', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total_processing' => Pembayaran::where('status_pembayaran', 'pending')->count(),
            'processing_today' => Pembayaran::where('status_pembayaran', 'pending')
                ->whereDate('created_at', Carbon::today())->count(),
            'processing_yesterday' => Pembayaran::where('status_pembayaran', 'pending')
                ->whereDate('created_at', Carbon::yesterday())->count(),
            'processing_this_month' => Pembayaran::where('status_pembayaran', 'pending')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
        ];

        return view('admin.pembayaran.pending', [
            'pembayaran' => $pembayaran,
            'stats' => $stats,
        ]);
    }

    /**
     * Menampilkan daftar pembayaran yang sukses
     */
    public function successful(Request $request)
    {
        $query = Pembayaran::with([
            'booking',
            'booking.user'
        ])
            ->where('status_pembayaran', 'paid');

        // Filter berdasarkan metode pembayaran
        if ($request->has('metode') && !empty($request->metode) && $request->metode !== 'all') {
            $query->where('metode_pembayaran', $request->metode);
        }

        // Filter berdasarkan tanggal
        if ($request->has('date_start') && !empty($request->date_start)) {
            $query->whereDate('updated_at', '>=', $request->date_start);
        }

        if ($request->has('date_end') && !empty($request->date_end)) {
            $query->whereDate('updated_at', '<=', $request->date_end);
        }
            
        $pembayaran = $query->orderBy('updated_at', 'desc')->paginate(10);

        $stats = [
            'total_successful' => Pembayaran::where('status_pembayaran', 'paid')->count(),
            'successful_today' => Pembayaran::where('status_pembayaran', 'paid')
                ->whereDate('updated_at', Carbon::today())->count(),
            'successful_yesterday' => Pembayaran::where('status_pembayaran', 'paid')
                ->whereDate('updated_at', Carbon::yesterday())->count(),
            'revenue_this_month' => Pembayaran::where('status_pembayaran', 'paid')
                ->whereMonth('updated_at', Carbon::now()->month)
                ->whereYear('updated_at', Carbon::now()->year)
                ->sum('jumlah_bayar'),
        ];

        return view('admin.pembayaran.successful', [
            'pembayaran' => $pembayaran,
            'stats' => $stats,
        ]);
    }

    /**
     * Menampilkan daftar pembayaran yang kedaluwarsa
     */
    public function expired()
    {
        $pembayaran = Pembayaran::with([
            'booking',
            'booking.user'
        ])
            ->where('status_pembayaran', 'expired')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.pembayaran.expired', [
            'pembayaran' => $pembayaran,
        ]);
    }

    /**
     * Menampilkan detail pembayaran
     */
    public function show($id)
    {
        $pembayaran = Pembayaran::with([
            'booking',
            'booking.user'
        ])
            ->findOrFail($id);

        return view('admin.pembayaran.show', [
            'pembayaran' => $pembayaran,
        ]);
    }

    /**
     * Menyetujui pembayaran
     */
    public function approve($id)
    {
        DB::beginTransaction();

        try {
            $pembayaran = Pembayaran::findOrFail($id);

            // Hanya bisa menyetujui pembayaran dalam status processing
            if ($pembayaran->status_pembayaran !== 'pending') {
                return response()->json(['success' => false, 'message' => 'Hanya pembayaran dalam status Pending yang dapat disetujui.'], 422);
            }

            // Ubah status pembayaran menjadi paid
            $pembayaran->status_pembayaran = 'paid';
            $pembayaran->tanggal_bayar = now();
            $pembayaran->save();

            // Update status booking
            $booking = $pembayaran->booking;
            $booking->status_booking = 'confirmed';
            $booking->save();

            // Update status kamar menjadi 'terisi' bila sebelumnya 'tersedia'
            $kamar = $booking->kamar;
            if ($kamar && $kamar->status_kamar === 'tersedia') {
                $kamar->status_kamar = 'terisi';
                $kamar->save();
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Pembayaran berhasil disetujui.']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyetujui pembayaran: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Menolak pembayaran
     */
    public function reject($id)
    {
        DB::beginTransaction();

        try {
            $pembayaran = Pembayaran::findOrFail($id);

            // Hanya bisa menolak pembayaran dalam status processing
            if ($pembayaran->status_pembayaran !== 'pending') {
                return response()->json(['success' => false, 'message' => 'Hanya pembayaran dalam status Pending yang dapat ditolak.'], 422);
            }

            // Ubah status pembayaran menjadi failed
            $pembayaran->status_pembayaran = 'failed';
            $pembayaran->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Pembayaran telah ditolak.']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menolak pembayaran: ' . $e->getMessage()], 500);
        }
    }
}
