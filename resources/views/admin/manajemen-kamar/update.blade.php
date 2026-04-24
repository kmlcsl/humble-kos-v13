@extends('layouts.admin.app')

@section('title', 'Edit Kamar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.manajemen-kamar.index') }}">Manajemen Kamar</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Kamar</li>
@endsection

@section('page-title', 'Edit Kamar')

@section('page-actions')
    <div class="btn-group" role="group">
        <a href="{{ route('admin.manajemen-kamar.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
@endsection

@section('content')

    <form action="{{ route('admin.manajemen-kamar.update', $kamars->kamar_id) }}" method="POST" enctype="multipart/form-data" novalidate>

        @csrf

        @method('PUT')



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
                                        {{ old('kosan_id', $kamars->kosan_id) == $kosan->kosan_id ? 'selected' : '' }}>
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
                                id="nomor_kamar" name="nomor_kamar" value="{{ old('nomor_kamar', $kamars->nomor_kamar) }}"
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
                                    <option value="single" {{ old('tipe_kamar', $kamars->tipe_kamar) == 'single' ? 'selected' : '' }}>Single
                                    </option>
                                    <option value="double" {{ old('tipe_kamar', $kamars->tipe_kamar) == 'double' ? 'selected' : '' }}>Double
                                    </option>
                                    <option value="shared" {{ old('tipe_kamar', $kamars->tipe_kamar) == 'shared' ? 'selected' : '' }}>Shared
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
                                    id="kapasitas" name="kapasitas" value="{{ old('kapasitas', $kamars->kapasitas) }}" min="1"
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
                                name="ukuran_kamar" value="{{ old('ukuran_kamar', $kamars->ukuran_kamar) }}" placeholder="Contoh: 3x4 m atau 12 m²" required>
                            @error('ukuran_kamar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi Kamar</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3"
                                placeholder="Deskripsi detail tentang kamar...">{{ old('deskripsi', $kamars->deskripsi) }}</textarea>
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
                                        id="harga_per_bulan" name="harga_per_bulan" value="{{ old('harga_per_bulan', $kamars->harga_per_bulan) }}"
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
                            @php
                                $selectedFasilitas = old('fasilitas', $kamars->fasilitas->pluck('fasilitas_id')->toArray());
                            @endphp
                            @forelse ($fasilitas as $item)
                                <div class="col-md-4 col-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="fasilitas[]"
                                            value="{{ $item->fasilitas_id }}" id="fasilitas_{{ $item->fasilitas_id }}"
                                            {{ in_array($item->fasilitas_id, $selectedFasilitas) ? 'checked' : '' }}>
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

                        <!-- Foto Utama -->
                        <div class="mb-4">
                            <h6 class="mb-3">Foto Utama</h6>
                            @if ($kamars->foto_kamar)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $kamars->foto_kamar) }}" alt="Foto Utama" class="img-thumbnail" style="max-width: 250px; height: auto;">
                                </div>
                            @else
                                <div class="alert alert-warning small p-2">Belum ada foto utama.</div>
                            @endif
                            <div class="mt-2">
                                <label for="foto_kamar" class="form-label">{{ $kamars->foto_kamar ? 'Ganti' : 'Upload' }} Foto Utama</label>
                                <input type="file" class="form-control @error('foto_kamar') is-invalid @enderror" id="foto_kamar" name="foto_kamar" accept="image/*">
                                @error('foto_kamar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Foto ini akan menjadi gambar utama untuk kamar ini.</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Foto Lainnya -->
                        <div>
                            <h6 class="mb-3">Foto Lainnya</h6>
                            @if ($kamars->fotos && $kamars->fotos->isNotEmpty())
                                <div class="row g-2">
                                    @foreach ($kamars->fotos as $foto)
                                        <div class="col-md-6 col-lg-3">
                                            <div class="position-relative">
                                                <img src="{{ asset('storage/' . $foto->path_foto) }}" alt="Foto {{ $foto->urutan }}" class="img-thumbnail" style="width: 100%; height: 150px; object-fit: cover;">
                                                <div class="form-check position-absolute bottom-0 inset-e-0 m-2 bg-white rounded px-2 py-1">
                                                    <input class="form-check-input" type="checkbox" name="hapus_foto[]" value="{{ $foto->foto_id }}" id="hapus_foto_{{ $foto->foto_id }}">
                                                    <label class="form-check-label small text-danger" for="hapus_foto_{{ $foto->foto_id }}">
                                                        Hapus
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-text mt-2">Centang "Hapus" untuk menghapus foto yang tidak diinginkan saat update.</div>
                            @else
                                 <div class="alert alert-info small p-2">Belum ada foto lainnya.</div>
                            @endif
                            <div class="mt-3">
                                <label for="foto_tambahan" class="form-label">Tambah Foto Lainnya (Max 3)</label>
                                <input type="file" class="form-control @error('foto_tambahan.*') is-invalid @enderror" id="foto_tambahan" name="foto_tambahan[]" accept="image/*" multiple>
                                @error('foto_tambahan.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
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
                                <option value="tersedia" {{ old('status_kamar', $kamars->status_kamar) == 'tersedia' ? 'selected' : '' }}>Tersedia
                                </option>
                                <option value="terisi" {{ old('status_kamar', $kamars->status_kamar) == 'terisi' ? 'selected' : '' }}>Terisi</option>
                                <option value="maintenance" {{ old('status_kamar', $kamars->status_kamar) == 'maintenance' ? 'selected' : '' }}>
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
