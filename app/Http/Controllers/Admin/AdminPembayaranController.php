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

        // Filter status
        if ($request->has('status') && $request->filled('status') && $request->input('status') !== 'all') {
            $statusMap = [
                'successful' => Pembayaran::STATUS_PAID,
                'processing' => Pembayaran::STATUS_PENDING,
            ];
            $dbStatus = $statusMap[$request->input('status')] ?? $request->input('status');
            $query->where('status_pembayaran', $dbStatus);
        }

        // Filter metode
        if ($request->has('metode') && $request->filled('metode') && $request->input('metode') !== 'all') {
            $query->where('metode_pembayaran', $request->input('metode'));
        }

        // Filter tanggal
        if ($request->has('date_start') && $request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->input('date_start'));
        }

        if ($request->has('date_end') && $request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->input('date_end'));
        }

        // Pengurutan
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Load relasi
        $query->with(['booking.user']);

        $pembayaran = $query->paginate(10);

        // Statistik
        $stats = [
            'total_pembayaran' => Pembayaran::query()->count(),
            'pending_pembayaran' => Pembayaran::query()->where('status_pembayaran', Pembayaran::STATUS_PENDING)->count(),
            'paid_pembayaran' => Pembayaran::query()->where('status_pembayaran', Pembayaran::STATUS_PAID)->count(),
            'failed_pembayaran' => Pembayaran::query()->where('status_pembayaran', Pembayaran::STATUS_FAILED)->count(),
            'expired_pembayaran' => Pembayaran::query()->where('status_pembayaran', Pembayaran::STATUS_EXPIRED)->count(),
            'total_pendapatan' => Pembayaran::query()->where('status_pembayaran', Pembayaran::STATUS_PAID)->sum('jumlah_bayar'),
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
    public function show(int $id)
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
    public function approve(int $id)
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
    public function reject(int $id)
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
