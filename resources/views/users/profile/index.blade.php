@extends('layouts.user.app')

@section('title', 'Profil Saya')

@section('styles')
    <style>
        .profile-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 0;
            height: 100%;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1rem 1.5rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 16px;
            border: 1px solid #FFFFFFFF;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #4f6f52;
            box-shadow: 0 0 0 0.2rem rgba(79, 111, 82, 0.25);
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .required-star {
            color: #dc3545;
        }

        .profile-info {
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .profile-header {
            border-radius: 15px;
            overflow: hidden;
        }

        .save-btn {
            border-radius: 10px;
            padding: 10px 30px;
            font-weight: 500;
            min-width: 180px;
        }

        @media (max-width: 767px) {
            .form-label {
                margin-bottom: 0.5rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4 px-3 px-md-4 pb-5">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 profile-header" style="background-color: #4f6f52;">
                    <div class="card-body p-4 p-md-4">
                        <h3 class="fw-bold mb-1 text-white">Profil Saya</h3>
                        <p class="mb-0 text-light opacity-90">Kelola informasi pribadi dan pengaturan akun Anda.</p>
                    </div>
                </div>
            </div>
        </div>

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

        <div class="row g-4">
            <!-- Kolom Kiri - Foto Profil & Info -->
            <div class="col-lg-5">
                <div class="card profile-card" style="background-color: #e6e9ee;">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-circle me-2 text-primary"></i>Tampilan Profil
                        </h5>
                    </div>
                    <div class="card-body p-4 d-flex flex-column align-items-center">
                        <div class="mb-4 text-center">
                            <img src="{{ $user->foto_profil ? asset('storage/' . $user->foto_profil) : asset('images/default-avatar.svg') }}"
                                alt="Foto Profil" id="profilePhotoDisplay" class="img-thumbnail rounded-circle"
                                style="width: 150px; height: 150px; object-fit: cover;">
                        </div>

                        <form action="{{ route('users.profile.update-profile-photo') }}" method="POST"
                            enctype="multipart/form-data" id="profilePhotoForm" class="w-100 mb-4">
                            @csrf
                            <div class="mb-3">
                                <label for="profilePhotoInput" class="form-label">Ganti Foto Profil</label>
                                <input type="file" class="form-control" id="profilePhotoInput" name="profile_photo"
                                    accept="image/jpeg,image/png,image/gif" aria-describedby="fileHelp">
                                <div id="fileHelp" class="form-text mt-2">
                                    Format: JPG, PNG, GIF. Ukuran file maks: 2MB.
                                </div>
                            </div>
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="submit" id="uploadButton" class="btn btn-primary btn-sm">
                                    <i class="fas fa-upload me-1"></i> Upload
                                </button>
                                @if ($user->foto_profil)
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#removePhotoModal">
                                        <i class="fas fa-trash-alt me-1"></i> Hapus
                                    </button>
                                @endif
                            </div>
                        </form>

                        <div class="profile-info w-100 mt-3 pt-4">
                            <h6 class="fw-semibold mb-3 text-muted">
                                <i class="fas fa-info-circle me-2"></i>Informasi Akun
                            </h6>
                            <div class="row mb-2">
                                <div class="col-5 col-sm-4 text-muted">Nama</div>
                                <div class="col-7 col-sm-8 fw-medium">{{ $user->name }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 col-sm-4 text-muted">No. Telepon</div>
                                <div class="col-7 col-sm-8 fw-medium">{{ $user->no_hp ?? '-' }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 col-sm-4 text-muted">Email</div>
                                <div class="col-7 col-sm-8 fw-medium">{{ $user->email ?? '-' }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 col-sm-4 text-muted">Alamat</div>
                                <div class="col-7 col-sm-8 fw-medium">{{ $user->alamat ?? '-' }}</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-sm-4 text-muted">Bergabung</div>
                                <div class="col-7 col-sm-8 fw-medium">
                                    {{ $user->created_at->locale('id')->format('d F Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan - Form Informasi Pribadi -->
            <div class="col-lg-7">
                <div class="card profile-card" style="background-color: #e6e9ee;">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-edit me-2 text-primary"></i>Ubah Informasi Pribadi
                        </h5>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('users.profile.update') }}" method="POST" id="profileForm">
                            @csrf
                            @method('PUT')

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label for="name" class="form-label">
                                        Nama Lengkap <span class="required-star">*</span>
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $user->name) }}" required
                                        placeholder="Masukkan nama lengkap Anda">
                                    @error('name')
                                        <div class="invalid-feedback d-block mt-1">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label for="no_hp" class="form-label">Nomor Telepon</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="tel" class="form-control @error('no_hp') is-invalid @enderror"
                                        id="no_hp" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                                        placeholder="Contoh: 081234567890">
                                    @error('no_hp')
                                        <div class="invalid-feedback d-block mt-1">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-text mt-1">
                                        <small>Digunakan untuk verifikasi dan notifikasi penting.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label for="alamat" class="form-label">
                                        Alamat Lengkap </label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control @error('alamat') is-invalid @enderror"
                                        id="alamat" name="alamat" value="{{ old('alamat', $user->alamat) }}"
                                        placeholder="Masukkan alamat lengkap Anda">
                                    @error('alamat')
                                        <div class="invalid-feedback d-block mt-1">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label for="email" class="form-label">Email @if (!$user->email)
                                            <span class="required-star">*</span>
                                        @endif
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $user->email) }}"
                                        @if ($user->email) disabled readonly @else required @endif
                                        placeholder="Masukkan email Anda">

                                    @if ($user->email)
                                        <div class="form-text mt-1">
                                            <small>Email tidak dapat diubah.</small>
                                        </div>
                                    @else
                                        <div class="form-text mt-1">
                                            <small>Email ini akan digunakan untuk login dan tidak dapat diubah setelah
                                                diatur.</small>
                                        </div>
                                    @endif

                                    @error('email')
                                        <div class="invalid-feedback d-block mt-1">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>


                            <div class="d-grid d-md-flex justify-content-md-end mt-4">
                                <button type="submit" class="btn btn-primary save-btn py-3 py-md-2">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hapus Foto -->
    @if ($user->foto_profil)
        <div class="modal fade" id="removePhotoModal" tabindex="-1" aria-labelledby="removePhotoModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="removePhotoModalLabel">
                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>Konfirmasi Hapus Foto
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus foto profil Anda? Tindakan ini tidak dapat
                            dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form action="{{ route('users.profile.remove-profile-photo') }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt me-2"></i>Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profilePhotoInput = document.getElementById('profilePhotoInput');
            const profilePhotoDisplay = document.getElementById('profilePhotoDisplay');
            const uploadButton = document.getElementById('uploadButton');
            const uploadForm = document.getElementById('profilePhotoForm');
            const profileForm = document.getElementById('profileForm');

            if (profilePhotoInput && profilePhotoDisplay) {
                profilePhotoInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const fileSize = this.files[0].size / 1024 / 1024; // MB
                        if (fileSize > 2) {
                            alert('Ukuran file terlalu besar. Maksimal 2MB.');
                            this.value = '';
                            return;
                        }

                        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                        if (!validTypes.includes(this.files[0].type)) {
                            alert('Format file tidak didukung. Gunakan JPG, PNG, atau GIF.');
                            this.value = '';
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            profilePhotoDisplay.src = e.target.result;
                        };
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }

            if (uploadForm) {
                uploadForm.addEventListener('submit', function(e) {
                    if (!profilePhotoInput.files || profilePhotoInput.files.length === 0) {
                        e.preventDefault();
                        alert('Silakan pilih file foto terlebih dahulu.');
                        profilePhotoInput.focus();
                        return;
                    }
                    uploadButton.disabled = true;
                    uploadButton.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Mengupload...';
                });
            }

            if (profileForm) {
                profileForm.addEventListener('submit', function(e) {
                    const nameInput = document.getElementById('name');
                    if (!nameInput.value.trim()) {
                        e.preventDefault();
                        alert('Nama lengkap harus diisi.');
                        nameInput.focus();
                        return;
                    }

                    const phoneInput = document.getElementById('no_hp');
                    if (phoneInput.value.trim()) {
                        const phoneRegex = /^08[0-9]{8,11}$/; // Contoh: 081234567890
                        if (!phoneRegex.test(phoneInput.value.trim())) {
                            e.preventDefault();
                            alert('Format nomor telepon tidak valid. Contoh: 081234567890');
                            phoneInput.focus();
                            return;
                        }
                    }

                    const alamatInput = document.getElementById('alamat');
                    if (!alamatInput.value.trim()) {
                        e.preventDefault();
                        alert('Alamat lengkap harus diisi.');
                        alamatInput.focus();
                        return;
                    }
                });
            }

            const phoneInput = document.getElementById('no_hp');
            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }
        });
    </script>
@endsection
