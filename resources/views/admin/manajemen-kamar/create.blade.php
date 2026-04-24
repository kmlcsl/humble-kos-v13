@extends('layouts.admin.app')

@section('title', 'Tambah Kamar Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.manajemen-kamar.index') }}">Manajemen Kamar</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Kamar Baru</li>
@endsection

@section('page-title', 'Tambah Kamar Baru')

@section('page-actions')
    <div class="btn-group" role="group">
        <a href="{{ route('admin.manajemen-kamar.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
@endsection

@section('content')
    <form action="{{ route('admin.manajemen-kamar.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <!-- Kolom Kiri -->
            <div class="col-lg-8">
                <!-- Informasi Umum -->
                <div class="admin-pemilik-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Informasi Kamar</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="kosan_id" class="form-label">Kosan <span class="text-danger">*</span></label>
                            <select class="form-select @error('kosan_id') is-invalid @enderror" id="kosan_id"
                                name="kosan_id" required>
                                <option value="">-- Pilih Kosan --</option>
                                @foreach ($kosans as $kosan)
                                    <option value="{{ $kosan->kosan_id }}"
                                        {{ old('kosan_id', $selectedKosan ? $selectedKosan->kosan_id : null) == $kosan->kosan_id ? 'selected' : '' }}>
                                        {{ $kosan->nama_kosan }} - {{ $kosan->alamat }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kosan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nomor_kamar" class="form-label">Nomor Kamar <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nomor_kamar') is-invalid @enderror"
                                id="nomor_kamar" name="nomor_kamar" value="{{ old('nomor_kamar') }}"
                                placeholder="Contoh: A-101, Lantai 1 - 01, dll" required>
                            @error('nomor_kamar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipe_kamar" class="form-label">Tipe Kamar <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('tipe_kamar') is-invalid @enderror" id="tipe_kamar"
                                    name="tipe_kamar" required>
                                    <option value="single" {{ old('tipe_kamar') == 'single' ? 'selected' : '' }}>Single
                                    </option>
                                    <option value="double" {{ old('tipe_kamar') == 'double' ? 'selected' : '' }}>Double
                                    </option>
                                    <option value="shared" {{ old('tipe_kamar') == 'shared' ? 'selected' : '' }}>Shared
                                    </option>
                                </select>
                                @error('tipe_kamar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kapasitas" class="form-label">Kapasitas (Orang) <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('kapasitas') is-invalid @enderror"
                                    id="kapasitas" name="kapasitas" value="{{ old('kapasitas', 1) }}" min="1"
                                    required>
                                @error('kapasitas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="ukuran_kamar" class="form-label">Ukuran Kamar <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ukuran_kamar') is-invalid @enderror" id="ukuran_kamar"
                                name="ukuran_kamar" value="{{ old('ukuran_kamar') }}" placeholder="Contoh: 3x4 m atau 12 m²" required>
                            @error('ukuran_kamar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi Kamar</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3"
                                placeholder="Deskripsi detail tentang kamar...">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Harga Kamar -->
                <div class="admin-pemilik-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Informasi Harga</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="harga_per_bulan" class="form-label">Harga per Bulan (Rp) <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('harga_per_bulan') is-invalid @enderror"
                                        id="harga_per_bulan" name="harga_per_bulan" value="{{ old('harga_per_bulan') }}"
                                        min="0" required>
                                </div>
                                @error('harga_per_bulan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fasilitas Kamar -->
                <div class="admin-pemilik-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Fasilitas Kamar</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Info:</strong> Pilih fasilitas yang tersedia di kamar ini.
                        </div>
                        <div class="row">
                            @forelse ($fasilitas as $item)
                                <div class="col-md-4 col-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="fasilitas[]"
                                            value="{{ $item->fasilitas_id }}" id="fasilitas_{{ $item->fasilitas_id }}"
                                            {{ is_array(old('fasilitas')) && in_array($item->fasilitas_id, old('fasilitas')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="fasilitas_{{ $item->fasilitas_id }}">
                                            <i class="{{ $item->icon_fasilitas ?? 'fas fa-check' }} me-1 text-primary"></i>
                                            {{ $item->nama_fasilitas }}
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Tidak ada data fasilitas yang tersedia.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Foto Kamar -->
                <div class="admin-pemilik-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Foto Kamar</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="foto_kamar" class="form-label">Upload Foto</label>
                            <input
                                class="form-control @error('foto_kamar') is-invalid @enderror"
                                type="file" id="foto_kamar" name="foto_kamar"
                                accept="image/jpeg,image/png,image/jpg">
                            <div class="form-text">Upload satu foto utama untuk kamar ini.</div>
                            @error('foto_kamar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="col-lg-4">
                <!-- Status Kamar -->
                <div class="admin-pemilik-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Status Kamar</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="status_kamar" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status_kamar') is-invalid @enderror" id="status_kamar"
                                name="status_kamar" required>
                                <option value="tersedia" {{ old('status_kamar') == 'tersedia' ? 'selected' : '' }}>Tersedia
                                </option>
                                <option value="terisi" {{ old('status_kamar') == 'terisi' ? 'selected' : '' }}>Terisi</option>
                                <option value="maintenance" {{ old('status_kamar') == 'maintenance' ? 'selected' : '' }}>
                                    Maintenance</option>
                            </select>
                            @error('status_kamar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="status-description mb-3">
                            <div class="alert alert-info tersedia">
                                <i class="fas fa-info-circle me-1"></i> Status <strong>Tersedia</strong> menandakan bahwa
                                kamar kosong dan siap untuk dipesan.
                            </div>
                            <div class="alert alert-primary terisi d-none">
                                <i class="fas fa-info-circle me-1"></i> Status <strong>Terisi</strong> menandakan bahwa
                                kamar sedang dihuni pengguna.
                            </div>
                            <div class="alert alert-warning maintenance d-none">
                                <i class="fas fa-info-circle me-1"></i> Status <strong>Maintenance</strong> menandakan
                                bahwa kamar sedang dalam perbaikan.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Simpan -->
                <div class="admin-pemilik-card mb-4">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-save me-1"></i>Simpan Kamar
                        </button>
                        <a href="{{ route('admin.manajemen-kamar.index') }}"
                            class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-times me-1"></i>Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('styles')
    <style>
        /* Preview foto */
        #imagePreview {
            margin-top: 10px;
        }

        .preview-item {
            position: relative;
            margin-bottom: 15px;
        }

        .preview-image {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
        }

        .preview-remove {
            position: absolute;
            top: 5px;
            right: 10px;
            background-color: rgba(255, 255, 255, 0.8);
            color: #dc3545;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
        }

        .preview-primary {
            position: absolute;
            bottom: 5px;
            left: 10px;
            background-color: rgba(13, 110, 253, 0.8);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 3px 8px;
            font-size: 12px;
        }

        /* Status description */
        .status-description .alert {
            font-size: 14px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle status description
            const statusSelect = document.getElementById('status_kamar');

            if (statusSelect) {
                statusSelect.addEventListener('change', function() {
                    const descriptions = document.querySelectorAll('.status-description .alert');

                    descriptions.forEach(desc => {
                        desc.classList.add('d-none');
                    });

                    const selectedStatus = this.value;
                    const selectedDesc = document.querySelector(`.status-description .${selectedStatus}`);

                    if (selectedDesc) {
                        selectedDesc.classList.remove('d-none');
                    }
                });

                // Trigger change event to show initial description
                statusSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endpush
