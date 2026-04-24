@extends('layouts.admin.app')

@section('title', 'Detail Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Manajemen Pengguna</a></li>
    <li class="breadcrumb-item active">Detail Pengguna</li>
@endsection

@section('page-title', 'Detail Pengguna')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Informasi Pengguna -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Pengguna</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">User ID</th>
                            <td>{{ $users->user_id }}</td>
                        </tr>
                        <tr>
                            <th>Nama Lengkap</th>
                            <td>{{ $users->nama_lengkap }}</td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td>{{ $users->username }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $users->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>No. Telepon</th>
                            <td>{{ $users->no_telepon ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $users->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>
                                @if($users->role == 'pemilik_kos')
                                    <span class="badge bg-info">Pemilik Kos</span>
                                @elseif($users->role == 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @else
                                    <span class="badge bg-secondary">Pengguna</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status Akun</th>
                            <td>
                                @if($users->status_akun == 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @elseif($users->status_akun == 'nonaktif')
                                    <span class="badge bg-warning">Non-aktif</span>
                                @elseif($users->status_akun == 'suspend')
                                    <span class="badge bg-danger">Suspend</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($users->status_akun) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Terdaftar</th>
                            <td>{{ $users->created_at->format('d F Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diupdate</th>
                            <td>{{ $users->updated_at->format('d F Y H:i') }}</td>
                        </tr>
                    </table>

                    <div class="mt-3">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <a href="{{ route('admin.users.update', $users->user_id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aksi Keamanan -->
        <div class="col-md-6">
            <!-- Success/Error Messages -->
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

            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Keamanan Akun</h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Informasi Keamanan</h6>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="150"><i class="fas fa-key text-muted me-2"></i>Password</td>
                            <td>: <span class="text-muted">••••••••••••</span></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-clock text-muted me-2"></i>Terakhir Update</td>
                            <td>: {{ $users->updated_at->format('d F Y H:i') }}</td>
                        </tr>
                    </table>

                    <hr>

                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <small><strong>Catatan:</strong> Untuk keamanan, password tidak dapat ditampilkan. Gunakan tombol di bawah untuk mereset password pengguna.</small>
                    </div>

                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                        <i class="fas fa-key me-2"></i>Reset Password Pengguna
                    </button>
                </div>
            </div>

            <!-- Quick Stats (Optional) -->
            @if($users->role == 'pemilik_kos')
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik Pemilik Kos</h6>
                </div>
                <div class="card-body">
                    @php
                        $totalKos = \App\Models\Kosan::where('owner_id', $users->user_id)->count();
                        $totalKamar = \App\Models\Kamar::whereIn('kosan_id',
                            \App\Models\Kosan::where('owner_id', $user->user_id)->pluck('kosan_id')
                        )->count();
                    @endphp
                    <p class="mb-2"><i class="fas fa-building text-primary me-2"></i> Total Kos: <strong>{{ $totalKos }}</strong></p>
                    <p class="mb-0"><i class="fas fa-door-open text-success me-2"></i> Total Kamar: <strong>{{ $totalKamar }}</strong></p>
                </div>
            </div>
            @endif

            @if($users->role == 'user')
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Aktivitas Pengguna</h6>
                </div>
                <div class="card-body">
                    @php
                        $totalBooking = \App\Models\BookingKosan::where('user_id', $users->user_id)->count();
                        $totalUlasan = \App\Models\UlasanKosan::where('user_id', $users->user_id)->count();
                    @endphp
                    <p class="mb-2"><i class="fas fa-calendar-check text-primary me-2"></i> Total Booking: <strong>{{ $totalBooking }}</strong></p>
                    <p class="mb-0"><i class="fas fa-star text-warning me-2"></i> Total Ulasan: <strong>{{ $totalUlasan }}</strong></p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="resetPasswordModalLabel">
                    <i class="fas fa-key me-2"></i>Reset Password Pengguna
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.update-password', $users->user_id) }}" method="POST" id="resetPasswordForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Peringatan!</strong><br>
                        Anda akan mereset password untuk: <strong>{{ $users->nama_lengkap }}</strong><br>
                        <small class="text-muted">Pastikan password baru dikomunikasikan kepada pengguna ini.</small>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Error:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="modal_new_password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                               id="modal_new_password" name="new_password" placeholder="Minimal 8 karakter" required autocomplete="new-password">
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimal 8 karakter</small>
                    </div>

                    <div class="mb-3">
                        <label for="modal_new_password_confirmation" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror"
                               id="modal_new_password_confirmation" name="new_password_confirmation" placeholder="Ulangi password baru" required autocomplete="new-password">
                        @error('new_password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="showPassword">
                        <label class="form-check-label" for="showPassword">
                            Tampilkan password
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-key me-2"></i>Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Show/hide password toggle
    document.getElementById('showPassword')?.addEventListener('change', function() {
        const passwordField = document.getElementById('modal_new_password');
        const confirmField = document.getElementById('modal_new_password_confirmation');
        const type = this.checked ? 'text' : 'password';
        passwordField.type = type;
        confirmField.type = type;
    });

    // Auto open modal if there are validation errors
    @if($errors->any())
        var resetPasswordModal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
        resetPasswordModal.show();
    @endif

    // Form validation
    document.getElementById('resetPasswordForm')?.addEventListener('submit', function(e) {
        const password = document.getElementById('modal_new_password').value;
        const confirmation = document.getElementById('modal_new_password_confirmation').value;

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

        return confirm('Apakah Anda yakin ingin mereset password pengguna ini?');
    });
</script>
@endpush
