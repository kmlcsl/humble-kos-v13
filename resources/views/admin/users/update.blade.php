@extends('layouts.admin.app')

@section('title', 'Edit Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Manajemen Pengguna</a></li>
    <li class="breadcrumb-item active">Edit Pengguna</li>
@endsection

@section('page-title', 'Edit Pengguna')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Form Edit Data Pengguna -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Form Edit Pengguna</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $users->user_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror"
                                   id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $users->nama_lengkap) }}" required>
                            @error('nama_lengkap')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                   id="username" name="username" value="{{ old('username', $users->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $users->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="no_telepon" class="form-label">No. Telepon</label>
                            <input type="text" class="form-control @error('no_telepon') is-invalid @enderror"
                                   id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $users->no_telepon) }}">
                            @error('no_telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror"
                                      id="alamat" name="alamat" rows="3">{{ old('alamat', $users->alamat) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Form Ganti Password -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-key me-2"></i>Ganti Password</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Error:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Perhatian:</strong> Isi form ini hanya jika ingin mengubah password pengguna.
                    </div>

                    <form action="{{ route('admin.users.update-password', $users->user_id) }}" method="POST" id="passwordForm">
                        @csrf

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                   id="new_password" name="new_password" placeholder="Minimal 8 karakter" required>
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimal 8 karakter</small>
                        </div>

                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                   id="new_password_confirmation" name="new_password_confirmation" placeholder="Ulangi password baru" required>
                            @error('new_password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-key me-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- User Info Card -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi User</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th width="120">User ID</th>
                            <td>: {{ $users->user_id }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>:
                                @if($users->role == 'pemilik_kos')
                                    <span class="badge bg-info">Pemilik Kos</span>
                                @elseif($users->role == 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @else
                                    <span class="badge bg-secondary">User</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status Akun</th>
                            <td>:
                                @if($users->status_akun == 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($users->status_akun) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Terdaftar</th>
                            <td>: {{ $users->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Debug script untuk memastikan form submit
    document.addEventListener('DOMContentLoaded', function() {
        const passwordForm = document.getElementById('passwordForm');

        if (passwordForm) {
            passwordForm.addEventListener('submit', function(e) {
                console.log('Form password disubmit');

                const password = document.getElementById('new_password').value;
                const confirmation = document.getElementById('new_password_confirmation').value;

                console.log('Password length:', password.length);
                console.log('Passwords match:', password === confirmation);

                if (password.length < 8) {
                    e.preventDefault();
                    alert('Password minimal 8 karakter!');
                    return false;
                }

                if (password !== confirmation) {
                    e.preventDefault();
                    alert('Password dan konfirmasi tidak cocok!');
                    return false;
                }
            });
        }
    });
</script>
@endpush
