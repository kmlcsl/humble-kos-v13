@extends('layouts.admin.app')

@section('title', 'Pengguna Admin')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Pengguna Admin</li>
@endsection

@section('page-title', 'Pengguna Admin')

@section('page-actions')
    <a href="{{ route('admin.pengaturan.admins.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah Pengguna
    </a>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-user-shield me-2"></i>Daftar Pengguna Admin</h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Terdaftar</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admins as $admin)
                                @if($admin->role !== 'admin')
                                @continue
                                @endif
                                <tr>
                                    <td><strong>{{ $admin->name }}</strong></td>
                                    <td>{{ $admin->email }}</td>
                                    <td>{{ $admin->created_at->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.pengaturan.admins.update', $admin->user_id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $admin->user_id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        <!-- Delete Confirmation Modal -->
                                        <div class="modal fade" id="deleteModal{{ $admin->user_id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <p>Apakah Anda yakin ingin menghapus admin <strong>{{ $admin->name }}</strong>?</p>
                                                        @if($admin->user_id === Auth::id())
                                                        <div class="alert alert-danger">Anda tidak dapat menghapus akun Anda sendiri.</div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <form action="{{ route('admin.pengaturan.admins.destroy', $admin->user_id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" @if($admin->user_id === Auth::id()) disabled @endif>Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada admin yang terdaftar.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $admins->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
