<div class="nav-section">
    <h6 class="nav-section-title">Transaksi</h6>
    <ul class="nav-items">
        <li class="nav-item {{ request()->routeIs('payments.*') || request()->routeIs('user.pembayaran.*') ? 'active' : '' }}">
            @php
                if (($role ?? null) === 'pemilik_kos') {
                    $pendingBooking = App\Models\BookingKosan::whereHas('kosan', function ($query) {
                        $query->where('id_pemilik', Auth::id());
                    })
                        ->where('status_booking', 'pending')
                        ->latest()
                        ->first();
                    $pendingCount = App\Models\BookingKosan::whereHas('kosan', function ($query) {
                        $query->where('id_pemilik', Auth::id());
                    })
                        ->where('status_booking', 'pending')
                        ->count();
                    $activePembayaran = \App\Models\Pembayaran::whereHas('booking', function ($query) {
                        $query->whereHas('kosan', function ($q) {
                            $q->where('id_pemilik', Auth::id());
                        });
                    })
                        ->whereIn('status_pembayaran', ['pending', 'processing'])
                        ->latest()
                        ->first();
                } else {
                    $pendingBooking = App\Models\BookingKosan::where('user_id', Auth::id())
                        ->where('status_booking', 'pending')
                        ->latest()
                        ->first();
                    $pendingCount = App\Models\BookingKosan::where('user_id', Auth::id())
                        ->where('status_booking', 'pending')
                        ->count();
                    $activePembayaran = \App\Models\Pembayaran::whereHas('booking', function ($query) {
                        $query->where('user_id', Auth::id());
                    })
                        ->whereIn('status_pembayaran', ['pending', 'processing'])
                        ->latest()
                        ->first();
                }
                $totalBadge = ($pendingCount ?? 0) + (!empty($activePembayaran) ? 1 : 0);
                $isPaymentActive = request()->routeIs('payments.*') || request()->routeIs('user.pembayaran.*');
            @endphp

            <a href="#pembayaranSubmenu"
                class="nav-link dropdown-toggle {{ $isPaymentActive ? 'active' : '' }}"
                aria-expanded="{{ $isPaymentActive ? 'true' : 'false' }}">
                <i class="fas fa-credit-card"></i>
                <span>Pembayaran</span>
                @if (($totalBadge ?? 0) > 0)
                    <span class="nav-badge">{{ $totalBadge }}</span>
                @endif
            </a>

            <ul class="collapse submenu {{ $isPaymentActive ? 'show' : '' }}" id="pembayaranSubmenu">
                @if (!empty($pendingBooking))
                    <li>
                        <a href="{{ route('users.pembayaran.index', $pendingBooking->booking_id) }}" class="nav-link">
                            <i class="fas fa-credit-card fa-sm me-2"></i>
                            <span>Bayar Booking Pending</span>
                            @if (($pendingCount ?? 0) > 0)
                                <span class="nav-badge nav-badge-sm">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    </li>
                @endif

                @if (!empty($activePembayaran))
                    <li>
                        <a href="{{ route('users.pembayaran.konfirmasi', $activePembayaran->booking_id) }}"
                            class="nav-link">
                            <i class="fas fa-hourglass-half fa-sm me-2 text-warning"></i>
                            <span>Pembayaran Aktif</span>
                            <span class="nav-badge nav-badge-sm bg-warning">1</span>
                        </a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('users.bookings.index') }}" class="nav-link">
                        <i class="fas fa-receipt fa-sm me-2"></i>
                        <span>Riwayat Transaksi</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item {{ request()->is('users/favorites') ? 'active' : '' }}">
            <a href="{{ route('users.favorites') }}" class="nav-link">
                <i class="fas fa-bookmark"></i>
                <span>Favorit Saya</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('users.reviews.*') ? 'active' : '' }}">
            <a href="{{ route('users.reviews.index') }}" class="nav-link">
                <i class="fas fa-star"></i>
                <span>{{ (($role ?? null) === 'pemilik_kos') ? 'Ulasan Kosan Saya' : 'Review Saya' }}</span>
            </a>
        </li>
    </ul>
</div>
