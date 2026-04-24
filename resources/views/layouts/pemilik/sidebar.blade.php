<div class="admin-sidebar">
    <!-- Logo -->
    <div class="sidebar-header d-flex align-items-center justify-content-center py-4">
        <a href="{{ route('pemilik.dashboard') }}" class="d-flex align-items-center text-decoration-none">
            <span class="ms-3 fs-5 fw-bold text-white sidebar-title full-title">HumbleKos</span>
            <span class="fs-5 fw-bold text-white short-title" style="display: none;">HK</span>
        </a>
    </div>

    <!-- Pemilik Info -->
    <div class="admin-profile d-flex align-items-center p-3 mt-3 mb-3 mx-3 bg-white bg-opacity-10 rounded">
        <div class="ms-3">
            <h6 class="mb-0 text-white fw-semibold">{{ Auth::user()->nama_lengkap ?? Auth::user()->name ?? 'Pemilik' }}</h6>
            <p class="mb-0 text-white-50 small">Pemilik Kos</p>
        </div>
    </div>

    <!-- Navigation Menu -->
    <ul class="sidebar-nav list-unstyled">
        <!-- Dashboard -->
        <li class="sidebar-item">
            <a href="{{ route('pemilik.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('pemilik.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Kos Management -->
        <li class="sidebar-item">
            <a href="#" class="sidebar-link sidebar-dropdown-toggle" data-target="kosSubmenu">
                <i class="fas fa-home"></i>
                <span>Manajemen Kos</span>
                <i class="fas fa-chevron-right dropdown-indicator"></i>
            </a>
            <ul id="kosSubmenu"
                class="sidebar-dropdown list-unstyled {{ request()->is('pemilik/kosan*') || request()->is('pemilik/kamar*') ? 'show' : '' }}">
                <li><a class="dropdown-item {{ request()->routeIs('pemilik.kosan.index') ? 'active' : '' }}"
                        href="{{ route('pemilik.kosan.index') }}">Daftar Kosan</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pemilik.kosan.create') ? 'active' : '' }}"
                        href="{{ route('pemilik.kosan.create') }}">Tambah Kosan</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pemilik.kamar.*') ? 'active' : '' }}"
                        href="{{ route('pemilik.kamar.index') }}">Manajemen Kamar</a></li>
            </ul>
        </li>

        <!-- Bookings -->
        <li class="sidebar-item">
            <a href="#" class="sidebar-link sidebar-dropdown-toggle" data-target="bookingSubmenu">
                <i class="fas fa-calendar-check"></i>
                <span>Manajemen Booking</span>
                <i class="fas fa-chevron-right dropdown-indicator"></i>
            </a>
            <ul id="bookingSubmenu"
                class="sidebar-dropdown list-unstyled {{ request()->is('pemilik/bookings*') ? 'show' : '' }}">
                <li><a class="dropdown-item {{ request()->routeIs('pemilik.bookings.index') ? 'active' : '' }}" href="{{ route('pemilik.bookings.index') }}">Semua Booking</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pemilik.bookings.pending') ? 'active' : '' }}" href="{{ route('pemilik.bookings.pending') }}">Menunggu Konfirmasi</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pemilik.bookings.confirmed') ? 'active' : '' }}" href="{{ route('pemilik.bookings.confirmed') }}">Dikonfirmasi</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pemilik.bookings.cancelled') ? 'active' : '' }}" href="{{ route('pemilik.bookings.cancelled') }}">Dibatalkan</a></li>
            </ul>
        </li>

        <!-- Payments -->
        <li class="sidebar-item">
            <a href="#" class="sidebar-link sidebar-dropdown-toggle" data-target="paymentSubmenu">
                <i class="fas fa-money-bill-wave"></i>
                <span>Manajemen Pembayaran</span>
                <i class="fas fa-chevron-right dropdown-indicator"></i>
            </a>
            <ul id="paymentSubmenu"
                class="sidebar-dropdown list-unstyled {{ request()->is('pemilik/pembayaran*') ? 'show' : '' }}">
                <li><a class="dropdown-item {{ request()->routeIs('pemilik.pembayaran.index') && !request()->has('status') ? 'active' : '' }}"
                        href="{{ route('pemilik.pembayaran.index') }}">Semua Pembayaran</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pemilik.pembayaran.verifikasi') ? 'active' : '' }}"
                        href="{{ route('pemilik.pembayaran.verifikasi') }}">Menunggu Verifikasi</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pemilik.pembayaran.sukses') ? 'active' : '' }}"
                        href="{{ route('pemilik.pembayaran.sukses') }}">Pembayaran Sukses</a>
                </li>
                <li><a class="dropdown-item {{ request()->routeIs('pemilik.pembayaran.kadaluarsa') ? 'active' : '' }}"
                        href="{{ route('pemilik.pembayaran.kadaluarsa') }}">Pembayaran Kadaluarsa</a></li>
            </ul>
        </li>

        <!-- Laporan -->
        <li class="sidebar-item">
            <a href="#" class="sidebar-link sidebar-dropdown-toggle" data-target="laporanSubmenu">
                <i class="fas fa-chart-bar"></i>
                <span>Laporan</span>
                <i class="fas fa-chevron-right dropdown-indicator"></i>
            </a>
            <ul id="laporanSubmenu"
                class="sidebar-dropdown list-unstyled {{ request()->is('pemilik/laporan*') ? 'show' : '' }}">
                <li><a class="dropdown-item {{ request()->routeIs('pemilik.laporan.index') ? 'active' : '' }}"
                        href="{{ route('pemilik.laporan.index') }}">Dashboard Laporan</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pemilik.laporan.okupansi') ? 'active' : '' }}"
                        href="{{ route('pemilik.laporan.okupansi') }}">Laporan Okupansi</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pemilik.laporan.pendapatan') ? 'active' : '' }}"
                        href="{{ route('pemilik.laporan.pendapatan') }}">Laporan Pendapatan</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pemilik.laporan.transaksi') ? 'active' : '' }}"
                        href="{{ route('pemilik.laporan.transaksi') }}">Riwayat Transaksi</a></li>
            </ul>
        </li>

        <!-- Reviews -->
        <li class="sidebar-item">
            <a href="{{ route('pemilik.ulasan.index') }}" class="sidebar-link {{ request()->routeIs('pemilik.ulasan.*') ? 'active' : '' }}">
                <i class="fas fa-star"></i>
                <span>Ulasan & Rating</span>
            </a>
        </li>

        <!-- Settings -->
        <li class="sidebar-item">
            <a href="#" class="sidebar-link sidebar-dropdown-toggle" data-target="settingsSubmenu">
                <i class="fas fa-cogs"></i>
                <span>Pengaturan</span>
                <i class="fas fa-chevron-right dropdown-indicator"></i>
            </a>
            <ul id="settingsSubmenu"
                class="sidebar-dropdown list-unstyled {{ request()->is('pemilik/pengaturan*') ? 'show' : '' }}">
                <li><a class="dropdown-item {{ request()->routeIs('pemilik.pengaturan.profil') ? 'active' : '' }}" href="{{ route('pemilik.pengaturan.profil') }}">Profil Saya</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pemilik.pengaturan.keamanan') ? 'active' : '' }}" href="{{ route('pemilik.pengaturan.keamanan') }}">Keamanan Akun</a></li>
            </ul>
        </li>
    </ul>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            class="btn btn-danger btn-sm w-100">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
        </a>
        <form id="logout-form" action="{{ route('pemilik.logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</div>
