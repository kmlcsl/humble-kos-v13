<header class="user-header">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid" style="padding-left: 8px; padding-right: 8px;">
            <!-- Mobile Toggle Button - ONLY show on mobile/tablet (< 992px) -->
            <button class="navbar-toggler border-0 mobile-only-hamburger" type="button" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Search Bar -->
            <div class="search-container grow mx-2 d-none d-md-flex">
                <form action="{{ route('users.kosan.index') }}" method="GET">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="keyword" class="search-input" placeholder="Cari kos, area, kampus..." value="{{ request('keyword') }}">
                        <button type="button" class="search-filter-btn" data-bs-toggle="modal"
                            data-bs-target="#searchFilterModal">
                            <i class="fas fa-sliders-h"></i>
                        </button>
                    </div>
                </form>
            </div>

            <div class="mobile-search-container grow mx-1 d-md-none">
                <form action="{{ route('users.kosan.index') }}" method="GET">
                    <div class="mobile-search-wrapper">
                        <i class="fas fa-search mobile-search-icon"></i>
                        <input type="text" name="keyword" class="mobile-search-input" placeholder="Cari..." value="{{ request('keyword') }}">
                        <button type="button" class="mobile-filter-btn" data-bs-toggle="modal"
                            data-bs-target="#searchFilterModal">
                            <i class="fas fa-sliders-h"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Side Menu -->
            <div class="header-actions ms-auto">
                <div class="header-action-item d-none d-md-block">
                    <a href="{{ route('users.favorites') }}" class="action-link">
                        <span class="action-icon">
                            <i class="fas fa-heart"></i>
                            <span class="action-badge">{{ $header_wishlist_count ?? 0 }}</span>
                        </span>
                        <span class="action-text d-none d-xl-inline-block">Favorit</span>
                    </a>
                </div>

                <!-- Notifications -->
                <div class="header-action-item">
                    <div class="dropdown">
                        <a class="action-link" href="#" role="button" id="notificationDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="action-icon">
                                <i class="fas fa-bell"></i>
                                <span class="action-badge">{{ $header_notification_count ?? 0 }}</span>
                            </span>
                            <span class="action-text d-none d-xl-inline-block">Notifikasi</span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end notification-dropdown"
                            aria-labelledby="notificationDropdown">
                            <div class="notification-header">
                                <h6 class="mb-0">Notifikasi</h6>
                                @auth
                                <form action="{{ route('users.notifications.readAll') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-link p-0 text-primary small">Tandai semua dibaca</button>
                                </form>
                                @endauth
                            </div>
                            <div class="notification-body">
                                @forelse(($header_notifications ?? collect()) as $notif)
                                    <form action="{{ route('users.notifications.read', data_get($notif,'notifikasi_id')) }}" method="POST" class="notification-item {{ data_get($notif,'is_read') ? '' : 'unread' }}">
                                        @csrf
                                        <div class="notification-icon bg-primary-light">
                                            <i class="fas fa-bell text-primary"></i>
                                        </div>
                                        <div class="notification-content">
                                            <p class="notification-text">{{ data_get($notif,'title') }}</p>
                                            <span class="notification-time">{{ optional(data_get($notif,'created_at'))->diffForHumans() }}</span>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-link p-0 ms-auto">Tandai dibaca</button>
                                    </form>
                                @empty
                                    <div class="p-3 text-center text-muted">Tidak ada notifikasi</div>
                                @endforelse
                            </div>
                            <div class="notification-footer">
                                @auth
                                <a href="{{ route('users.notifications.index') }}" class="text-center d-block">Lihat semua notifikasi</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Profile / Auth -->
                @auth
                <div class="header-action-item">
                    <div class="dropdown">
                        <a class="user-profile-link" href="#" role="button" id="userDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                <img src="{{ Auth::user()->profile_photo_url }}"
                                    alt="{{ Auth::user()->name ?? 'User' }}" class="profile-img">
                            </div>
                            <div class="user-info d-none d-lg-block">
                                <h6 class="user-name">{{ Auth::user()->name ?? 'Tamu' }}</h6>
                                <span class="user-role">
                                    @if (Auth::user()->role === 'pemilik_kos')
                                        Pemilik Kos
                                    @else
                                        {{ Auth::user()->student_id ? 'Mahasiswa' : 'Pengguna' }}
                                    @endif
                                </span>
                            </div>
                            <i class="fas fa-chevron-down ms-2 d-none d-lg-block dropdown-arrow"></i>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end user-dropdown" aria-labelledby="userDropdown">
                            <li>
                                <div class="dropdown-user-details d-lg-none">
                                    <h6 class="mb-0">{{ Auth::user()->name ?? 'Tamu' }}</h6>
                                    <p class="small text-muted mb-0">{{ Auth::user()->username ?? '' }}</p>
                                </div>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('users.profile.index') }}"><i
                                        class="fas fa-user me-2"></i> Profil Saya</a></li>
                            <li><a class="dropdown-item" href="{{ route('users.bookings.index') }}"><i
                                        class="fas fa-calendar-check me-2"></i> Booking Saya</a></li>
                            <li><a class="dropdown-item" href="{{ route('users.favorites') }}"><i class="fas fa-heart me-2"></i> Favorit</a>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('users.settings.index') }}"><i
                                        class="fas fa-cog me-2"></i> Pengaturan</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                @endauth

                @guest
                <div class="header-action-item d-flex align-items-center gap-1 gap-md-2" style="margin-right: 4px;">
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Daftar</a>
                </div>
                @endguest
            </div>
        </div>
    </nav>
