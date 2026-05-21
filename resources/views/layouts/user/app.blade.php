<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Home') - HumbleKos</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon-humblekos.png') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-aplikasi-humblekos.png') }}">
    
    <!-- Mobile Web App Meta -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="HumbleKos">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="HumbleKos">
    <meta name="theme-color" content="#4f6f52">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Swiper Slider CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Vite CSS & JS (automatically handles dev/production) -->
    @vite(['resources/css/fonts.css', 'resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
    <style>
        /* ===== CRITICAL CSS - FALLBACK ===== */
        :root {
            --sidebar-width: 280px;
            --header-height: 72px;
            --primary: #4f6f52;
            --primary-dark: #3a4d39;
            --primary-light: #a4c3a2;
            --secondary: #eef5e4;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-600: #6c757d;
            --gray-700: #495057;
            --gray-900: #212529;
            --transition-speed: 0.3s;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background-color: #f8f9fa;
            color: var(--gray-900);
        }

        /* Sidebar - Mobile Hidden */
        .main-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .content-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: margin-left var(--transition-speed);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1;
            padding: 30px;
            padding-top: var(--header-height);
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 12px 10px;
                padding-top: calc(var(--header-height) + 4px);
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 8px 6px;
                padding-top: calc(var(--header-height) + 2px);
            }
        }

        /* Mobile Overlay */
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1030;
            display: none;
            transition: all var(--transition-speed);
        }

        /* Mobile: Hide sidebar & adjust layout */
        @media (max-width: 992px) {
            .content-wrapper {
                margin-left: 0;
                width: 100%;
            }

            .user-sidebar {
                transform: translateX(-100%);
            }

            body.sidebar-active .mobile-overlay {
                display: block !important;
            }

            body.sidebar-active .user-sidebar {
                transform: translateX(0) !important;
            }
        }

        /* Footer */
        .main-footer {
            background-color: var(--primary-dark);
            color: white;
            padding: 20px 0 10px;
            margin-top: auto;
        }

        .footer-logo {
            width: 120px;
            margin-bottom: 10px;
        }

        .footer-links h5 {
            color: #f8f9fa;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .footer-links ul {
            list-style: none;
            padding-left: 0;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.2s;
            font-size: 13px;
        }

        .footer-links a:hover {
            color: var(--primary-light);
        }

        .copyright {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            padding-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 20px;
            text-align: center;
        }

        /* Hamburger Button */
        .navbar-toggler {
            border: none !important;
            padding: 0.15rem 0.4rem !important;
            font-size: 1.1rem !important;
            cursor: pointer;
            color: var(--primary-dark) !important;
            transition: all 0.3s ease !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 36px;
            height: 36px;
            -webkit-tap-highlight-color: transparent;
            min-width: 36px;
            margin-right: 4px;
        }

        .navbar-toggler:focus {
            box-shadow: none !important;
            outline: none !important;
        }

        .navbar-toggler:active,
        .navbar-toggler:hover {
            background-color: rgba(79, 111, 82, 0.1) !important;
            color: var(--primary) !important;
        }

        @media (max-width: 480px) {
            .navbar-toggler {
                font-size: 0.95rem !important;
                width: 32px !important;
                height: 32px !important;
                padding: 0.1rem 0.3rem !important;
                margin-right: 2px;
            }
        }
    </style>
</head>

<body>
    <div class="main-wrapper @yield('body-class')">
        <!-- Mobile Overlay -->
        <div class="mobile-overlay" id="mobileOverlay"></div>

        <!-- Sidebar -->
        @include('layouts.user.sidebar')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Header -->
            @include('layouts.user.header')

            <!-- Main Content -->
            <div class="main-content">
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="main-footer custom-height">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4 mb-3 mb-lg-0">
                            <img src="{{ asset('images/hk-3.png') }}" alt="HumbleKos" class="footer-logo">
                            <p class="footer-description">Temukan kos-kosan terbaik untuk kebutuhan hunian Anda.</p>
                            <div class="social-icons">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-youtube"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-3 mb-md-0">
                            <div class="footer-links">
                                <h5>Perusahaan</h5>
                                <ul>
                                    <li class="{{ request()->routeIs('tentang-kami.*') ? 'active' : '' }}">
                                        <a href="{{ route('users.tentang-kami.index') }}">Tentang Kami</a>
                                    </li>
                                    <li><a href="#">Tim</a></li>
                                    <li><a href="#">Kontak</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-3 mb-md-0">
                            <div class="footer-links">
                                <h5>Pengguna</h5>
                                <ul>
                                    <li><a href="#">Cara Booking</a></li>
                                    <li><a href="#">Kebijakan Privasi</a></li>
                                    <li><a href="#">FAQ</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-3 mb-md-0">
                            <div class="footer-links">
                                <h5>Pemilik Kos</h5>
                                <ul>
                                    <li><a href="#">Daftarkan Kos</a></li>
                                    <li><a href="#">Cara Kerja</a></li>
                                    <li><a href="#">Bantuan</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="footer-links">
                                <h5>Pembayaran</h5>
                                <ul>
                                    <li><a href="#">Metode Pembayaran</a></li>
                                    <li><a href="#">Ketentuan Biaya</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="copyright text-center">
                        <p>&copy; {{ date('Y') }} HumbleKos. Full Copyright.</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

    @stack('scripts')

    <!-- Auto-hide Alert Messages Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Find all alert messages
            const alerts = document.querySelectorAll('.alert');

            alerts.forEach(function(alert) {
                // Auto-hide after 10 seconds (10000ms)
                setTimeout(function() {
                    // Add fade out animation
                    alert.style.transition = 'opacity 0.5s ease-out';
                    alert.style.opacity = '0';

                    // Remove from DOM after animation completes
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 10000);
            });
        });
    </script>

    <!-- Clean Sidebar Toggle Script -->
    <script>
        (function() {
            function initSidebarToggle() {
                const sidebarToggle = document.getElementById('sidebarToggle');
                const closeSidebar = document.getElementById('closeSidebar');
                const mobileOverlay = document.getElementById('mobileOverlay');
                const body = document.body;

                if (!sidebarToggle || sidebarToggle.dataset.initialized) return;
                sidebarToggle.dataset.initialized = "true";

                // Single reliable click handler
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    body.classList.toggle('sidebar-active');
                });

                if (closeSidebar) {
                    closeSidebar.addEventListener('click', function(e) {
                        e.preventDefault();
                        body.classList.remove('sidebar-active');
                    });
                }

                if (mobileOverlay) {
                    mobileOverlay.addEventListener('click', function(e) {
                        body.classList.remove('sidebar-active');
                    });
                }

                // Close sidebar when clicking nav links on mobile
                const navLinks = document.querySelectorAll('.nav-link, .sidebar-link');
                navLinks.forEach(function(link) {
                    link.addEventListener('click', function() {
                        const isDropdownToggle = this.classList.contains('dropdown-toggle');
                        if (window.innerWidth <= 992 && !isDropdownToggle) {
                            body.classList.remove('sidebar-active');
                        }
                    });
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initSidebarToggle);
            } else {
                initSidebarToggle();
            }
            // Fallback for late-rendering
            setTimeout(initSidebarToggle, 500);
        })();
    </script>
</body>

</html>
