<aside class="user-sidebar">
    <div class="sidebar-header">
        <a href="#" class="logo-container">
            <span class="sidebar-logo-text">
                <span class="h">H</span><span class="umble">umble</span><span class="k">K</span><span
                    class="os">os</span>
            </span>
        </a>
        <button id="closeSidebar" class="d-lg-none close-sidebar">
            <i class="fas fa-times"></i>
        </button>
    </div>

    @php
        $user = Auth::user();
        $userName = $user?->nama_lengkap ?? $user?->name ?? 'Tamu';
        
        // Logika Inisial: Ambil huruf pertama dari maksimal 2 kata pertama
        $words = explode(' ', trim($userName));
        $initials = '';
        $count = 0;
        foreach ($words as $word) {
            if (!empty(trim($word)) && $count < 2) {
                $initials .= strtoupper($word[0]);
                $count++;
            }
        }
    @endphp
    <!-- User Profile Summary -->
    <div class="user-profile-summary"
        style="display: flex !important; align-items: center !important; justify-content: flex-start !important; padding: 15px 15px 15px 60px !important;"> 
        <div class="sidebar-user-avatar">

            @if (!empty($user?->foto_profil))
                <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="{{ $userName }}"
                    style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
            @else
                <div
                    style="width:100%; height:100%; border-radius:50%;
                    display:flex; align-items:center; justify-content:center;
                    font-weight:700; color:var(--primary-dark); font-size: 20px;">
                    {{ $user ? $initials : 'T' }}
                </div>
            @endif
        </div>

        <div class="user-info"
            style="display: flex !important; flex-direction: column !important; justify-content: center !important; flex: 1 !important; min-width: 0 !important; overflow: hidden !important; opacity: 1 !important; visibility: visible !important;">
            <h6 class="user-name"
                style="margin: 0 !important; padding: 0 !important; color: #000000 !important; font-weight: 700 !important; font-size: 18px !important; display: block !important; opacity: 1 !important; visibility: visible !important; line-height: 1.3;">
                {{ $user ? $initials : 'Tamu' }}
            </h6>
            <span class="user-status"
                style="margin: 0 !important; padding: 0 !important; font-size: 13px !important; color: #333333 !important; display: block !important; opacity: 1 !important; visibility: visible !important; line-height: 1.3; margin-top: 2px;">
                @if (($user?->role ?? null) === 'pemilik_kos')
                    Pemilik Kos
                @else
                    Pengguna
                @endif
            </span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <div class="nav-section">
            <h6 class="nav-section-title">Main</h6>
            <ul class="nav-items">
                <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('users.dashboard') }}" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Beranda</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->is('users/kosan') ? 'active' : '' }}">
                    <a href="{{ route('users.kosan.index') }}" class="nav-link">
                        <i class="fas fa-building"></i>
                        <span>Daftar Kos</span>
                    </a>
                </li>
                @auth
                @endauth
                <li class="nav-item {{ request()->is('users/kosan/nearby') ? 'active' : '' }}">
                    <a href="{{ route('users.kosan.nearby') }}" class="nav-link">
                        <i class="fas fa-map-marked-alt"></i>
                        <span>Peta Lokasi Kos</span>
                    </a>
                </li>

            </ul>
        </div>

        @includeWhen(Auth::check(), 'layouts.user.sidebar-transaksi', ['role' => $role ?? null])
    </nav>

    <div></div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="help-card">
            <div class="help-icon">
                <i class="fas fa-headset"></i>
            </div>
            <div class="help-content">
                <h6>Butuh Bantuan?</h6>
                <p>Hubungi tim support kami</p>
                <a href="#" class="help-link">Pusat Bantuan</a>
            </div>
        </div>

        @includeWhen(Auth::check(), 'layouts.user.sidebar-logout-auth')
    </div>
</aside>

