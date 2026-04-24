<?php

namespace App\Http\Controllers;

use App\Models\Kosan;
use App\Models\BookingKosan;
use App\Models\UlasanKosan;
use App\Services\KosanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KosanController extends Controller
{
    protected $kosanService;

    public function __construct(KosanService $kosanService)
    {
        $this->kosanService = $kosanService;
    }

    public function index(Request $request)
    {
        $kosans = $this->kosanService->getAllKosan($request);

        return view('users.kosan.index', [
            'kosans' => $kosans
        ]);
    }

    public function show($id)
    {
        $kosan = $this->kosanService->getKosanById($id);

        // Eager load all necessary relationships for the detail view
        $kosan->load([
            'fotos',
            'kamars.fotos',
            'kamars.fasilitas',
            'ulasanReview.user'
        ]);

        $kosan->kamars = $kosan->kamars->sortBy('nomor_kamar');

        $kosanSerupa = $this->kosanService->getKosanSerupa($kosan);

        $kamars = $kosan->kamars;

        $hasActiveBooking = false;
        $hasActiveBookingForThisKosan = false;
        $userHasReviewed = false;
        if (Auth::check()) {
            $userId = Auth::id();
            $hasActiveBooking = BookingKosan::where('user_id', $userId)
                ->whereIn('status_booking', ['pending', 'confirmed'])
                ->whereDate('tanggal_checkout', '>=', Carbon::now()->toDateString())
                ->exists();

            $hasActiveBookingForThisKosan = BookingKosan::where('user_id', $userId)
                ->where('status_booking', 'confirmed')
                ->whereDate('tanggal_checkout', '>=', Carbon::now()->toDateString())
                ->whereHas('kamar', function ($query) use ($kosan) {
                    $query->where('kosan_id', $kosan->kosan_id);
                })
                ->exists();

            $userHasReviewed = UlasanKosan::where('user_id', $userId)
                ->where('kosan_id', $kosan->kosan_id)
                ->exists();
        }

        return view('users.kosan.show', [
            'kosan' => $kosan,
            'kosanSerupa' => $kosanSerupa,
            'kamars' => $kamars,
            'hasActiveBooking' => $hasActiveBooking,
            'hasActiveBookingForThisKosan' => $hasActiveBookingForThisKosan,
            'userHasReviewed' => $userHasReviewed
        ]);
    }

    public function toggleFavorite($id)
    {
        $kosan = Kosan::findOrFail($id);
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Anda harus login untuk menambahkan favorit.'], 401);
        }

        $favorites = $kosan->favorit ?? [];

        if (in_array($userId, $favorites)) {
            $favorites = array_diff($favorites, [$userId]);
            $action = 'removed';
            $message = 'Kosan berhasil dihapus dari favorit';
        } else {
            $favorites[] = $userId;
            $action = 'added';
            $message = 'Kosan berhasil ditambahkan ke favorit';
        }

        $kosan->favorit = array_values($favorites);
        $kosan->save();

        return response()->json([
            'success' => true,
            'message' => $message,
            'action' => $action
        ]);
    }

    public function rateKosan(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $user = Auth::user();
        $kosan = Kosan::where('status_validasi', 'approved')->findOrFail($id);

        // Check if user has ever had a confirmed booking for this kosan
        $isEligible = BookingKosan::where('user_id', $user->user_id)
            ->where('status_booking', 'confirmed')
            ->whereHas('kamar', function ($query) use ($kosan) {
                $query->where('kosan_id', $kosan->kosan_id);
            })
            ->exists();

        if (!$isEligible) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus pernah menyewa kosan ini untuk memberikan rating.'
            ], 403);
        }

        UlasanKosan::updateOrCreate(
            [
                'kosan_id' => $kosan->kosan_id,
                'user_id' => $user->user_id,
            ],
            [
                'rating' => $request->rating,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih! Rating Anda telah disimpan.'
        ]);
    }

    public function favorites()
    {
        $favorites = $this->kosanService->getFavoritesByUser(Auth::id());

        return view('users.kosan.favorites', [
            'favorites' => $favorites
        ]);
    }

    public function bookingForm($id)
    {
        $kosan = $this->kosanService->getKosanById($id);

        if (Auth::check()) {
            $hasActive = BookingKosan::where('user_id', Auth::id())
                ->whereIn('status_booking', ['pending', 'confirmed'])
                ->whereDate('tanggal_checkout', '>=', Carbon::now()->toDateString())
                ->exists();
            if ($hasActive) {
                return redirect()->route('users.kosan.show', $id)
                    ->with('error', 'Anda sudah memiliki booking aktif. Selesaikan terlebih dahulu sebelum memesan kamar lain.');
            }
        }

        $kamarId = request()->input('kamar_id');
        $selectedKamar = null;
        if ($kamarId) {
            $kamar = \App\Models\Kamar::find($kamarId);
            if (!$kamar || $kamar->status_kamar !== 'tersedia') {
                return redirect()->route('users.kosan.show', $id)
                    ->with('error', 'Kamar yang dipilih tidak tersedia.');
            }
            $hasRoomActive = BookingKosan::where('kamar_id', $kamarId)
                ->where('status_booking', 'confirmed')
                ->whereDate('tanggal_checkout', '>=', Carbon::now()->toDateString())
                ->exists();
            if ($hasRoomActive) {
                return redirect()->route('users.kosan.show', $id)
                    ->with('error', 'Kamar ini sedang ditempati. Silakan pilih kamar lain.');
            }
            $selectedKamar = $kamar;
        }

        $durasi = request()->input('durasi', 'bulanan');
        $nilaiDurasi = (int) request()->input('nilai_durasi', 1);
        $jumlahKamar = (int) request()->input('jumlah_kamar', 1);
        $tanggalMulai = request()->input('tanggal_mulai', Carbon::now()->toDateString());

        $monthlyRoomPrice = (float) ($selectedKamar ? ($selectedKamar->harga_per_bulan ?? 0) : $kosan->getHargaBulananAttribute());

        return view('users.kosan.booking', [
            'kosan' => $kosan,
            'selectedKamar' => $selectedKamar,
            'monthlyRoomPrice' => $monthlyRoomPrice,
            'durasi' => $durasi,
            'nilaiDurasi' => $nilaiDurasi,
            'jumlahKamar' => $jumlahKamar,
            'tanggalMulai' => $tanggalMulai
        ]);
    }

    public function processBooking(Request $request, $id)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'jenis_durasi' => 'required|in:harian,mingguan,bulanan,tiga_bulan,semester,tahunan',
            'nilai_durasi' => 'required|integer|min:1',
            'jumlah_kamar' => 'required|integer|min:1|max:3',
            'kamar_id' => 'required|exists:kamar,kamar_id',
            'catatan' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:20',
        ], [
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi.',
            'tanggal_mulai.date' => 'Format tanggal tidak valid.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini.',
            'jenis_durasi.required' => 'Jenis durasi harus dipilih.',
            'jenis_durasi.in' => 'Jenis durasi tidak valid.',
            'nilai_durasi.required' => 'Nilai durasi harus diisi.',
            'nilai_durasi.integer' => 'Nilai durasi harus berupa angka.',
            'nilai_durasi.min' => 'Nilai durasi minimal 1.',
            'jumlah_kamar.required' => 'Jumlah kamar harus dipilih.',
            'jumlah_kamar.integer' => 'Jumlah kamar harus berupa angka.',
            'jumlah_kamar.min' => 'Minimal memesan 1 kamar.',
            'jumlah_kamar.max' => 'Maksimal pemesanan 3 kamar per pengguna.',
            'kamar_id.required' => 'Kamar harus dipilih.',
            'kamar_id.exists' => 'Kamar yang dipilih tidak valid.',
            'email.email' => 'Format email tidak valid.',
            'telepon.max' => 'Nomor telepon maksimal 20 karakter.',
        ]);

        // Penyesuaian batas nilai durasi untuk mode bulanan
        if ($request->jenis_durasi === 'bulanan' && (int) $request->nilai_durasi > 11) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Durasi bulanan maksimal 11 bulan. Untuk lebih lama, gunakan durasi tahunan.');
        }

        try {
            if (Auth::check()) {
                $hasActive = BookingKosan::where('user_id', Auth::id())
                    ->whereIn('status_booking', ['pending', 'confirmed'])
                    ->whereDate('tanggal_checkout', '>=', Carbon::now()->toDateString())
                    ->exists();
                if ($hasActive) {
                    return redirect()->route('users.kosan.show', $id)
                        ->with('error', 'Anda sudah memiliki booking aktif. Selesaikan terlebih dahulu sebelum memesan kamar lain.');
                }
            }

            $kamarId = (int) $request->input('kamar_id');
            $kamar = \App\Models\Kamar::find($kamarId);
            if (!$kamar || $kamar->status_kamar !== 'tersedia') {
                return redirect()->route('users.kosan.show', $id)
                    ->with('error', 'Kamar yang dipilih tidak tersedia.');
            }

            $start = Carbon::parse($request->tanggal_mulai);
            $end = (clone $start);
            $jenis = $request->jenis_durasi;
            $nilai = (int) $request->nilai_durasi;
            if ($jenis === 'harian') {
                $end->addDays($nilai);
            } elseif ($jenis === 'mingguan') {
                $end->addWeeks($nilai);
            } elseif ($jenis === 'bulanan') {
                $end->addMonthsNoOverflow($nilai);
            } elseif ($jenis === 'tiga_bulan') {
                $end->addMonthsNoOverflow(3);
            } elseif ($jenis === 'semester') {
                $end->addMonthsNoOverflow(6);
            } elseif ($jenis === 'tahunan') {
                $end->addYearsNoOverflow($nilai);
            }

            $conflict = BookingKosan::where('kamar_id', $kamarId)
                ->where('status_booking', 'confirmed')
                ->whereDate('tanggal_checkin', '<=', $end->toDateString())
                ->whereDate('tanggal_checkout', '>=', $start->toDateString())
                ->exists();
            if ($conflict) {
                return redirect()->route('users.kosan.show', $id)
                    ->with('error', 'Tanggal yang dipilih berbenturan dengan penyewa aktif kamar ini.');
            }

            $booking = $this->kosanService->createBooking($id, Auth::id(), $request->all());

            return redirect()->route('users.pembayaran.index', $booking->booking_id)
                ->with('success', 'Booking Anda berhasil dibuat! Silakan pilih metode pembayaran untuk menyelesaikan booking.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat membuat booking: ' . $e->getMessage());
        }
    }

    public function reviewForm($id)
    {
        $kosan = Kosan::where('status_validasi', 'approved')->findOrFail($id);
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $eligible = BookingKosan::where('user_id', Auth::id())
            ->where('status_booking', 'confirmed')
            ->whereHas('kamar', function ($query) use ($kosan) {
                $query->where('kosan_id', $kosan->kosan_id);
            })
            ->exists();
        if (!$eligible) {
            return redirect()->route('users.kosan.show', $id)
                ->with('error', 'Anda harus pernah menyewa kosan ini untuk memberikan ulasan.');
        }

        return view('users.kosan.review', [
            'kosan' => $kosan
        ]);
    }

    public function storeReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|min:10',
        ], [
            'rating.required' => 'Rating harus diisi.',
            'rating.integer' => 'Rating harus berupa angka.',
            'rating.min' => 'Rating minimal 1.',
            'rating.max' => 'Rating maksimal 5.',
            'komentar.required' => 'Komentar harus diisi.',
            'komentar.min' => 'Komentar minimal 10 karakter.',
        ]);

        $user = Auth::user();
        $kosan = Kosan::where('status_validasi', 'approved')->findOrFail($id);

        $eligible = BookingKosan::where('user_id', $user->user_id)
            ->where('status_booking', 'confirmed')
            ->whereHas('kamar', function ($query) use ($kosan) {
                $query->where('kosan_id', $kosan->kosan_id);
            })
            ->exists();

        if (!$eligible) {
            return redirect()->route('users.kosan.show', $id)
                ->with('error', 'Anda harus pernah menyewa kosan ini untuk memberikan ulasan.');
        }

        $existing = UlasanKosan::where('kosan_id', $kosan->kosan_id)
            ->where('user_id', $user->user_id)
            ->first();

        if ($existing) {
            $existing->update([
                'rating' => $request->rating,
                'komentar' => $request->komentar,
            ]);
        } else {
            UlasanKosan::create([
                'kosan_id' => $kosan->kosan_id,
                'user_id' => $user->user_id,
                'rating' => $request->rating,
                'komentar' => $request->komentar
            ]);
        }

        return redirect()->route('users.kosan.show', $id)
            ->with('success', 'Terima kasih atas ulasan Anda!');
    }

    public function search(Request $request)
    {
        $kosans = $this->kosanService->searchKosan($request);

        return view('users.kosan.search', [
            'kosans' => $kosans
        ]);
    }

    public function nearby(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $radius = $request->input('radius', 5); // radius dalam km, default 5km

        if (!$latitude || !$longitude) {
            return view('users.kosan.nearby', [
                'nearbyKosans' => [],
                'latitude' => null,
                'longitude' => null,
                'radius' => $radius
            ]);
        }

        $nearbyKosans = $this->kosanService->getNearbyKosan($latitude, $longitude, $radius);

        return view('users.kosan.nearby', [
            'nearbyKosans' => $nearbyKosans,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'radius' => $radius
        ]);
    }

    public function getNearbyKosan(Request $request)
    {
        $lat = (float) $request->input('lat');
        $lng = (float) $request->input('lng');
        $radius = (int) $request->input('radius', 5);

        if (!$lat || !$lng) {
            return response()->json([]);
        }

        try {
            $results = $this->kosanService->getNearbyKosan($lat, $lng, $radius);

            $payload = $results->map(function ($k) {
                $hargaBulanan = (float) ($k->harga_bulanan ?? 0.0);
                return [
                    'kosan_id' => $k->kosan_id,
                    'nama_kos' => $k->nama_kosan,
                    'alamat' => $k->alamat,
                    'harga_bulanan' => $hargaBulanan,
                    'latitude' => (float) $k->latitude,
                    'longitude' => (float) $k->longitude,
                    'foto_kosan' => $k->foto_kosan,
                    'distance' => isset($k->distance) ? (float) $k->distance : null,
                    'distance_text' => isset($k->distance) ? $this->formatDistance($k->distance) : null,
                ];
            });

            return response()->json($payload);
        } catch (\Throwable $e) {
            return response()->json([]);
        }
    }

    public function availability(Request $request, $id)
    {
        try {
            $start = Carbon::parse($request->input('start', now()->toDateString()));
            $type = $request->input('duration_type', 'bulanan');
            $value = (int) $request->input('duration_value', 1);

            $end = null;
            if ($type === 'harian') {
                $end = (clone $start)->addDays(max(1, $value));
            } elseif ($type === 'mingguan') {
                $end = (clone $start)->addWeeks(max(1, $value));
            } elseif ($type === 'tahunan') {
                $end = (clone $start)->addMonths(12 * max(1, $value));
            } else {
                $end = (clone $start)->addMonths(max(1, $value));
            }

            $data = $this->kosanService->getAvailabilityRange($id, $start, $end);
            return response()->json($data);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Gagal memeriksa ketersediaan',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                'available_count' => 0,
                'available_rooms' => [],
                'status' => 'error'
            ], 500);
        }
    }

    private function formatDistance($distance)
    {
        if ($distance < 1) {
            // Konversi ke meter
            $meters = round($distance * 1000);
            return "{$meters} meter";
        } else {
            // Tampilkan dalam km dengan 1 desimal
            return round($distance, 1) . " km";
        }
    }
}