</header>

<!-- Search Filter Modal -->
<div class="modal fade" id="searchFilterModal" tabindex="-1" aria-labelledby="searchFilterModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchFilterModalLabel">Filter Pencarian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('users.kosan.index') }}" method="GET">
                    <!-- Pertahankan keyword search jika ada -->
                    @if(request()->has('keyword'))
                        <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <select name="kota" class="form-select">
                            <option value="" {{ empty(request('kota')) ? 'selected' : '' }}>Pilih Lokasi</option>
                            <option value="Aceh Barat" {{ request('kota') == 'Aceh Barat' ? 'selected' : '' }}>Aceh Barat</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dekat Kampus</label>
                        <select name="kampus" class="form-select">
                            <option value="" {{ empty(request('kampus')) ? 'selected' : '' }}>Pilih Kampus</option>
                            <option value="Universitas Teuku Umar" {{ request('kampus') == 'Universitas Teuku Umar' ? 'selected' : '' }}>Universitas Teuku Umar</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rentang Harga</label>
                        <div class="row">
                            <div class="col">
                                <input type="number" name="harga_min" class="form-control" placeholder="Min" min="0" value="{{ request('harga_min') }}">
                            </div>
                            <div class="col">
                                <input type="number" name="harga_max" class="form-control" placeholder="Max" min="0" value="{{ request('harga_max') }}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Kos</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipe_kosan" value="putra" id="putraCheck" {{ request('tipe_kosan') == 'putra' ? 'checked' : '' }}>
                            <label class="form-check-label" for="putraCheck">Kos Putra</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipe_kosan" value="putri" id="putriCheck" {{ request('tipe_kosan') == 'putri' ? 'checked' : '' }}>
                            <label class="form-check-label" for="putriCheck">Kos Putri</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipe_kosan" value="campur" id="campurCheck" {{ request('tipe_kosan') == 'campur' ? 'checked' : '' }}>
                            <label class="form-check-label" for="campurCheck">Kos Campur</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fasilitas</label>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fasilitas[]" value="AC" id="acCheck" {{ in_array('AC', request('fasilitas', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="acCheck">AC</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fasilitas[]" value="Wifi" id="wifiCheck" {{ in_array('Wifi', request('fasilitas', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="wifiCheck">Wifi</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fasilitas[]" value="Kasur" id="kasurCheck" {{ in_array('Kasur', request('fasilitas', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="kasurCheck">Kasur</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fasilitas[]" value="Lemari" id="lemariCheck" {{ in_array('Lemari', request('fasilitas', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="lemariCheck">Lemari</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fasilitas[]" value="Kamar Mandi Dalam" id="kamarmandidCheck" {{ in_array('Kamar Mandi Dalam', request('fasilitas', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="kamarmandidCheck">Kamar Mandi Dalam</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fasilitas[]" value="Tempat Parkir" id="parkirCheck" {{ in_array('Tempat Parkir', request('fasilitas', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="parkirCheck">Tempat Parkir</label>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
            </div>
                </form>
        </div>
    </div>
</div>

<style>
    /* Header Styles */
    .user-header {
        background-color: #e6e9ee;
        position: fixed;
        top: 0;
        right: 0;
        left: var(--sidebar-width);
        z-index: 1021;
        transition: left var(--transition-speed);
        border-bottom: 1px solid var(--gray-200);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .navbar {
        padding: 12px 24px;
        height: var(--header-height);
        min-height: var(--header-height);
    }

    /* Mobile adjustments */
    @media (max-width: 768px) {
        .navbar {
            padding: 10px 8px;
        }
    }

    @media (max-width: 480px) {
        .navbar {
            padding: 8px 6px;
        }
    }

    @media (max-width: 992px) {
        .user-header {
            left: 0;
        }
    }

    /* ===================================
       DESKTOP SEARCH BAR STYLES
       =================================== */
    .search-container {
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
    }

    .search-input-wrapper {
        position: relative;
        background-color: white;
        border-radius: 50px;
        display: flex;
        align-items: center;
        padding: 0 10px;
        transition: all 0.3s;
        border: 1px solid var(--gray-300);
    }

    .search-input-wrapper:focus-within {
        background-color: white;
        border-color: var(--primary-light);
        box-shadow: 0 0 0 4px rgba(164, 195, 162, 0.1);
    }

    .search-icon {
        color: var(--gray-600);
        font-size: 14px;
        padding: 0 10px;
    }

    .search-input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 12px 10px;
        font-size: 14px;
        outline: none;
    }

    .search-filter-btn {
        background: none;
        border: none;
        color: var(--gray-600);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        cursor: pointer;
    }

    .search-filter-btn:hover {
        background-color: var(--gray-200);
        color: var(--primary);
    }

    /* ===================================
       MOBILE SEARCH BAR STYLES - NEW
       =================================== */
.mobile-search-container {
    width: 100%;
    margin: 0 2px;
    flex: 1;
    min-width: 0;
}


.mobile-search-wrapper {
    position: relative;
    background-color: white;
    border-radius: 20px;
    display: flex;
    align-items: center;
    padding: 0 8px;
    transition: all 0.3s;
    border: 1px solid var(--gray-300);
    height: 32px;
    width: 100%;
    min-width: 0;
}

    .mobile-search-wrapper:focus-within {
        background-color: white;
        border-color: var(--primary-light);
        box-shadow: 0 0 0 2px rgba(164, 195, 162, 0.1);
    }

    .mobile-search-icon {
        color: #6c757d;
        font-size: 12px;
        padding: 0 6px;
        flex-shrink: 0;
    }

    .mobile-search-input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 6px 4px;
        font-size: 12px;
        outline: none;
        min-width: 0;
    }

    .mobile-search-input::placeholder {
        font-size: 11px;
        color: #999;
    }

    .mobile-filter-btn {
        background: none;
        border: none;
        color: #6c757d;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        cursor: pointer;
        flex-shrink: 0;
        font-size: 10px;
    }

    .mobile-filter-btn:hover {
        background-color: #e9ecef;
        color: var(--primary);
    }

    /* ===================================
       OLD MOBILE SEARCH BAR - DISABLED
       =================================== */
    /* .mobile-search-bar {
        padding: 10px 15px;
        background-color: white;
        border-bottom: 1px solid var(--gray-200);
        display: none !important;
    } */

    /* ===================================
       HEADER ACTIONS STYLES
       =================================== */
    .header-actions {
        display: flex;
        align-items: center;
        margin-left: auto;
    }

    .header-action-item {
        position: relative;
        margin-left: 6px;
    }

    @media (max-width: 480px) {
        .header-action-item {
            margin-left: 2px;
            margin-right: 2px !important;
        }

        .header-action-item .btn {
            padding: 0.3rem 0.5rem;
            font-size: 0.7rem;
            white-space: nowrap;
        }

        .header-actions {
            margin-left: auto;
            margin-right: 4px;
        }
    }

    .action-link {
        display: flex;
        align-items: center;
        color: var(--gray-700);
        text-decoration: none;
        padding: 5px;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .action-link:hover {
        color: var(--primary);
        background-color: var(--gray-100);
    }

    .action-icon {
        position: relative;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .action-badge {
        position: absolute;
        top: 0;
        right: 0;
        width: 18px;
        height: 18px;
        background-color: var(--primary);
        color: white;
        font-size: 10px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        border: 2px solid white;
    }

    .action-text {
        font-size: 14px;
        margin-left: 5px;
        font-weight: 500;
    }

    /* ===================================
       USER PROFILE STYLES
       =================================== */
    .user-profile-link {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: var(--gray-700);
        padding: 5px;
        border-radius: 8px;
        transition: all 0.3s ease;
        position: relative;
    }

    .user-profile-link:hover {
        background-color: var(--gray-100);
        color: var(--primary);
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        overflow: hidden;
        background-color: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        position: relative;
    }

    .profile-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        transition: none;
    }

    .avatar-placeholder {
        color: var(--primary-dark);
        font-size: 14px;
        font-weight: 600;
        line-height: 1;
    }

    .user-info {
        line-height: 1.2;
        min-width: 0;
    }

    .user-name {
        font-size: 14px;
        margin: 0;
        color: var(--dark);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 120px;
    }

    .user-role {
        font-size: 12px;
        color: var(--gray-600);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 120px;
    }

    .dropdown-arrow {
        font-size: 12px;
        transition: transform 0.3s ease;
    }

    .user-profile-link[aria-expanded="true"] .dropdown-arrow {
        transform: rotate(180deg);
    }

    .dropdown-user-details {
        padding: 10px 16px;
        border-bottom: 1px solid var(--gray-200);
    }

    /* ===================================
       NOTIFICATION DROPDOWN STYLES
       =================================== */
    .notification-dropdown {
        width: 320px;
        max-height: 500px;
        overflow: hidden;
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid var(--gray-200);
    }

    .notification-body {
        max-height: 350px;
        overflow-y: auto;
    }

    .notification-body::-webkit-scrollbar {
        width: 5px;
    }

    .notification-body::-webkit-scrollbar-track {
        background: var(--gray-100);
    }

    .notification-body::-webkit-scrollbar-thumb {
        background-color: var(--gray-300);
        border-radius: 20px;
    }

    .notification-item {
        display: flex;
        align-items: flex-start;
        padding: 15px;
        border-bottom: 1px solid var(--gray-200);
        transition: background-color 0.2s;
        text-decoration: none;
        color: var(--dark);
    }

    .notification-item:hover {
        background-color: var(--gray-100);
    }

    .notification-item.unread {
        background-color: rgba(79, 111, 82, 0.05);
    }

    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 16px;
    }

    .bg-primary-light {
        background-color: rgba(79, 111, 82, 0.1);
    }

    .bg-success-light {
        background-color: rgba(40, 167, 69, 0.1);
    }

    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }

    .bg-info-light {
        background-color: rgba(23, 162, 184, 0.1);
    }

    .notification-content {
        flex: 1;
    }

    .notification-text {
        margin-bottom: 5px;
        font-size: 14px;
        color: var(--gray-800);
    }

    .notification-time {
        font-size: 12px;
        color: var(--gray-600);
    }

    .notification-footer {
        padding: 12px;
        border-top: 1px solid var(--gray-200);
    }

    .notification-footer a {
        color: var(--primary);
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        transition: color 0.2s;
    }

    .notification-footer a:hover {
        color: var(--primary-dark);
    }

    /* ===================================
       USER DROPDOWN STYLES
       =================================== */
    .user-dropdown {
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        min-width: 200px;
        z-index: 1100;
    }

    .user-dropdown .dropdown-item {
        padding: 10px 16px;
        transition: all 0.2s ease;
    }

    .user-dropdown .dropdown-item:hover {
        background-color: var(--gray-100);
        color: var(--primary);
    }

    .user-dropdown .dropdown-item i {
        width: 16px;
        text-align: center;
    }

    /* ===================================
       MODAL STYLES
       =================================== */
    .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        border-bottom: 1px solid var(--gray-200);
        padding: 15px 20px;
    }

    .modal-title {
        font-weight: 600;
        font-size: 18px;
        color: var(--dark);
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        border-top: 1px solid var(--gray-200);
        padding: 15px 20px;
    }

    .form-label {
        font-weight: 500;
        font-size: 14px;
        color: var(--gray-700);
        margin-bottom: 8px;
    }

    .form-select,
    .form-control {
        border-radius: 8px;
        border: 1px solid var(--gray-300);
        padding: 10px 15px;
        font-size: 14px;
    }

    .form-select:focus,
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 111, 82, 0.1);
    }

    .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .btn-primary {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
    }

    .btn-outline-secondary {
        color: var(--gray-700);
        border-color: var(--gray-300);
    }

    .btn-outline-secondary:hover {
        background-color: var(--gray-100);
        color: var(--gray-800);
    }

    /* ===================================
       HAMBURGER MENU - HIDE ON DESKTOP
       =================================== */
    /* DESKTOP: Hide hamburger menu completely on screens >= 992px */
    .mobile-only-hamburger {
        display: none !important;
    }

    /* MOBILE/TABLET: Show hamburger menu only on screens < 992px */
    @media (max-width: 991px) {
        .mobile-only-hamburger {
            display: flex !important;
        }
    }

    /* ===================================
       RESPONSIVE BREAKPOINTS
       =================================== */

    /* Tablet & Medium Screens - 992px */
    @media (max-width: 992px) {
        .navbar {
            padding: 4px 8px;
            height: auto;
            min-height: 50px;
        }

        .search-container {
            max-width: 200px;
            margin: 0 4px;
            flex-shrink: 2;
            min-width: 150px;
        }

        .search-input-wrapper {
            border-radius: 15px;
            padding: 0 6px;
        }

        .search-input {
            padding: 4px 6px;
            font-size: 11px;
            min-width: 0;
        }

        .search-input::placeholder {
            font-size: 10px;
        }

        .search-filter-btn {
            width: 24px;
            height: 24px;
            font-size: 10px;
        }

        .action-text {
            display: none !important;
        }

        .header-action-item {
            margin-left: 3px;
        }

        .action-icon {
            width: 30px;
            height: 30px;
            font-size: 14px;
        }

        .user-avatar {
            width: 26px;
            height: 26px;
        }
    }

    /* Small Screens - 768px */
    @media (max-width: 768px) {
        .search-container {
            display: none;
        }

        .navbar {
            padding: 6px 8px;
            justify-content: space-between;
        }

        .header-actions {
            margin-left: auto;
        }
    }

    /* Mobile Screens - 576px */
    @media (max-width: 576px) {
        .user-avatar {
            width: 32px;
            height: 32px;
            margin-right: 8px;
        }

        .avatar-placeholder {
            font-size: 12px;
        }

        .action-icon {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }

        .action-badge {
            width: 16px;
            height: 16px;
            font-size: 9px;
        }

        .notification-dropdown {
            width: 280px;
        }

        .user-dropdown {
            min-width: 180px;
        }

        /* Mobile Search Responsive */
        .mobile-search-wrapper {
            height: 28px;
            border-radius: 15px;
            padding: 0 6px;
        }

        .mobile-search-input {
            font-size: 11px;
            padding: 4px 3px;
        }

        .mobile-search-input::placeholder {
            font-size: 10px;
        }

        .mobile-filter-btn {
            width: 20px;
            height: 20px;
            font-size: 9px;
        }
    }

    /* Very Small Screens - 415px */
    @media (max-width: 415px) {
        .navbar {
            padding: 2px 4px;
            min-height: 45px;
            height: 45px;
        }

        .navbar .container-fluid {
            padding: 0;
            gap: 2px;
        }

        .navbar-toggler {
            padding: 2px 4px;
            font-size: 12px;
            border: none;
            margin-right: 4px;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .search-container {
            display: none !important;
        }

        .header-actions {
            margin-left: auto;
            gap: 2px;
            display: flex;
            align-items: center;
        }

        .header-action-item {
            margin-left: 2px;
        }

        .action-icon {
            width: 24px !important;
            height: 24px !important;
            font-size: 12px !important;
        }

        .action-badge {
            width: 12px !important;
            height: 12px !important;
            font-size: 7px !important;
            top: -2px;
            right: -2px;
        }

        .user-avatar {
            width: 22px !important;
            height: 22px !important;
            margin-right: 4px;
        }

        .avatar-placeholder {
            font-size: 9px !important;
        }

        .user-profile-link {
            padding: 2px;
        }

        .user-info {
            display: none !important;
        }

        .dropdown-arrow {
            display: none !important;
        }

        /* Mobile Search 415px */
        .mobile-search-wrapper {
            height: 26px;
            border-radius: 13px;
            padding: 0 5px;
        }

        .mobile-search-input {
            font-size: 10px;
            padding: 3px 2px;
        }

        .mobile-search-input::placeholder {
            font-size: 9px;
        }

        .mobile-filter-btn {
            width: 18px;
            height: 18px;
            font-size: 8px;
        }

        .mobile-search-icon {
            font-size: 10px;
            padding: 0 4px;
        }
    }

    /* Extra Small Screens - 375px */
    @media (max-width: 375px) {
        .navbar {
            padding: 1px 2px;
            min-height: 40px;
            height: 40px;
        }

        .navbar-toggler {
            width: 24px;
            height: 24px;
            font-size: 10px;
            margin-right: 2px;
        }

        .action-icon {
            width: 20px !important;
            height: 20px !important;
            font-size: 10px !important;
        }

        .action-badge {
            width: 10px !important;
            height: 10px !important;
            font-size: 6px !important;
        }

        .user-avatar {
            width: 18px !important;
            height: 18px !important;
            margin-right: 2px;
        }

        .avatar-placeholder {
            font-size: 8px !important;
        }

        .header-action-item {
            margin-left: 1px;
        }

        /* Mobile Search 375px */
        .mobile-search-wrapper {
            height: 24px;
            padding: 0 4px;
        }

        .mobile-search-input {
            font-size: 9px;
            padding: 2px;
        }

        .mobile-filter-btn {
            width: 16px;
            height: 16px;
            font-size: 7px;
        }
    }

    /* Ultra Small Screens - 320px */
    @media (max-width: 320px) {
        .navbar {
            padding: 0 1px;
            min-height: 38px;
            height: 38px;
        }

        .navbar-toggler {
            width: 22px;
            height: 22px;
            font-size: 9px;
            margin-right: 1px;
        }

        .action-icon {
            width: 18px !important;
            height: 18px !important;
            font-size: 9px !important;
        }

        .user-avatar {
            width: 16px !important;
            height: 16px !important;
        }

        .avatar-placeholder {
            font-size: 7px !important;
        }

        .header-action-item:first-child {
            display: none !important;
        }

        /* Mobile Search 320px */
        .mobile-search-wrapper {
            height: 22px;
            padding: 0 3px;
        }

        .mobile-search-input::placeholder {
            font-size: 8px;
        }

        .mobile-filter-btn {
            width: 14px;
            height: 14px;
            font-size: 6px;
        }

        .mobile-search-icon {
            font-size: 9px;
            padding: 0 3px;
        }
    }
</style>
