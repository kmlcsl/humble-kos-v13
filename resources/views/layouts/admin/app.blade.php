<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - HumbleKos Admin</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon-humblekos.png') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-aplikasi-humblekos.png') }}">
    
    <!-- Mobile Web App Meta -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="HumbleKos Admin">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="HumbleKos Admin">
    <meta name="theme-color" content="#4f6f52">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/admin-core.css') }}">
    <script src="{{ asset('js/admin-core.js') }}" defer></script>
    @stack('styles')

    <style>
        @media (min-width: 992px) {
            /* 1. General Collapsed Behavior */
            .sidebar-collapsed .admin-sidebar {
                width: var(--admin-sidebar-collapsed-width);
                overflow: hidden !important;
            }
            .sidebar-collapsed .sidebar-link span,
            .sidebar-collapsed .sidebar-link .dropdown-indicator {
                display: none;
            }
            .sidebar-collapsed .sidebar-link {
                justify-content: center;
            }
            .sidebar-collapsed .admin-main {
                margin-left: var(--admin-sidebar-collapsed-width);
            }

            /* 2. Header */
            .sidebar-collapsed .sidebar-header {
                text-align: center;
            }
            .sidebar-collapsed .sidebar-header .full-title {
                display: none;
            }
            .sidebar-collapsed .sidebar-header .short-title {
                display: inline !important;
                margin-left: 0 !important;
            }

            /* 3. Admin Profile */
            .sidebar-collapsed .admin-profile .ms-3 {
                display: none;
            }
            .sidebar-collapsed .admin-profile {
                justify-content: center;
                align-items: center;
                padding: 0.75rem !important;
                min-height: 50px;
            }
            .sidebar-collapsed .admin-profile::before {
                content: '{{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 1)) }}';
                color: white;
                font-weight: 600;
                font-size: 1.5rem;
                line-height: 1;
            }

            /* 4. Logout Button */
            .sidebar-collapsed .sidebar-footer .btn {
                font-size: 0;
            }
            .sidebar-collapsed .sidebar-footer .btn > .fas {
                font-size: 1rem;
                margin: 0 !important;
            }
        }

        .short-title {
            display: none;
        }
    </style>
</head>

<body class="admin-dashboard-body">
    <div class="admin-container @yield('sidebar-state')">
        <!-- Mobile Overlay -->
        <div class="mobile-overlay" id="mobileOverlay"></div>

        <!-- Sidebar -->
        @include('layouts.admin.sidebar')

        <!-- Main Content -->
        <div class="admin-main">
            <!-- Header -->
            @include('layouts.admin.header')

            <!-- Content Area -->
            <div class="admin-content">
                <!-- Breadcrumb -->
                <div class="breadcrumb-container">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                </div>

                <!-- Page Title -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0 text-gray-800">@yield('page-title')</h1>
                    <div>
                        @yield('page-actions')
                    </div>
                </div>

                <!-- Content -->
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Toggle Sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            const mobileToggleBtn = document.getElementById('mobileSidebarToggle');
            const closeSidebarBtn = document.getElementById('closeSidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');
            const adminContainer = document.querySelector('.admin-container');

            function toggleSidebar() {
                const isMobile = window.innerWidth < 992;
                adminContainer.classList.toggle('sidebar-collapsed');

                // Prevent body scroll on mobile when sidebar is open
                if (isMobile) {
                    if (adminContainer.classList.contains('sidebar-collapsed')) {
                        document.body.style.overflow = 'hidden';
                    } else {
                        document.body.style.overflow = '';
                    }
                }
            }

            function closeSidebar() {
                adminContainer.classList.remove('sidebar-collapsed');
                document.body.style.overflow = '';
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', toggleSidebar);
            }

            if (mobileToggleBtn) {
                mobileToggleBtn.addEventListener('click', toggleSidebar);
            }

            if (closeSidebarBtn) {
                closeSidebarBtn.addEventListener('click', closeSidebar);
            }

            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', closeSidebar);
            }

            // Close sidebar on window resize if switched to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    document.body.style.overflow = '';
                }
            });

            // Custom Dropdown Toggle untuk Sidebar
            const dropdownToggles = document.querySelectorAll('.sidebar-dropdown-toggle');

            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Get target dropdown
                    const targetId = this.getAttribute('data-target');
                    const dropdown = document.getElementById(targetId);

                    if (dropdown) {
                        // Toggle show class
                        dropdown.classList.toggle('show');

                        // Update aria-expanded
                        const isExpanded = dropdown.classList.contains('show');
                        this.setAttribute('aria-expanded', isExpanded);

                        // Close other dropdowns
                        dropdownToggles.forEach(otherToggle => {
                            if (otherToggle !== this) {
                                const otherTargetId = otherToggle.getAttribute(
                                    'data-target');
                                const otherDropdown = document.getElementById(
                                otherTargetId);
                                if (otherDropdown) {
                                    otherDropdown.classList.remove('show');
                                    otherToggle.setAttribute('aria-expanded', 'false');
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>

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

    @stack('scripts')
</body>

</html>
