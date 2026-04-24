@extends('layouts.admin.app')
@section('title', 'Dashboard')

@section('breadcrumb')

@section('page-title', 'Dashboard')

@section('content')
    <!-- Welcome Section -->
    <div class="welcome-section p-4 bg-white rounded shadow-sm mb-4">
        <div class="d-flex align-items-center">
            <div class="avatar-container me-3">
                <div class="avatar-circle">
                    <span>{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
            </div>
            <div>
                <h2 class="welcome-title mb-1">Selamat Datang, {{ Auth::user()->name }}!</h2>
                <p class="text-muted mb-0">Ini adalah dashboard admin HumbleKos. Kelola semua data kos, pengguna, dan
                    transaksi dari sini.</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex align-items-center w-100">
                        <div class="grow me-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pengguna</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_users']) }}
                            </div>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded ms-auto">
                            <i class="fas fa-users fs-4 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex align-items-center w-100">
                        <div class="grow me-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Kosan</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_kosan']) }}
                            </div>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded ms-auto">
                            <i class="fas fa-home fs-4 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex align-items-center w-100">
                        <div class="grow me-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Booking</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_bookings']) }}</div>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded ms-auto">
                            <i class="fas fa-calendar-check fs-4 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex align-items-center w-100">
                        <div class="grow me-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Pendapatan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp
                                {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded ms-auto">
                            <i class="fas fa-money-bill-wave fs-4 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Statistics -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 col-6 mb-3">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex align-items-center w-100">
                        <div class="grow me-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Booking Pending</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['pending_bookings']) }}</div>
                        </div>
                        <div class="bg-secondary bg-opacity-10 p-3 rounded ms-auto">
                            <i class="fas fa-clock fs-4 text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-6 mb-3">
            <div class="card border-left-emerald shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex align-items-center w-100">
                        <div class="grow me-2">
                            <div class="text-xs font-weight-bold text-emerald text-uppercase mb-1">Booking Confirmed</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['confirmed_bookings']) }}</div>
                        </div>
                        <div class="bg-emerald bg-opacity-10 p-3 rounded ms-auto" style="background-color: rgba(16, 185, 129, 0.1);">
                            <i class="fas fa-check-circle fs-4 text-emerald" style="color: #10b981;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex align-items-center w-100">
                        <div class="grow me-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Booking Cancelled</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['cancelled_bookings']) }}</div>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded ms-auto">
                            <i class="fas fa-times-circle fs-4 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity and Charts -->
    <div class="row">
        <!-- Recent Activity -->
        <div class="col-lg-8 mb-4">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5 class="admin-card-title">Aktivitas Terbaru</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="admin-card-body p-0">
                    <ul class="activity-list list-group list-group-flush">
                        @forelse ($recentActivities as $activity)
                            <li class="list-group-item d-flex align-items-center py-3">
                                @if ($activity->activity_type == 'booking')
                                    <div class="activity-icon me-3 bg-warning bg-opacity-10 text-warning">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>

                                    <div class="activity-content grow">
                                        <p class="mb-0 fw-medium">Booking Baru</p>
                                        <p class="text-muted small mb-0">
                                            {{ $activity->pengguna->name ?? 'Pengguna' }} melakukan booking di
                                            {{ $activity->kosan->nama_kosan ?? 'Kosan' }}.
                                        </p>
                                    </div>

                                    <div class="activity-time small text-muted">
                                        {{ optional($activity->created_at)->diffForHumans() }}
                                    </div>
                                @elseif ($activity->activity_type == 'payment')
                                    @php
                                        $payUi = match ($activity->status_pembayaran) {
                                            'paid' => [
                                                'bg' => 'bg-success bg-opacity-10 text-success',
                                                'icon' => 'fa-money-bill-wave',
                                            ],
                                            'pending' => [
                                                'bg' => 'bg-warning bg-opacity-10 text-warning',
                                                'icon' => 'fa-clock',
                                            ],
                                            'failed' => [
                                                'bg' => 'bg-danger bg-opacity-10 text-danger',
                                                'icon' => 'fa-exclamation-circle',
                                            ],
                                            default => [
                                                'bg' => 'bg-secondary bg-opacity-10 text-secondary',
                                                'icon' => 'fa-circle-info',
                                            ],
                                        };
                                    @endphp

                                    <div class="activity-icon me-3 {{ $payUi['bg'] }}">
                                        <i class="fas {{ $payUi['icon'] }}"></i>
                                    </div>

                                    <div class="activity-content grow">
                                        <p class="mb-0 fw-medium">
                                            Pembayaran
                                            {{ $activity->status_label ?? ucfirst($activity->status_pembayaran) }}
                                        </p>

                                        <p class="text-muted small mb-0">
                                            Rp {{ number_format($activity->jumlah_bayar, 0, ',', '.') }} oleh
                                            {{ optional($activity->booking)->user->nama_lengkap ?? (optional($activity->booking)->user->username ?? 'Pengguna') }}
                                            untuk {{ optional($activity->booking)->kosan->nama_kosan ?? 'Kosan' }}.
                                        </p>
                                    </div>

                                    <div class="activity-time small text-muted">
                                        {{ optional($activity->created_at)->diffForHumans() }}
                                    </div>
                                @endif
                            </li>
                        @empty
                            <li class="list-group-item text-center py-3">
                                <p class="text-muted mb-0">Tidak ada aktivitas terbaru.</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="col-lg-4 mb-4">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <h5 class="admin-card-title">Statistik Booking</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm text-muted p-0" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Minggu Ini</a>
                            <a class="dropdown-item" href="#">Bulan Ini</a>
                            <a class="dropdown-item" href="#">Tahun Ini</a>
                        </div>
                    </div>
                </div>
                <div class="admin-card-body">
                    <canvas id="bookingChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Tasks and News -->
    <div class="row">
        <!-- Tasks -->
        <div class="col-lg-6 mb-4">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <h5 class="admin-card-title">Tugas yang Akan Datang</h5>
                    <a href="#" class="btn btn-sm btn-primary">+ Tambah Tugas</a>
                </div>

                <div class="admin-card-body p-0">
                    <ul class="task-list list-group list-group-flush">
                        @forelse ($upcomingTasks as $task)
                            @php
                                $isToday = optional($task->created_at)->isToday();
                                $badgeClass = $isToday ? 'bg-warning' : 'bg-info';
                                $badgeText = $isToday ? 'Hari Ini' : optional($task->created_at)->diffForHumans();
                            @endphp

                            <li class="list-group-item d-flex align-items-center py-3">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" id="task-{{ $task->id }}"
                                        @if (isset($task->link)) onclick="window.location='{{ $task->link }}'" @endif>
                                </div>

                                <div class="task-content grow">
                                    <label class="form-check-label fw-medium mb-0" for="task-{{ $task->id }}">
                                        @php
                                            $taskTitleId = match ($task->title) {
                                                'Approve Pembayaran Pending' => 'Setujui Pembayaran (Menunggu)',
                                                'Verify Kosan' => 'Verifikasi Kosan',
                                                default => $task->title,
                                            };
                                        @endphp

                                        {{ $taskTitleId }}
                                    </label>
                                    <p class="text-muted small mb-0">
                                        {{ $task->description }}
                                    </p>
                                </div>

                                <span class="badge {{ $badgeClass }}">
                                    {{ $badgeText }}
                                </span>
                            </li>
                        @empty
                            <li class="list-group-item text-center py-3">
                                <p class="text-muted mb-0">Tidak ada tugas yang akan datang.</p>
                            </li>
                        @endforelse
                    </ul>
                </div>

                <div class="admin-card-footer text-center">
                    <a href="#" class="text-decoration-none">
                        Lihat semua tugas <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- News/Updates -->
        <div class="col-lg-6 mb-4">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <h5 class="admin-card-title">Update & Pengumuman</h5>
                    <span class="badge bg-primary">4 Baru</span>
                </div>
                <div class="admin-card-body p-0">
                    <div class="news-item p-3 border-bottom">
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="news-title mb-0">Pembaruan Sistem v2.1</h6>
                            <span class="badge bg-success">Baru</span>
                        </div>
                        <p class="news-desc text-muted small mb-2">Sistem pembayaran telah diupdate dengan fitur baru dan
                            peningkatan keamanan.</p>
                        <div class="news-date small text-muted">15 Maret 2025</div>
                    </div>
                    <div class="news-item p-3 border-bottom">
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="news-title mb-0">Promo Akhir Semester</h6>
                            <span class="badge bg-success">Baru</span>
                        </div>
                        <p class="news-desc text-muted small mb-2">Program promo untuk mahasiswa baru telah dimulai.
                            Pastikan untuk update info di dashboard.</p>
                        <div class="news-date small text-muted">12 Maret 2025</div>
                    </div>
                    <div class="news-item p-3 border-bottom">
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="news-title mb-0">Meeting Tim Admin</h6>
                            <span class="badge bg-success">Baru</span>
                        </div>
                        <p class="news-desc text-muted small mb-2">Meeting evaluasi bulanan akan diadakan pada tanggal 20
                            Maret 2025.</p>
                        <div class="news-date small text-muted">10 Maret 2025</div>
                    </div>
                    <div class="news-item p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="news-title mb-0">Perbaikan Sistem Notifikasi</h6>
                            <span class="badge bg-success">Baru</span>
                        </div>
                        <p class="news-desc text-muted small mb-2">Bug pada sistem notifikasi email telah diperbaiki.
                            Silahkan test kembali.</p>
                        <div class="news-date small text-muted">8 Maret 2025</div>
                    </div>
                </div>
                <div class="admin-card-footer text-center">
                    <a href="#" class="text-decoration-none">Lihat semua update <i
                            class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-actions')
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('admin.manajemen-kosan.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Kos
        </a>
    </div>
@endsection

@push('styles')
    <style>
        /* Custom Admin Card Styles */
        .admin-card {
            background-color: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            box-shadow: 0 0.1rem 1rem 0 rgba(58, 59, 69, 0.1);
            display: flex;
            flex-direction: column;
        }

        .admin-card-header {
            padding: 0.75rem 1.25rem;
            margin-bottom: 0;
            background-color: #e6e9ee;
            border-bottom: 1px solid #e3e6f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top-left-radius: 0.35rem;
            border-top-right-radius: 0.35rem;
        }

        .admin-card-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #4e73df;
        }

        .admin-card-body {
            padding: 1.25rem;
            flex: 1 1 auto;
        }

        .admin-card-footer {
            padding: 0.75rem 1.25rem;
            background-color: #f8f9fc;
            border-top: 1px solid #e3e6f0;
            border-bottom-left-radius: 0.35rem;
            border-bottom-right-radius: 0.35rem;
        }

        /* Border Left Card Styles */
        .text-xs {
            font-size: 0.7rem;
        }

        .font-weight-bold {
            font-weight: 700 !important;
        }

        .no-gutters {
            margin-right: 0;
            margin-left: 0;
        }

        .no-gutters>.col,
        .no-gutters>[class*="col-"] {
            padding-right: 0;
            padding-left: 0;
        }

        /* Welcome Section Styles */
        .welcome-section {
            background: linear-gradient(to right, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)), url('{{ asset('admin/img/bg-pattern.jpg') }}');
            background-size: cover;
            border-left: 5px solid #4e73df;
        }

        .avatar-circle {
            width: 60px;
            height: 60px;
            background-color: #4e73df;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .welcome-title {
            font-size: 22px;
            font-weight: 600;
            color: var(--gray-800);
        }

        /* Data Card Additional Styles */
        .data-card-label {
            font-size: 14px;
            color: var(--gray-500);
        }

        /* Activity List Styles */
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Task List Styles */
        .task-list .form-check-input:checked+.task-content label {
            text-decoration: line-through;
            color: var(--gray-500);
        }

        /* News Styles */
        .news-item {
            transition: background-color 0.2s;
        }

        .news-item:hover {
            background-color: var(--gray-100);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Booking Chart
            const ctx = document.getElementById('bookingChart').getContext('2d');
            const bookingChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Dikonfirmasi', 'Tertunda', 'Dibatalkan'],
                    datasets: [{
                        data: [{{ $stats['confirmed_bookings'] }},
                            {{ $stats['pending_bookings'] }},
                            {{ $stats['cancelled_bookings'] }}
                        ],
                        backgroundColor: [
                            '#10b981', // success
                            '#f59e0b', // warning
                            '#ef4444' // danger
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 15
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 10,
                            cornerRadius: 5,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw + '%';
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });

            // Task Checkbox Effect
            const checkboxes = document.querySelectorAll('.task-list .form-check-input');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const label = this.closest('.list-group-item').querySelector(
                        '.form-check-label');
                    if (this.checked) {
                        label.style.textDecoration = 'line-through';
                        label.style.color = 'var(--gray-500)';
                    } else {
                        label.style.textDecoration = 'none';
                        label.style.color = '';
                    }
                });
            });
        });
    </script>
@endpush
