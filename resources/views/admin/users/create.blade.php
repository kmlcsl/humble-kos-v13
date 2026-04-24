@extends('layouts.admin.app')

@section('title', 'Tambah Pengguna Baru')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Manajemen Pengguna</a></li>
<li class="breadcrumb-item active" aria-current="page">Tambah Pengguna</li>
@endsection

@section('page-title', 'Tambah Pengguna Baru')

@section('page-actions')
<a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left me-2"></i>Kembali
</a>
@endsection

@section('content')

{{-- Display All Validation Errors --}}
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <h5><i class="fas fa-exclamation-triangle me-2"></i>Ada kesalahan dalam form:</h5>
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<form action="{{ route('admin.users.store') }}" method="POST">
    @csrf
    <div class="admin-pemilik-card">
        <div class="card-header">
            <h5 class="card-title">Detail Pengguna</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_lengkap" class="form-label required">Nama Lengkap</label>
                    <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required>
                    @error('nama_lengkap')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label required">Username</label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label required">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label required">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label required">Konfirmasi Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label required">Role</label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="pemilik_kos" {{ old('role') == 'pemilik_kos' ? 'selected' : '' }}>Pemilik Kos</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="no_telepon" class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control @error('no_telepon') is-invalid @enderror" id="no_telepon" name="no_telepon" value="{{ old('no_telepon') }}">
                    @error('no_telepon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Simpan Pengguna
            </button>
        </div>
    </div>
</form>
@endsection

@push('styles')
<style>
    .required::after {
        content: ' *';
        color: red;
    }
</style>
@endpush
