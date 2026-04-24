<header class="admin-header">
    <div class="container-fluid px-3">
        <div class="row align-items-center">
            <div class="col">
                <!-- Sidebar Toggle Button -->
                <button class="btn btn-link text-secondary border-0 p-0 d-lg-none" id="mobileSidebarToggle">
                    <i class="fas fa-bars fs-5"></i>
                </button>
                <button class="btn btn-link text-secondary border-0 p-0 d-none d-lg-inline-block" id="sidebarToggle">
                    <i class="fas fa-bars fs-5"></i>
                </button>
            </div>

            <div class="col-auto d-flex align-items-center">
                <!-- Search -->
                <div class="dropdown me-3 d-none d-md-block">
                    <button class="btn btn-outline-secondary border-0" type="button" id="searchDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-search"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-3" style="width: 300px;"
                        aria-labelledby="searchDropdown">
                        <form class="d-flex" action="#" method="GET">
                            <input class="form-control me-2" type="search" name="query" placeholder="Search..."
                                aria-label="Search">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="dropdown me-3">
                    <button class="btn btn-outline-secondary border-0 position-relative" type="button"
                        id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        @if ($notifications_count ?? 0 > 0)
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $notifications_count > 99 ? '99+' : $notifications_count }}
                                <span class="visually-hidden">unread notifications</span>
                            </span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0"
                        aria-labelledby="notificationDropdown">
                        <div
                            class="notification-header d-flex justify-content-between align-items-center p-3 border-bottom">
                            <h6 class="mb-0 fw-bold">Notifications</h6>
                            @if ($notifications_count ?? 0 > 0)
                                <a href="#" class="text-decoration-none small">Mark all as read</a>
                            @endif
                        </div>
                        <div class="notification-body">
                            @if (isset($notifications) && count($notifications) > 0)
                                @foreach ($notifications as $notification)
                                    <a href="#"
                                        class="dropdown-item p-3 border-bottom notification-item {{ $notification->read_at ? '' : 'unread' }}">
                                        <div class="d-flex">
                                            <div class="notification-icon me-3">
                                                @switch($notification->type)
                                                    @case('booking')
                                                        <div class="icon-circle bg-primary">
                                                            <i class="fas fa-calendar-check text-white"></i>
                                                        </div>
                                                    @break

                                                    @case('payment')
                                                        <div class="icon-circle bg-success">
                                                            <i class="fas fa-money-bill-wave text-white"></i>
                                                        </div>
                                                    @break

                                                    @case('user')
                                                        <div class="icon-circle bg-info">
                                                            <i class="fas fa-user text-white"></i>
                                                        </div>
                                                    @break

                                                    @default
                                                        <div class="icon-circle bg-secondary">
                                                            <i class="fas fa-bell text-white"></i>
                                                        </div>
                                                @endswitch
                                            </div>
                                            <div>
                                                <p class="notification-text mb-1">{{ $notification->message }}</p>
                                                <span
                                                    class="notification-time small text-muted">{{ $notification->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                <div class="p-4 text-center empty-notification">
                                    <i class="fas fa-bell-slash fs-3 mb-3 text-muted"></i>
                                    <p class="mb-0 text-muted">No notifications yet</p>
                                </div>
                            @endif
                        </div>
                        <div class="notification-footer text-center p-2 border-top">
                            <a href="#" class="text-decoration-none small">View all notifications</a>
                        </div>
                    </div>
                </div>

                <!-- Pemilik Profile -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary border-0 d-flex align-items-center" type="button"
                        id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        @php
                            $user = Auth::user();
                            $avatarUrl = $user && $user->foto_profil
                                ? asset('storage/' . $user->foto_profil)
                                : asset('images/default-avatar.svg');
                        @endphp
                        <img src="{{ $avatarUrl }}"
                            onerror="this.src='{{ asset('images/default-avatar.svg') }}'" alt="User"
                            class="rounded-circle me-2" width="32" height="32"
                            style="object-fit: cover; border: 2px solid #dee2e6;">
                        <span class="d-none d-md-inline">{{ Auth::user()->nama_lengkap ?? Auth::user()->name ?? 'Pemilik' }}</span>
                        <i class="fas fa-chevron-down ms-2 small"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li>
                            <h6 class="dropdown-header">Halo, {{ Auth::user()->nama_lengkap ?? Auth::user()->name ?? 'Pemilik' }}!</h6>
                        </li>
                        <li><a class="dropdown-item" href="{{ route('pemilik.pengaturan.profil') }}"><i class="fas fa-user me-2 text-primary"></i> Profile
                                Saya</a></li>
                        <li><a class="dropdown-item" href="{{ route('pemilik.pengaturan.keamanan') }}"><i class="fas fa-cog me-2 text-primary"></i>
                                Pengaturan</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="#"
                                onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                            <form id="header-logout-form" action="{{ route('pemilik.logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
    /* Admin Header Styles */
    .admin-header {
        background-color: #DAE4F5FF;
        border-bottom: 1px solid #e2e8f0;
        padding: 15px 0;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 0;
        z-index: 999;
    }

    #sidebarToggle,
    #mobileSidebarToggle {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.3s;
        padding: 0 !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
    }

    #sidebarToggle:hover,
    #mobileSidebarToggle:hover {
        background-color: #f1f5f9;
    }

    #sidebarToggle:focus,
    #mobileSidebarToggle:focus {
        outline: none !important;
        box-shadow: none !important;
        border: none !important;
    }

    /* Fix hamburger icon - ensure only 3 lines visible */
    #sidebarToggle i.fa-bars,
    #mobileSidebarToggle i.fa-bars {
        font-size: 1.25rem;
        line-height: 1;
    }

    /* Notification Dropdown */
    .notification-dropdown {
        width: 320px;
        max-height: 400px;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .notification-body {
        max-height: 300px;
        overflow-y: auto;
    }

    .notification-body::-webkit-scrollbar {
        width: 5px;
    }

    .notification-body::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .notification-body::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 20px;
    }

    .notification-item {
        transition: background-color 0.2s;
    }

    .notification-item:hover {
        background-color: #f8fafc;
    }

    .notification-item.unread {
        background-color: #f0f9ff;
    }

    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .notification-text {
        font-size: 14px;
        color: #334155;
    }

    /* Responsive Adjustments */
    @media (min-width: 992px) {
        .admin-header .container-fluid {
            padding-left: 30px;
            padding-right: 30px;
        }
    }

    /* When the sidebar is collapsed */
    body.sidebar-collapsed .admin-header .container-fluid {
        padding-left: 30px;
    }
</style>
