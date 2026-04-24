<div class="admin-sidebar">
    <!-- Logo -->
    <div class="sidebar-header d-flex align-items-center justify-content-between py-4 px-3">
        <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center text-decoration-none">
            <span class="ms-3 fs-5 fw-bold text-white sidebar-title full-title">HumbleKos</span>
            <span class="fs-5 fw-bold text-white short-title" style="display: none;">HK</span>
        </a>
        <button id="closeSidebar" class="btn btn-link text-white d-lg-none p-0 border-0">
            <i class="fas fa-times fs-5"></i>
        </button>
    </div>

    <!-- Admin Info -->
    <div class="admin-profile d-flex align-items-center p-3 mt-3 mb-3 mx-3 bg-white bg-opacity-10 rounded">
        <div class="ms-3">
            <h6 class="mb-0 text-white fw-semibold">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</h6>
            <p class="mb-0 text-white-50 small">Administrator</p>
        </div>
    </div>

    <!-- Navigation Menu -->
    <ul class="sidebar-nav list-unstyled">
        <!-- Dashboard -->
        <li class="sidebar-item">
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
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
                class="sidebar-dropdown list-unstyled {{ request()->is('admin/manajemenkosan*') || request()->is('admin/manajemen-kamar*') ? 'show' : '' }}">
                <li><a class="dropdown-item {{ request()->routeIs('admin.manajemen-kosan.index') ? 'active' : '' }}"
                        href="{{ route('admin.manajemen-kosan.index') }}">Daftar Kosan</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.manajemen-kosan.create') ? 'active' : '' }}"
                        href="{{ route('admin.manajemen-kosan.create') }}">Tambah Kosan</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.manajemen-kamar.*') ? 'active' : '' }}"
                        href="{{ route('admin.manajemen-kamar.index') }}">Manajemen Kamar</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.fasilitas.*') ? 'active' : '' }}" href="{{ route('admin.fasilitas.index') }}">Fasilitas</a></li>
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
                class="sidebar-dropdown list-unstyled {{ request()->is('admin/bookings*') ? 'show' : '' }}">
                <li><a class="dropdown-item {{ request()->routeIs('admin.bookings.index') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}">Semua Booking</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.bookings.pending') ? 'active' : '' }}" href="{{ route('admin.bookings.pending') }}">Menunggu Konfirmasi</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.bookings.confirmed') ? 'active' : '' }}" href="{{ route('admin.bookings.confirmed') }}">Dikonfirmasi</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.bookings.cancelled') ? 'active' : '' }}" href="{{ route('admin.bookings.cancelled') }}">Dibatalkan</a></li>
            </ul>
        </li>

        <!-- Users -->
        <li class="sidebar-item">
            <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Manajemen Pengguna</span>
            </a>
        </li>

        <!-- Payments -->
        <li class="sidebar-item">
            <a href="#" class="sidebar-link sidebar-dropdown-toggle" data-target="paymentSubmenu">
                <i class="fas fa-money-bill-wave"></i>
                <span>Manajemen Pembayaran</span>
                <i class="fas fa-chevron-right dropdown-indicator"></i>
            </a>
            <ul id="paymentSubmenu"
                class="sidebar-dropdown list-unstyled {{ request()->is('admin/pembayaran*') ? 'show' : '' }}">
                <li><a class="dropdown-item {{ request()->routeIs('admin.pembayaran.index') && !request()->has('status') ? 'active' : '' }}"
                        href="{{ route('admin.pembayaran.index') }}">Semua Pembayaran</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.pembayaran.pending') ? 'active' : '' }}"
                        href="{{ route('admin.pembayaran.pending') }}">Menunggu Verifikasi</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.pembayaran.successful') ? 'active' : '' }}"
                        href="{{ route('admin.pembayaran.successful') }}">Pembayaran Sukses</a>
                </li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.pembayaran.expired') ? 'active' : '' }}"
                        href="{{ route('admin.pembayaran.expired') }}">Pembayaran Kadaluarsa</a></li>
            </ul>
        </li>

        <!-- Laporan -->
        <li class="sidebar-item">
            <a href="{{ route('admin.laporan.index') }}" class="sidebar-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span>Laporan</span>
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
                class="sidebar-dropdown list-unstyled {{ request()->is('admin/pengaturan*') ? 'show' : '' }}">
                <li><a class="dropdown-item {{ request()->routeIs('admin.pengaturan.general') ? 'active' : '' }}" href="{{ route('admin.pengaturan.general') }}">Umum</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.pengaturan.appearance') ? 'active' : '' }}" href="{{ route('admin.pengaturan.appearance') }}">Tampilan</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.pengaturan.email') ? 'active' : '' }}" href="{{ route('admin.pengaturan.email') }}">Email</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.pengaturan.admins') ? 'active' : '' }}" href="{{ route('admin.pengaturan.admins') }}">Pengguna Admin</a></li>
            </ul>
        </li>
    </ul>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            class="btn btn-danger btn-sm w-100">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
        </a>
        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</div>
