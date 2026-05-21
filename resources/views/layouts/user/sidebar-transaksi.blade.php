<div class="nav-section">
    <h6 class="nav-section-title">Transaksi</h6>
    <ul class="nav-items">
        @php
            // Identifikasi status aktif untuk grup Pembayaran
            $isPaymentGroupActive = request()->routeIs('users.pembayaran.*') || request()->routeIs('users.bookings.*');
            
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
        @endphp

        <li class="nav-item {{ $isPaymentGroupActive ? 'active' : '' }}">
            <a href="#pembayaranSubmenu"
                class="nav-link dropdown-toggle {{ $isPaymentGroupActive ? 'active' : '' }}"
                aria-expanded="{{ $isPaymentGroupActive ? 'true' : 'false' }}">
                <i class="fas fa-credit-card"></i>
                <span>Pembayaran</span>
                @if (($totalBadge ?? 0) > 0)
                    <span class="nav-badge">{{ $totalBadge }}</span>
                @endif
            </a>

            <ul class="submenu {{ $isPaymentGroupActive ? 'show' : '' }}" id="pembayaranSubmenu">
                {{-- 1. Daftar Booking (Selalu ada, pengganti Riwayat Transaksi agar tidak double) --}}
                <li>
                    <a href="{{ route('users.bookings.index') }}" 
                       class="nav-link {{ request()->routeIs('users.bookings.index') ? 'active' : '' }}">
                        <i class="fas fa-list-ul fa-sm"></i>
                        <span>Daftar Booking Saya</span>
                    </a>
                </li>

                {{-- 2. Bayar Booking Pending (Hanya muncul jika ada booking yang belum masuk tahap pembayaran gateway/manual) --}}
                @if (!empty($pendingBooking))
                    <li>
                        <a href="{{ route('users.pembayaran.index', $pendingBooking->booking_id) }}" 
                           class="nav-link {{ request()->routeIs('users.pembayaran.index') ? 'active' : '' }}">
                            <i class="fas fa-money-bill-wave fa-sm"></i>
                            <span>Bayar Booking</span>
                            @if (($pendingCount ?? 0) > 0)
                                <span class="nav-badge nav-badge-sm">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    </li>
                @endif

                {{-- 3. Pembayaran Aktif (Diubah ke Show agar bisa 'Lanjutkan Pembayaran') --}}
                @if (!empty($activePembayaran))
                    <li>
                        <a href="{{ route('users.bookings.show', $activePembayaran->booking_id) }}"
                            class="nav-link {{ request()->routeIs('users.bookings.show') && !request()->routeIs('users.bookings.index') ? 'active' : '' }}">
                            <i class="fas fa-hourglass-half fa-sm text-warning"></i>
                            <span>Pembayaran Aktif</span>
                            <span class="nav-badge nav-badge-sm bg-warning">1</span>
                        </a>
                    </li>
                @endif
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
