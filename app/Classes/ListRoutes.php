<?php

namespace App\Classes;

class ListRoutes
{
    function getDataAuth($index = null)
    {
        $data = [
            //START AUTHENTICATION
            // Root
            [
                'title' => 'Root',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/',
                        'controller' => 'UserDashboardController@index',
                        'name' => 'root',
                        'middleware' => '',
                        'public' => true,
                    ]
                ]
            ],

            // Rute untuk Registrasi User
            [
                'title' => 'User Registration',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/register',
                        'controller' => 'Auth\RegisterController@showRegistrationForm',
                        'name' => 'register',
                        'middleware' => 'guest:web',
                    ],
                    [
                        'type' => 'store',
                        'method' => 'post',
                        'url' => '/register',
                        'controller' => 'Auth\RegisterController@register',
                        'name' => 'register.submit',
                        'middleware' => 'guest:web',
                    ],
                ],
            ],

            // User Login
            [
                'title' => 'User Login',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/login',
                        'controller' => 'Auth\UserLoginController@showLoginForm',
                        'name' => 'login',
                        'middleware' => 'guest:web',
                        'public' => true,
                    ],
                    [
                        'type' => 'store',
                        'method' => 'post',
                        'url' => '/login',
                        'controller' => 'Auth\UserLoginController@login',
                        'name' => '',
                        'middleware' => 'guest:web',
                        'public' => true,
                    ]
                ]
            ],

            // Password Reset
            [
                'title' => 'Password Reset',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/password/reset',
                        'controller' => 'Auth\ForgotPasswordController@showLinkRequestForm',
                        'name' => 'password.request',
                        'middleware' => 'guest:web',
                        'public' => true,
                    ],
                    [
                        'type' => 'store',
                        'method' => 'post',
                        'url' => '/password/email',
                        'controller' => 'Auth\ForgotPasswordController@sendResetLinkEmail',
                        'name' => 'password.email',
                        'middleware' => 'guest:web',
                        'public' => true,
                    ],
                    [
                        'type' => 'edit',
                        'method' => 'get',
                        'url' => '/password/reset/{token}',
                        'controller' => 'Auth\ResetPasswordController@showResetForm',
                        'name' => 'password.reset',
                        'middleware' => 'guest:web',
                        'public' => true,
                    ],
                    [
                        'type' => 'update',
                        'method' => 'post',
                        'url' => '/password/reset',
                        'controller' => 'Auth\ResetPasswordController@reset',
                        'name' => 'password.update',
                        'middleware' => 'guest:web',
                        'public' => true,
                    ],
                ]
            ],

            // Admin Login
            [
                'title' => 'Admin Login (Admin/Pemilik Login)',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/admin/login',
                        'controller' => 'Auth\AdminPemilikLoginController@showLoginForm',
                        'name' => 'admin.login',
                        'middleware' => '',
                        'public' => true,
                    ],
                    [
                        'type' => 'store',
                        'method' => 'post',
                        'url' => '/admin/login',
                        'controller' => 'Auth\AdminPemilikLoginController@login',
                        'name' => '',
                        'middleware' => '',
                        'public' => true,
                    ]
                ]
            ],

            // Logout
            [
                'title' => 'Logout | Admin',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'post',
                        'url' => '/admin/logout',
                        'controller' => 'Auth\AdminPemilikLoginController@logout',
                        'name' => 'admin.logout',
                        'middleware' => 'auth:admin',
                    ]
                ]
            ],
            [
                'title' => 'Logout | Pemilik',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'post',
                        'url' => '/pemilik/logout',
                        'controller' => 'Auth\AdminPemilikLoginController@logout',
                        'name' => 'pemilik.logout',
                        'middleware' => 'auth:web',
                    ]
                ]
            ],
            [
                'title' => 'Logout | User',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'post',
                        'url' => '/logout',
                        'controller' => 'Auth\UserLoginController@logout',
                        'name' => 'logout',
                        'middleware' => 'auth:web',
                    ]
                ]
            ],
            //END AUTHENTICATION

            //START ADMIN
            // Admin Dashboard
            [
                'title' => 'Admin Dashboard',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/admin/dashboard',
                        'controller' => 'Admin\AdminDashboardController@index',
                        'name' => 'admin.dashboard',
                        'middleware' => 'auth:admin',
                        'public' => false,
                    ]
                ]
            ],

            // Manajemen Kosan untuk Admin
            [
                'title' => 'Admin Manajemen Kosan',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/admin/manajemenkosan',
                        'controller' => 'Admin\AdminKosanController@index',
                        'name' => 'admin.manajemen-kosan.index',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'create',
                        'method' => 'get',
                        'url' => '/admin/manajemenkosan/create',
                        'controller' => 'Admin\AdminKosanController@create',
                        'name' => 'admin.manajemen-kosan.create',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'store',
                        'method' => 'post',
                        'url' => '/admin/manajemenkosan',
                        'controller' => 'Admin\AdminKosanController@store',
                        'name' => 'admin.manajemen-kosan.store',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'show',
                        'method' => 'get',
                        'url' => '/admin/manajemenkosan/{kosan_id}',
                        'controller' => 'Admin\AdminKosanController@show',
                        'name' => 'admin.manajemen-kosan.show',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'update',
                        'method' => 'get,put',
                        'url' => '/admin/manajemenkosan/{kosan_id}/update',
                        'controller' => 'Admin\AdminKosanController@update',
                        'name' => 'admin.manajemen-kosan.update',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/admin/manajemenkosan/{kosan_id}',
                        'controller' => 'Admin\AdminKosanController@destroy',
                        'name' => 'admin.manajemenkosan.delete',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'toggle-status',
                        'method' => 'post',
                        'url' => '/admin/manajemenkosan/{kosan_id}/toggle-status',
                        'controller' => 'Admin\AdminKosanController@toggleStatus',
                        'name' => 'admin.manajemenkosan.toggle-status',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'toggle-featured',
                        'method' => 'post',
                        'url' => '/admin/manajemenkosan/{kosan_id}/toggle-featured',
                        'controller' => 'Admin\AdminKosanController@toggleFeatured',
                        'name' => 'admin.manajemenkosan.toggle-featured',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'export',
                        'method' => 'get',
                        'url' => '/admin/manajemenkosan/export',
                        'controller' => 'Admin\ExportController@exportKosanExcel',
                        'name' => 'admin.manajemenkosan.export',
                        'middleware' => 'auth:admin',
                    ],
                ],
            ],

            // Manajemen Kamar untuk Admin
            [
                'title' => 'Admin Manajemen Kamar',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/admin/manajemen-kamar',
                        'controller' => 'Admin\AdminKamarController@index',
                        'name' => 'admin.manajemen-kamar.index',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'by-kosan',
                        'method' => 'get',
                        'url' => '/admin/manajemen-kamar/kosan/{id}',
                        'controller' => 'Admin\AdminKamarController@byKosan',
                        'name' => 'admin.manajemen-kamar.by-kosan',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'create',
                        'method' => 'get',
                        'url' => '/admin/manajemen-kamar/create',
                        'controller' => 'Admin\AdminKamarController@create',
                        'name' => 'admin.manajemen-kamar.create',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'store',
                        'method' => 'post',
                        'url' => '/admin/manajemen-kamar',
                        'controller' => 'Admin\AdminKamarController@store',
                        'name' => 'admin.manajemen-kamar.store',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'show',
                        'method' => 'get',
                        'url' => '/admin/manajemen-kamar/{id}',
                        'controller' => 'Admin\AdminKamarController@show',
                        'name' => 'admin.manajemen-kamar.show',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'update',
                        'method' => 'get,put',
                        'url' => '/admin/manajemen-kamar/{id}/update',
                        'controller' => 'Admin\AdminKamarController@update',
                        'name' => 'admin.manajemen-kamar.update',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/admin/manajemen-kamar/{id}',
                        'controller' => 'Admin\AdminKamarController@destroy',
                        'name' => 'admin.manajemen-kamar.delete',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'change-status',
                        'method' => 'post',
                        'url' => '/admin/manajemen-kamar/{id}/change-status',
                        'controller' => 'Admin\AdminKamarController@changeStatus',
                        'name' => 'admin.manajemen-kamar.change-status',
                        'middleware' => 'auth:admin',
                    ],
                ],
            ],

            // Admin Manajemen Fasilitas
            [
                'title' => 'Admin Manajemen Fasilitas',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/admin/fasilitas',
                        'controller' => 'Admin\AdminFasilitasController@index',
                        'name' => 'admin.fasilitas.index',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'create',
                        'method' => 'get',
                        'url' => '/admin/fasilitas/create',
                        'controller' => 'Admin\AdminFasilitasController@create',
                        'name' => 'admin.fasilitas.create',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'store',
                        'method' => 'post',
                        'url' => '/admin/fasilitas',
                        'controller' => 'Admin\AdminFasilitasController@store',
                        'name' => 'admin.fasilitas.store',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'show',
                        'method' => 'get',
                        'url' => '/admin/fasilitas/{id}',
                        'controller' => 'Admin\AdminFasilitasController@show',
                        'name' => 'admin.fasilitas.show',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'update',
                        'method' => 'get,put',
                        'url' => '/admin/fasilitas/{id}/update',
                        'controller' => 'Admin\AdminFasilitasController@update',
                        'name' => 'admin.fasilitas.update',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/admin/fasilitas/{id}',
                        'controller' => 'Admin\AdminFasilitasController@destroy',
                        'name' => 'admin.fasilitas.delete',
                        'middleware' => 'auth:admin',
                    ],
                ],
            ],

            // Admin Manajemen Pembayaran
            [
                'title' => 'Admin Manajemen Pembayaran',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/admin/pembayaran',
                        'controller' => 'Admin\AdminPembayaranController@index',
                        'name' => 'admin.pembayaran.index',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'show',
                        'method' => 'get',
                        'url' => '/admin/pembayaran/{id}',
                        'controller' => 'Admin\AdminPembayaranController@show',
                        'name' => 'admin.pembayaran.show',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'approve',
                        'method' => 'post',
                        'url' => '/admin/pembayaran/{id}/approve',
                        'controller' => 'Admin\AdminPembayaranController@approve',
                        'name' => 'admin.pembayaran.approve',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'reject',
                        'method' => 'post',
                        'url' => '/admin/pembayaran/{id}/reject',
                        'controller' => 'Admin\AdminPembayaranController@reject',
                        'name' => 'admin.pembayaran.reject',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'pending',
                        'method' => 'get',
                        'url' => '/admin/pembayaran/pending',
                        'controller' => 'Admin\AdminPembayaranController@pending',
                        'name' => 'admin.pembayaran.pending',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'successful',
                        'method' => 'get',
                        'url' => '/admin/pembayaran/successful',
                        'controller' => 'Admin\AdminPembayaranController@successful',
                        'name' => 'admin.pembayaran.successful',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'expired',
                        'method' => 'get',
                        'url' => '/admin/pembayaran/expired',
                        'controller' => 'Admin\AdminPembayaranController@expired',
                        'name' => 'admin.pembayaran.expired',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'export-excel',
                        'method' => 'get',
                        'url' => '/admin/pembayaran/export/excel',
                        'controller' => 'Admin\ExportController@exportPembayaranExcel',
                        'name' => 'admin.pembayaran.export.excel',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'export-pdf',
                        'method' => 'get',
                        'url' => '/admin/pembayaran/export/pdf',
                        'controller' => 'Admin\ExportController@exportPembayaranPdf',
                        'name' => 'admin.pembayaran.export.pdf',
                        'middleware' => 'auth:admin',
                    ],
                ]
            ],

            // Admin Bookings Management
            [
                'title' => 'Admin Bookings',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/admin/bookings',
                        'controller' => 'Admin\AdminBookingController@index',
                        'name' => 'admin.bookings.index',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'pending',
                        'method' => 'get',
                        'url' => '/admin/bookings/pending',
                        'controller' => 'Admin\AdminBookingController@pending',
                        'name' => 'admin.bookings.pending',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'confirmed',
                        'method' => 'get',
                        'url' => '/admin/bookings/confirmed',
                        'controller' => 'Admin\AdminBookingController@confirmed',
                        'name' => 'admin.bookings.confirmed',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'cancelled',
                        'method' => 'get',
                        'url' => '/admin/bookings/cancelled',
                        'controller' => 'Admin\AdminBookingController@cancelled',
                        'name' => 'admin.bookings.cancelled',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'export-excel',
                        'method' => 'get',
                        'url' => '/admin/bookings/export/excel',
                        'controller' => 'Admin\ExportController@exportBookingExcel',
                        'name' => 'admin.bookings.export.excel',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'export-pdf',
                        'method' => 'get',
                        'url' => '/admin/bookings/export/pdf',
                        'controller' => 'Admin\ExportController@exportBookingPdf',
                        'name' => 'admin.bookings.export.pdf',
                        'middleware' => 'auth:admin',
                    ],
                ],
            ],

            // Admin Users Management
            [
                'title' => 'Admin Users',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/admin/users',
                        'controller' => 'Admin\AdminUserController@index',
                        'name' => 'admin.users.index',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'create',
                        'method' => 'get',
                        'url' => '/admin/users/create',
                        'controller' => 'Admin\AdminUserController@create',
                        'name' => 'admin.users.create',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'store',
                        'method' => 'post',
                        'url' => '/admin/users',
                        'controller' => 'Admin\AdminUserController@store',
                        'name' => 'admin.users.store',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'show',
                        'method' => 'get',
                        'url' => '/admin/users/{id}',
                        'controller' => 'Admin\AdminUserController@show',
                        'name' => 'admin.users.show',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'update',
                        'method' => 'get,put',
                        'url' => '/admin/users/{id}/update',
                        'controller' => 'Admin\AdminUserController@update',
                        'name' => 'admin.users.update',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/admin/users/{id}',
                        'controller' => 'Admin\AdminUserController@destroy',
                        'name' => 'admin.users.delete',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'update-password',
                        'method' => 'post',
                        'url' => '/admin/users/{id}/update-password',
                        'controller' => 'Admin\AdminUserController@updatePassword',
                        'name' => 'admin.users.update-password',
                        'middleware' => 'auth:admin',
                    ],
                ],
            ],

            // Admin Laporan (ringkasan booking dan pendapatan)
            [
                'title' => 'Admin Laporan',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/admin/laporan',
                        'controller' => 'Admin\AdminLaporanController@index',
                        'name' => 'admin.laporan.index',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'export-excel',
                        'method' => 'get',
                        'url' => '/admin/laporan/export/excel',
                        'controller' => 'Admin\ExportController@exportLaporanExcel',
                        'name' => 'admin.laporan.export.excel',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'export-pdf',
                        'method' => 'get',
                        'url' => '/admin/laporan/export/pdf',
                        'controller' => 'Admin\ExportController@exportLaporanPdf',
                        'name' => 'admin.laporan.export.pdf',
                        'middleware' => 'auth:admin',
                    ],
                ],
            ],

            // Admin Pengaturan
            [
                'title' => 'Admin Pengaturan',
                'item' => [
                    [
                        'type' => 'general',
                        'method' => 'get',
                        'url' => '/admin/pengaturan/general',
                        'controller' => 'Admin\AdminPengaturanController@general',
                        'name' => 'admin.pengaturan.general',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'appearance',
                        'method' => 'get',
                        'url' => '/admin/pengaturan/appearance',
                        'controller' => 'Admin\AdminPengaturanController@appearance',
                        'name' => 'admin.pengaturan.appearance',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'email',
                        'method' => 'get',
                        'url' => '/admin/pengaturan/email',
                        'controller' => 'Admin\AdminPengaturanController@email',
                        'name' => 'admin.pengaturan.email',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'admins',
                        'method' => 'get',
                        'url' => '/admin/pengaturan/admins',
                        'controller' => 'Admin\AdminPengaturanController@admins',
                        'name' => 'admin.pengaturan.admins',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'admins-create',
                        'method' => 'get',
                        'url' => '/admin/pengaturan/admins/create',
                        'controller' => 'Admin\AdminPengaturanController@create',
                        'name' => 'admin.pengaturan.admins.create',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'admins-store',
                        'method' => 'post',
                        'url' => '/admin/pengaturan/admins',
                        'controller' => 'Admin\AdminPengaturanController@store',
                        'name' => 'admin.pengaturan.admins.store',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'admins-update',
                        'method' => 'get,put',
                        'url' => '/admin/pengaturan/admins/{admin}/update',
                        'controller' => 'Admin\AdminPengaturanController@update',
                        'name' => 'admin.pengaturan.admins.update',
                        'middleware' => 'auth:admin',
                    ],
                    [
                        'type' => 'admins-destroy',
                        'method' => 'delete',
                        'url' => '/admin/pengaturan/admins/{admin}',
                        'controller' => 'Admin\AdminPengaturanController@destroy',
                        'name' => 'admin.pengaturan.admins.destroy',
                        'middleware' => 'auth:admin',
                    ],
                ],
            ],
            //END ADMIN

            //START PEMILIK
            // Pemilik Kos Dashboard
            [
                'title' => 'Pemilik Kos Dashboard',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/pemilik/dashboard',
                        'controller' => 'Pemilik\PemilikDashboardController@index',
                        'name' => 'pemilik.dashboard',
                        'middleware' => 'pemilik_kos',
                        'public' => false,
                    ],
                ]
            ],

            // Pemilik Kos - Manajemen Kos
            [
                'title' => 'Pemilik Manajemen Kos',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/pemilik/kosan',
                        'controller' => 'Pemilik\PemilikKosanController@index',
                        'name' => 'pemilik.kosan.index',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'create',
                        'method' => 'get',
                        'url' => '/pemilik/kosan/create',
                        'controller' => 'Pemilik\PemilikKosanController@create',
                        'name' => 'pemilik.kosan.create',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'store',
                        'method' => 'post',
                        'url' => '/pemilik/kosan',
                        'controller' => 'Pemilik\PemilikKosanController@store',
                        'name' => 'pemilik.kosan.store',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'show',
                        'method' => 'get',
                        'url' => '/pemilik/kosan/{id}',
                        'controller' => 'Pemilik\PemilikKosanController@show',
                        'name' => 'pemilik.kosan.show',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'update',
                        'method' => 'get,put',
                        'url' => '/pemilik/kosan/{id}/update',
                        'controller' => 'Pemilik\PemilikKosanController@update',
                        'name' => 'pemilik.kosan.update',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/pemilik/kosan/{id}',
                        'controller' => 'Pemilik\PemilikKosanController@destroy',
                        'name' => 'pemilik.kosan.delete',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'upload-galeri',
                        'method' => 'post',
                        'url' => '/pemilik/kosan/{id}/galeri',
                        'controller' => 'Pemilik\PemilikKosanController@uploadGaleri',
                        'name' => 'pemilik.kosan.upload-galeri',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'delete-galeri',
                        'method' => 'delete',
                        'url' => '/pemilik/kosan/{id}/galeri/{foto}',
                        'controller' => 'Pemilik\PemilikKosanController@deleteGaleri',
                        'name' => 'pemilik.kosan.delete-galeri',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'export',
                        'method' => 'get',
                        'url' => '/pemilik/kosan/export',
                        'controller' => 'Pemilik\ExportController@exportKosanExcel',
                        'name' => 'pemilik.kosan.export',
                        'middleware' => 'pemilik_kos',
                    ],
                ],
            ],

            // Pemilik Kos - Manajemen Kamar
            [
                'title' => 'Pemilik Manajemen Kamar',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/pemilik/kamar',
                        'controller' => 'Pemilik\PemilikKamarController@index',
                        'name' => 'pemilik.kamar.index',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'by-kosan',
                        'method' => 'get',
                        'url' => '/pemilik/kamar/kosan/{id}',
                        'controller' => 'Pemilik\PemilikKamarController@byKosan',
                        'name' => 'pemilik.kamar.by-kosan',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'create',
                        'method' => 'get',
                        'url' => '/pemilik/kamar/create',
                        'controller' => 'Pemilik\PemilikKamarController@create',
                        'name' => 'pemilik.kamar.create',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'store',
                        'method' => 'post',
                        'url' => '/pemilik/kamar',
                        'controller' => 'Pemilik\PemilikKamarController@store',
                        'name' => 'pemilik.kamar.store',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'show',
                        'method' => 'get',
                        'url' => '/pemilik/kamar/{id}',
                        'controller' => 'Pemilik\PemilikKamarController@show',
                        'name' => 'pemilik.kamar.show',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'update',
                        'method' => 'get,put',
                        'url' => '/pemilik/kamar/{id}/update',
                        'controller' => 'Pemilik\PemilikKamarController@update',
                        'name' => 'pemilik.kamar.update',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/pemilik/kamar/{id}',
                        'controller' => 'Pemilik\PemilikKamarController@destroy',
                        'name' => 'pemilik.kamar.delete',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'change-status',
                        'method' => 'post',
                        'url' => '/pemilik/kamar/{id}/change-status',
                        'controller' => 'Pemilik\PemilikKamarController@changeStatus',
                        'name' => 'pemilik.kamar.change-status',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'update-fasilitas',
                        'method' => 'post',
                        'url' => '/pemilik/kamar/{id}/fasilitas',
                        'controller' => 'Pemilik\PemilikKamarController@updateFasilitas',
                        'name' => 'pemilik.kamar.update-fasilitas',
                        'middleware' => 'pemilik_kos',
                    ],
                ],
            ],

            // Pemilik Kos - Manajemen Booking
            [
                'title' => 'Pemilik Manajemen Booking',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/pemilik/bookings',
                        'controller' => 'Pemilik\PemilikBookingController@index',
                        'name' => 'pemilik.bookings.index',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'show',
                        'method' => 'get',
                        'url' => '/pemilik/bookings/{id}',
                        'controller' => 'Pemilik\PemilikBookingController@show',
                        'name' => 'pemilik.bookings.show',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'pending',
                        'method' => 'get',
                        'url' => '/pemilik/bookings/status/pending',
                        'controller' => 'Pemilik\PemilikBookingController@pending',
                        'name' => 'pemilik.bookings.pending',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'confirmed',
                        'method' => 'get',
                        'url' => '/pemilik/bookings/status/confirmed',
                        'controller' => 'Pemilik\PemilikBookingController@confirmed',
                        'name' => 'pemilik.bookings.confirmed',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'cancelled',
                        'method' => 'get',
                        'url' => '/pemilik/bookings/status/cancelled',
                        'controller' => 'Pemilik\PemilikBookingController@cancelled',
                        'name' => 'pemilik.bookings.cancelled',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'export-excel',
                        'method' => 'get',
                        'url' => '/pemilik/bookings/export/excel',
                        'controller' => 'Pemilik\ExportController@exportBookingExcel',
                        'name' => 'pemilik.bookings.export.excel',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'export-pdf',
                        'method' => 'get',
                        'url' => '/pemilik/bookings/export/pdf',
                        'controller' => 'Pemilik\ExportController@exportBookingPdf',
                        'name' => 'pemilik.bookings.export.pdf',
                        'middleware' => 'pemilik_kos',
                    ],
                ],
            ],

            // Pemilik Kos - Manajemen Pembayaran
            [
                'title' => 'Pemilik Manajemen Pembayaran',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/pemilik/pembayaran',
                        'controller' => 'Pemilik\PemilikPembayaranController@index',
                        'name' => 'pemilik.pembayaran.index',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'show',
                        'method' => 'get',
                        'url' => '/pemilik/pembayaran/{id}',
                        'controller' => 'Pemilik\PemilikPembayaranController@show',
                        'name' => 'pemilik.pembayaran.show',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'verifikasi',
                        'method' => 'get',
                        'url' => '/pemilik/pembayaran/status/verifikasi',
                        'controller' => 'Pemilik\PemilikPembayaranController@verifikasi',
                        'name' => 'pemilik.pembayaran.verifikasi',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'sukses',
                        'method' => 'get',
                        'url' => '/pemilik/pembayaran/status/sukses',
                        'controller' => 'Pemilik\PemilikPembayaranController@sukses',
                        'name' => 'pemilik.pembayaran.sukses',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'kadaluarsa',
                        'method' => 'get',
                        'url' => '/pemilik/pembayaran/status/kadaluarsa',
                        'controller' => 'Pemilik\PemilikPembayaranController@kadaluarsa',
                        'name' => 'pemilik.pembayaran.kadaluarsa',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'export-excel',
                        'method' => 'get',
                        'url' => '/pemilik/pembayaran/export/excel',
                        'controller' => 'Pemilik\ExportController@exportPembayaranExcel',
                        'name' => 'pemilik.pembayaran.export.excel',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'export-pdf',
                        'method' => 'get',
                        'url' => '/pemilik/pembayaran/export/pdf',
                        'controller' => 'Pemilik\ExportController@exportPembayaranPdf',
                        'name' => 'pemilik.pembayaran.export.pdf',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'approve',
                        'method' => 'post',
                        'url' => '/pemilik/pembayaran/{id}/approve',
                        'controller' => 'Pemilik\PemilikPembayaranController@approve',
                        'name' => 'pemilik.pembayaran.approve',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'reject',
                        'method' => 'post',
                        'url' => '/pemilik/pembayaran/{id}/reject',
                        'controller' => 'Pemilik\PemilikPembayaranController@reject',
                        'name' => 'pemilik.pembayaran.reject',
                        'middleware' => 'pemilik_kos',
                    ],
                ],
            ],

            // Pemilik Kos - Laporan
            [
                'title' => 'Pemilik Laporan',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/pemilik/laporan',
                        'controller' => 'Pemilik\PemilikLaporanController@index',
                        'name' => 'pemilik.laporan.index',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'okupansi',
                        'method' => 'get',
                        'url' => '/pemilik/laporan/okupansi',
                        'controller' => 'Pemilik\PemilikLaporanController@okupansi',
                        'name' => 'pemilik.laporan.okupansi',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'pendapatan',
                        'method' => 'get',
                        'url' => '/pemilik/laporan/pendapatan',
                        'controller' => 'Pemilik\PemilikLaporanController@pendapatan',
                        'name' => 'pemilik.laporan.pendapatan',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'transaksi',
                        'method' => 'get',
                        'url' => '/pemilik/laporan/transaksi',
                        'controller' => 'Pemilik\PemilikLaporanController@transaksi',
                        'name' => 'pemilik.laporan.transaksi',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'export-excel',
                        'method' => 'get',
                        'url' => '/pemilik/laporan/export/excel',
                        'controller' => 'Pemilik\ExportController@exportLaporanExcel',
                        'name' => 'pemilik.laporan.export.excel',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'export-pdf',
                        'method' => 'get',
                        'url' => '/pemilik/laporan/export/pdf',
                        'controller' => 'Pemilik\ExportController@exportLaporanPdf',
                        'name' => 'pemilik.laporan.export.pdf',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'export-excel-okupansi',
                        'method' => 'get',
                        'url' => '/pemilik/laporan/export/okupansi/excel',
                        'controller' => 'Pemilik\ExportController@exportOkupansiExcel',
                        'name' => 'pemilik.laporan.export.okupansi_excel',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'export-pdf-okupansi',
                        'method' => 'get',
                        'url' => '/pemilik/laporan/export/okupansi/pdf',
                        'controller' => 'Pemilik\ExportController@exportOkupansiPdf',
                        'name' => 'pemilik.laporan.export.okupansi_pdf',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'export-excel-pendapatan',
                        'method' => 'get',
                        'url' => '/pemilik/laporan/export/pendapatan/excel',
                        'controller' => 'Pemilik\ExportController@exportPendapatanExcel',
                        'name' => 'pemilik.laporan.export.pendapatan_excel',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'export-pdf-pendapatan',
                        'method' => 'get',
                        'url' => '/pemilik/laporan/export/pendapatan/pdf',
                        'controller' => 'Pemilik\ExportController@exportPendapatanPdf',
                        'name' => 'pemilik.laporan.export.pendapatan_pdf',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'export-excel-transaksi',
                        'method' => 'get',
                        'url' => '/pemilik/laporan/export/transaksi/excel',
                        'controller' => 'Pemilik\ExportController@exportTransaksiExcel',
                        'name' => 'pemilik.laporan.export.transaksi_excel',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'export-pdf-transaksi',
                        'method' => 'get',
                        'url' => '/pemilik/laporan/export/transaksi/pdf',
                        'controller' => 'Pemilik\ExportController@exportTransaksiPdf',
                        'name' => 'pemilik.laporan.export.transaksi_pdf',
                        'middleware' => 'pemilik_kos',
                    ],
                ],
            ],

            // Pemilik Kos - Ulasan
            [
                'title' => 'Pemilik Ulasan',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/pemilik/ulasan',
                        'controller' => 'Pemilik\PemilikUlasanController@index',
                        'name' => 'pemilik.ulasan.index',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'show',
                        'method' => 'get',
                        'url' => '/pemilik/ulasan/{id}',
                        'controller' => 'Pemilik\PemilikUlasanController@show',
                        'name' => 'pemilik.ulasan.show',
                        'middleware' => 'pemilik_kos',
                    ],
                ],
            ],

            // Pemilik Kos - Pengaturan
            [
                'title' => 'Pemilik Pengaturan',
                'item' => [
                    [
                        'type' => 'profil',
                        'method' => 'get',
                        'url' => '/pemilik/pengaturan/profil',
                        'controller' => 'Pemilik\PemilikPengaturanController@profil',
                        'name' => 'pemilik.pengaturan.profil',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'update-profil',
                        'method' => 'put',
                        'url' => '/pemilik/pengaturan/profil',
                        'controller' => 'Pemilik\PemilikPengaturanController@updateProfil',
                        'name' => 'pemilik.pengaturan.update-profil',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'keamanan',
                        'method' => 'get',
                        'url' => '/pemilik/pengaturan/keamanan',
                        'controller' => 'Pemilik\PemilikPengaturanController@keamanan',
                        'name' => 'pemilik.pengaturan.keamanan',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'update-password',
                        'method' => 'put',
                        'url' => '/pemilik/pengaturan/password',
                        'controller' => 'Pemilik\PemilikPengaturanController@updatePassword',
                        'name' => 'pemilik.pengaturan.update-password',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'update-foto-profil',
                        'method' => 'post',
                        'url' => '/pemilik/pengaturan/foto-profil',
                        'controller' => 'Pemilik\PemilikPengaturanController@updateFotoProfil',
                        'name' => 'pemilik.pengaturan.update-foto-profil',
                        'middleware' => 'pemilik_kos',
                    ],
                    [
                        'type' => 'remove-foto-profil',
                        'method' => 'delete',
                        'url' => '/pemilik/pengaturan/foto-profil',
                        'controller' => 'Pemilik\PemilikPengaturanController@removeFotoProfil',
                        'name' => 'pemilik.pengaturan.remove-foto-profil',
                        'middleware' => 'pemilik_kos',
                    ],
                ],
            ],
            //END PEMILIK

            //START USER
            // User Dashboard
            [
                'title' => 'User Dashboard',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/dashboard',
                        'controller' => 'UserDashboardController@index',
                        'name' => 'users.dashboard',
                        'middleware' => '',
                        'public' => true,
                    ],
                    // Tambahkan route baru ini
                    [
                        'type' => 'save-location',
                        'method' => 'post',
                        'url' => '/dashboard/save-location',
                        'controller' => 'UserDashboardController@saveUserLocation',
                        'name' => 'users.dashboard.save-location',
                        'middleware' => 'auth:web',
                        'public' => false,
                    ]
                ]
            ],

            // User Kosan Routes
            [
                'title' => 'User Kosan',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/users/kosan',
                        'controller' => 'KosanController@index',
                        'name' => 'users.kosan.index',
                        'middleware' => '',
                        'public' => true,
                    ],
                    [
                        'type' => 'show',
                        'method' => 'get',
                        'url' => '/users/kosan/{id}',
                        'controller' => 'KosanController@show',
                        'name' => 'users.kosan.show',
                        'middleware' => '',
                        'public' => true,
                    ],
                    [
                        'type' => 'search',
                        'method' => 'get',
                        'url' => '/users/kosan/search',
                        'controller' => 'KosanController@search',
                        'name' => 'users.kosan.search',
                        'middleware' => '',
                        'public' => true,
                    ],
                    [
                        'type' => 'nearby',
                        'method' => 'get',
                        'url' => '/users/kosan/nearby',
                        'controller' => 'KosanController@nearby',
                        'name' => 'users.kosan.nearby',
                        'middleware' => '',
                        'public' => true,
                    ],
                    [
                        'type' => 'api-nearby',
                        'method' => 'get',
                        'url' => '/api/nearby-kosans',
                        'controller' => 'KosanController@getNearbyKosan',
                        'name' => 'api.nearby-kosans',
                        'middleware' => '',
                        'public' => true,
                    ],
                    [
                        'type' => 'api-availability',
                        'method' => 'get',
                        'url' => '/api/kosan/{id}/availability',
                        'controller' => 'KosanController@availability',
                        'name' => 'api.kosan.availability',
                        'middleware' => '',
                        'public' => true,
                    ],
                    [
                        'type' => 'toggle-favorite',
                        'method' => 'post',
                        'url' => '/users/kosan/{id}/favorite',
                        'controller' => 'KosanController@toggleFavorite',
                        'name' => 'users.kosan.toggle-favorite',
                        'middleware' => 'auth:web',
                        'public' => false,
                    ],
                    [
                        'type' => 'booking-form',
                        'method' => 'get',
                        'url' => '/users/kosan/{id}/booking',
                        'controller' => 'KosanController@bookingForm',
                        'name' => 'users.kosan.booking-form',
                        'middleware' => 'auth:web',
                        'public' => false,
                    ],
                    [
                        'type' => 'process-booking',
                        'method' => 'post',
                        'url' => '/users/kosan/{id}/booking',
                        'controller' => 'KosanController@processBooking',
                        'name' => 'users.kosan.process-booking',
                        'middleware' => 'auth:web',
                        'public' => false,
                    ],
                    [
                        'type' => 'rate',
                        'method' => 'post',
                        'url' => '/users/kosan/{id}/rate',
                        'controller' => 'KosanController@rateKosan',
                        'name' => 'users.kosan.rate',
                        'middleware' => 'auth:web',
                        'public' => false,
                    ],
                    [
                        'type' => 'review-form',
                        'method' => 'get',
                        'url' => '/users/kosan/{id}/review',
                        'controller' => 'KosanController@reviewForm',
                        'name' => 'users.kosan.review-form',
                        'middleware' => 'auth:web',
                        'public' => false,
                    ],
                    [
                        'type' => 'store-review',
                        'method' => 'post',
                        'url' => '/users/kosan/{id}/review',
                        'controller' => 'KosanController@storeReview',
                        'name' => 'users.kosan.store-review',
                        'middleware' => 'auth:web',
                        'public' => false,
                    ]
                ]
            ],

            // Update pada array User Bookings di ListRoutes.php
            [
                'title' => 'User Bookings',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/users/bookings',
                        'controller' => 'BookingController@index',
                        'name' => 'users.bookings.index',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'show',
                        'method' => 'get',
                        'url' => '/users/bookings/{id}',
                        'controller' => 'BookingController@show',
                        'name' => 'users.bookings.show',
                        'middleware' => 'auth:web',
                    ],
                    // Tambahkan routes baru berikut
                    [
                        'type' => 'cancel',
                        'method' => 'put',
                        'url' => '/users/bookings/{id}/cancel',
                        'controller' => 'BookingController@cancel',
                        'name' => 'users.bookings.cancel',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'process-payment',
                        'method' => 'post',
                        'url' => '/users/bookings/{id}/process-payment',
                        'controller' => 'BookingController@processPayment',
                        'name' => 'users.bookings.process-payment',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'complete',
                        'method' => 'put',
                        'url' => '/users/bookings/{id}/complete',
                        'controller' => 'BookingController@complete',
                        'name' => 'users.bookings.complete',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'extend-form',
                        'method' => 'get',
                        'url' => '/users/bookings/{id}/extend',
                        'controller' => 'BookingController@extendForm',
                        'name' => 'users.bookings.extend-form',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'extend',
                        'method' => 'put',
                        'url' => '/users/bookings/{id}/extend',
                        'controller' => 'BookingController@extend',
                        'name' => 'users.bookings.extend',
                        'middleware' => 'auth:web',
                    ]
                ]
            ],

            // User Pembayaran
            [
                'title' => 'User Pembayaran',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/users/bookings/{booking}/pembayaran',
                        'controller' => 'PembayaranController@index',
                        'name' => 'users.pembayaran.index',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'process',
                        'method' => 'post',
                        'url' => '/users/bookings/{booking}/pembayaran/process',
                        'controller' => 'PembayaranController@process',
                        'name' => 'users.pembayaran.process',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'konfirmasi',
                        'method' => 'get',
                        'url' => '/users/bookings/{booking}/pembayaran/konfirmasi',
                        'controller' => 'PembayaranController@konfirmasi',
                        'name' => 'users.pembayaran.konfirmasi',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'check-status',
                        'method' => 'get',
                        'url' => '/users/bookings/{booking}/pembayaran/check-status',
                        'controller' => 'PembayaranController@checkStatus',
                        'name' => 'users.pembayaran.check-status',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'update-status',
                        'method' => 'post',
                        'url' => '/users/bookings/{booking}/pembayaran/update-status',
                        'controller' => 'PembayaranController@updateStatus',
                        'name' => 'users.pembayaran.update-status',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'cancel',
                        'method' => 'post',
                        'url' => '/users/bookings/{booking}/pembayaran/cancel',
                        'controller' => 'PembayaranController@cancel',
                        'name' => 'users.pembayaran.cancel',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'manual-payment',
                        'method' => 'get',
                        'url' => '/users/bookings/{booking}/pembayaran/manual',
                        'controller' => 'PembayaranController@manualPayment',
                        'name' => 'users.pembayaran.manual',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'upload-bukti',
                        'method' => 'post',
                        'url' => '/users/bookings/{booking}/pembayaran/upload-bukti',
                        'controller' => 'PembayaranController@uploadBukti',
                        'name' => 'users.pembayaran.upload-bukti',
                        'middleware' => 'auth:web',
                    ],
                ]
            ],

            // Payment Gateway Callbacks (Public Routes)
            [
                'title' => 'Payment Gateway Callbacks',
                'item' => [
                    [
                        'type' => 'midtrans-callback',
                        'method' => 'post',
                        'url' => '/payment/midtrans/callback',
                        'controller' => 'PembayaranController@callback',
                        'name' => 'pembayaran.midtrans.callback',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'payment-redirect',
                        'method' => 'get',
                        'url' => '/pembayaran/{transaction_id}',
                        'controller' => 'PembayaranController@redirectByCode',
                        'name' => 'pembayaran.redirect',
                        'middleware' => 'auth:web',
                    ],
                ]
            ],

            // User Favorites
            [
                'title' => 'User Favorites',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/users/favorites',
                        'controller' => 'KosanController@favorites',
                        'name' => 'users.favorites',
                        'middleware' => 'auth:web',
                    ]
                ]
            ],

            // User Reviews
            [
                'title' => 'User Reviews',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/users/reviews',
                        'controller' => 'ReviewController@index',
                        'name' => 'users.reviews.index',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'show',
                        'method' => 'get',
                        'url' => '/users/reviews/{id}',
                        'controller' => 'ReviewController@show',
                        'name' => 'users.reviews.show',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'update',
                        'method' => 'get,put',
                        'url' => '/users/reviews/{id}/update',
                        'controller' => 'ReviewController@update',
                        'name' => 'users.reviews.update',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/users/reviews/{id}',
                        'controller' => 'ReviewController@destroy',
                        'name' => 'users.reviews.delete',
                        'middleware' => 'auth:web',
                    ]
                ]
            ],

            // User Profile
            [
                'title' => 'User Profil',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/users/profile',
                        'controller' => 'UserProfileController@index',
                        'name' => 'users.profile.index',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'update',
                        'method' => 'put',
                        'url' => '/users/profile/update',
                        'controller' => 'UserProfileController@update',
                        'name' => 'users.profile.update',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'update-profile-photo',
                        'method' => 'post',
                        'url' => '/users/profile/update-profile-photo',
                        'controller' => 'UserProfileController@updateProfilePhoto',
                        'name' => 'users.profile.update-profile-photo',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'remove-profile-photo',
                        'method' => 'delete',
                        'url' => '/users/profile/remove-profile-photo',
                        'controller' => 'UserProfileController@removeProfilePhoto',
                        'name' => 'users.profile.remove-profile-photo',
                        'middleware' => 'auth:web',
                    ],
                ]
            ],

            // User Pengaturan
            [
                'title' => 'User Pengaturan',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/users/pengaturan',
                        'controller' => 'UserSettingsController@index',
                        'name' => 'users.settings.index',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'update-password',
                        'method' => 'put',
                        'url' => '/users/pengaturan/password',
                        'controller' => 'UserSettingsController@updatePassword',
                        'name' => 'users.settings.update-password',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'update-notifications',
                        'method' => 'put',
                        'url' => '/users/pengaturan/notifikasi',
                        'controller' => 'UserSettingsController@updateNotifications',
                        'name' => 'users.settings.update-notifications',
                        'middleware' => 'auth:web',
                    ],
                    [
                        'type' => 'update-privacy',
                        'method' => 'put',
                        'url' => '/users/pengaturan/privasi',
                        'controller' => 'UserSettingsController@updatePrivacy',
                        'name' => 'users.settings.update-privacy',
                        'middleware' => 'auth:web',
                    ],
                ]
            ],

            // Tentang Kami
            [
                'title' => 'Tentang Kami',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/users/tentang-kami',
                        'controller' => 'TentangKamiController@index',
                        'name' => 'users.tentang-kami.index',
                        'middleware' => 'auth:web',
                    ],
                ]
            ],
            //END USER
            // Tambahkan rute lain sesuai kebutuhan
        ];

        if (!empty($index)) {
            return !empty($data[$index]) ? $data[$index] : null;
        }
        return $data;
    }

    function getIgnoreType($type = null)
    {
        $data = ['/', 'form', 'ajax', 'system'];
        if (!empty($type)) {
            return !in_array($type, $data) ? 1 : 0;
        }
        return $data;
    }
}
