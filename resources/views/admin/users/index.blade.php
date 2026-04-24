@extends('layouts.admin.app')

@section('title', 'Manajemen Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item active">Manajemen Pengguna</li>
@endsection

@section('page-title', 'Manajemen Pengguna')

@section('page-actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah Pengguna
    </a>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Alert Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Daftar Pengguna</h5>
                        <span class="text-white">Total: {{ $users->total() }} pengguna</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>No. HP</th>
                                    <th>Alamat</th>
                                    <th>Role</th>
                                    <th>Terdaftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $index => $user)
                                    <tr>
                                        <td>{{ $users->firstItem() + $index }}</td>
                                        <td><strong>{{ $user->nama_lengkap }}</strong></td>
                                        <td>{{ $user->email ?? '-' }}</td>
                                        <td>{{ $user->no_telepon ?? '-' }}</td>
                                        <td>{{ Str::limit($user->alamat, 30) ?? '-' }}</td>
                                        <td>
                                            @if ($user->role == 'pemilik_kos')
                                                <span class="badge bg-info">Pemilik Kos</span>
                                            @elseif($user->role == 'admin')
                                                <span class="badge bg-danger">Admin</span>
                                            @else
                                                <span class="badge bg-secondary">Pengguna</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                        <td class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('admin.users.show', $user->user_id) }}"
                                                class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.update', $user->user_id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Tidak ada data pengguna.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination --}}
                    @if ($users->hasPages())
                        <div class="p-3">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