<style>
    :root {
        --primary: #4f6f52;
        --primary-dark: #3a4d39;
        --primary-light: #a4c3a2;
        --secondary: #eef5e4;
        --danger: #ff6b6b;
        --dark: #2c3639;
        --light: #f8f9fa;
        --gray-100: #f8f9fa;
        --gray-200: #e9ecef;
        --gray-300: #dee2e6;
        --gray-400: #ced4da;
        --gray-500: #adb5bd;
        --gray-600: #6c757d;
        --gray-700: #495057;
        --gray-800: #343a40;
        --gray-900: #212529;
        --sidebar-width: 280px;
        --transition-speed: 0.3s;
    }

    /* Sidebar Styles */
    .user-sidebar {
        width: var(--sidebar-width);
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        background-color: #e6e9ee;
        border-right: 1px solid var(--gray-200);
        display: flex;
        flex-direction: column;
        z-index: 1040;
        transition: transform var(--transition-speed);
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        overflow-y: auto;
    }

    /* Mobile Adjustments */
    @media (max-width: 992px) {
        .user-sidebar {
            transform: translateX(-100%);
        }

        body.sidebar-active .user-sidebar {
            transform: translateX(0);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        }
    }

    /* Mobile Overlay Styles */
    .mobile-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1035;
        display: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    body.sidebar-active .mobile-overlay {
        display: block;
        opacity: 1;
    }

    /* Hide scrollbar for Chrome, Safari and Opera */
    .user-sidebar::-webkit-scrollbar {
        width: 5px;
    }

    .user-sidebar::-webkit-scrollbar-track {
        background: transparent;
    }

    .user-sidebar::-webkit-scrollbar-thumb {
        background-color: var(--gray-300);
        border-radius: 20px;
    }

    /* Sidebar Header */
    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 35px;
        border-bottom: 2px solid #d1d5db;
        height: 60px;
        background-color: #e6e9ee;
        position: relative;
    }

    .sidebar-logo-text {
        font-family: 'Poppins', sans-serif;
        color: #000000;
        font-weight: 700;
        display: inline-flex;
        align-items: baseline;
        text-align: center;
        line-height: 1;
        letter-spacing: -0.5px;
    }

    .h { font-size: 25px; }
    .umble { font-size: 21px; }
    .k { font-size: 25px; }
    .os { font-size: 21px; }

    .logo-container {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-grow: 1;
        text-decoration: none;
    }

    .close-sidebar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--gray-200);
        border: none;
        color: var(--gray-700);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        position: absolute;
        right: 20px;
    }

    .close-sidebar:hover {
        background: var(--gray-300);
        color: var(--gray-900);
    }

    /* User Profile Summary */
    .user-profile-summary {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px 20px;
        border-bottom: 2px solid #d1d5db;
        background-color: #e6e9ee;
    }

    .sidebar-user-avatar {
        width: 50px !important;
        height: 50px !important;
        border-radius: 50%;
        background-color: var(--primary-light);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-right: 15px;
        transition: all 0.3s ease;
    }

    @media (max-width: 576px) {
        .sidebar-user-avatar {
            width: 35px !important;
            height: 35px !important;
        }
        .user-profile-summary {
            padding-left: 35px !important;
        }
    }

    @media (max-width: 400px) {
        .sidebar-user-avatar {
            width: 30px !important;
            height: 30px !important;
        }
        .user-profile-summary {
            padding-left: 30px !important;
        }
    }

    .sidebar-user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .user-info {
        display: flex;
        flex-direction: column;
        justify-content: center;
        flex: 1;
        min-width: 0;
    }

    .user-name {
        margin: 0;
        padding: 0;
        color: #000000;
        font-weight: 600;
        font-size: 16px;
        word-break: break-word;
        line-height: 1.3;
    }

    .user-status {
        margin: 0;
        padding: 0;
        font-size: 13px;
        color: #333333;
        line-height: 1.3;
        margin-top: 2px;
    }

    /* Navigation */
    .sidebar-nav {
        flex: 1;
        padding: 10px 0;
        overflow-y: auto;
        border-bottom: 2px solid #d1d5db;
    }

    .nav-section {
        margin-bottom: 25px;
        padding: 0 20px;
    }

    .nav-section-title {
        font-size: 12px;
        text-transform: uppercase;
        color: var(--gray-600);
        margin-bottom: 15px;
        padding-left: 10px;
        letter-spacing: 0.5px;
    }

    .nav-items {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .nav-item {
        margin-bottom: 5px;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        color: var(--gray-700);
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.2s;
        font-weight: 500;
    }

    .nav-link:hover {
        background-color: var(--gray-100);
        color: var(--primary);
    }

    .nav-item.active > .nav-link {
        background-color: var(--primary-light);
        color: var(--primary-dark);
    }

    .nav-link i {
        width: 20px;
        text-align: center;
        font-size: 18px;
        margin-right: 12px;
    }

    /* Submenu Styles */
    .submenu {
        list-style: none !important;
        padding: 0 !important;
        margin: 5px 0 10px 0 !important;
        display: none;
        background-color: rgba(0, 0, 0, 0.05) !important;
        border-radius: 12px !important;
        border-left: 3px solid var(--primary-light) !important;
        width: 100% !important;
    }

    .submenu.show {
        display: block !important;
        visibility: visible !important;
        height: auto !important;
        opacity: 1 !important;
    }

    .submenu li {
        width: 100% !important;
        display: block !important;
    }

    .submenu .nav-link {
        padding: 10px 15px 10px 40px !important;
        font-size: 13px !important;
        color: #4b5563 !important;
        display: flex !important;
        align-items: center !important;
        text-decoration: none !important;
        width: 100% !important;
        background-color: transparent !important;
    }

    .submenu .nav-link:hover {
        background-color: rgba(79, 111, 82, 0.1) !important;
        color: var(--primary-dark) !important;
    }

    .submenu .nav-link.active {
        background-color: white !important;
        color: var(--primary) !important;
        font-weight: 600 !important;
        border-radius: 0 8px 8px 0 !important;
    }

    .submenu .nav-link i {
        font-size: 13px !important;
        width: 18px !important;
        margin-right: 10px !important;
        color: inherit !important;
    }

    .dropdown-toggle {
        position: relative;
        display: flex !important;
        align-items: center;
        justify-content: space-between;
    }

    .dropdown-toggle::after {
        content: '\f107';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        font-size: 12px;
        transition: transform 0.3s;
        margin-left: auto;
    }

    .dropdown-toggle[aria-expanded="true"]::after {
        transform: rotate(180deg);
    }

    .nav-badge {
        background-color: var(--danger) !important;
        color: white !important;
        font-size: 10px !important;
        padding: 2px 7px !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        margin-left: 10px !important;
    }

    .nav-badge-sm {
        padding: 1px 5px !important;
        font-size: 9px !important;
    }

    /* Sidebar Footer */
    .sidebar-footer {
        padding: 20px 10px;
        border-top: 2px solid #d1d5db;
        background-color: #e6e9ee;
    }

    .help-card {
        background-color: var(--secondary);
        padding: 15px;
        border-radius: 12px;
        display: flex;
        align-items: center;
    }

    .help-icon {
        width: 40px;
        height: 40px;
        background-color: var(--primary-light);
        color: var(--primary-dark);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin-right: 15px;
    }

    .help-content h6 {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .help-content p {
        font-size: 12px;
        color: var(--gray-600);
        margin-bottom: 5px;
    }

    .help-link {
        color: var(--primary);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }

    /* Logout Form & Button Styles */
    .logout-form {
        margin-top: 15px;
        padding-top: 5px;
    }

    .logout-button {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px;
        background-color: transparent;
        border: 1px solid var(--gray-300);
        border-radius: 12px;
        color: var(--gray-700);
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        gap: 10px;
    }

    .logout-button:hover {
        background-color: #fee2e2;
        border-color: #fca5a5;
        color: #ef4444;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.1);
    }

    .logout-button i {
        font-size: 16px;
    }

    /* Enhanced Mobile Responsiveness */
    @media (max-width: 576px) {
        :root {
            --sidebar-width: 260px;
        }

        .sidebar-footer {
            padding: 15px 12px;
        }

        .logout-form {
            margin-top: 12px;
        }

        .logout-button {
            padding: 10px;
            font-size: 13px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize submenus state
        const submenus = document.querySelectorAll('.submenu');
        submenus.forEach(function(submenu) {
            if (submenu.classList.contains('show')) {
                submenu.style.setProperty('display', 'block', 'important');
            } else {
                submenu.style.setProperty('display', 'none', 'important');
            }
        });

        // Handle dropdown toggles
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
        dropdownToggles.forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const targetId = this.getAttribute('href').replace('#', '');
                const target = document.getElementById(targetId);

                if (target) {
                    const isCurrentlyHidden = target.style.display === 'none' || !target.classList.contains('show');

                    if (isCurrentlyHidden) {
                        // Show it
                        target.style.setProperty('display', 'block', 'important');
                        target.classList.add('show');
                        this.setAttribute('aria-expanded', 'true');
                    } else {
                        // Hide it
                        target.style.setProperty('display', 'none', 'important');
                        target.classList.remove('show');
                        this.setAttribute('aria-expanded', 'false');
                    }
                }
            });
        });
    });
</script>
