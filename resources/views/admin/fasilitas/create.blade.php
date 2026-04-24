@extends('layouts.admin.app')

@section('title', 'Tambah Fasilitas')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.fasilitas.index') }}">Manajemen Fasilitas</a></li>
<li class="breadcrumb-item active" aria-current="page">Tambah Fasilitas</li>
@endsection

@section('page-title', 'Tambah Fasilitas')

@section('page-actions')
<a href="{{ route('admin.fasilitas.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left me-2"></i>Kembali
</a>
@endsection

@section('content')

{{-- Display Errors --}}
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

<form action="{{ route('admin.fasilitas.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row">
        <div class="col-lg-8">
            <div class="admin-pemilik-card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Informasi Fasilitas</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nama_fasilitas" class="form-label required">Nama Fasilitas</label>
                        <input type="text" class="form-control @error('nama_fasilitas') is-invalid @enderror"
                            id="nama_fasilitas" name="nama_fasilitas" value="{{ old('nama_fasilitas') }}"
                            placeholder="Contoh: WiFi, AC, Kamar Mandi Dalam" required>
                        @error('nama_fasilitas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                            id="deskripsi" name="deskripsi" rows="4"
                            placeholder="Deskripsi fasilitas (opsional)">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="icon_fasilitas" class="form-label">Icon Fasilitas</label>
                        <input type="file" class="form-control @error('icon_fasilitas') is-invalid @enderror"
                            id="icon_fasilitas" name="icon_fasilitas" accept="image/*">
                        @error('icon_fasilitas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Format: JPG, PNG, SVG. Ukuran maksimal: 1MB</div>
                    </div>

                    <div class="row" id="preview-container"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="admin-pemilik-card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Publikasi</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Fasilitas yang ditambahkan akan langsung tersedia untuk dipilih pada kamar kosan.</p>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-1"></i> Simpan Fasilitas
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle file upload preview
        const fileInput = document.getElementById('icon_fasilitas');
        const previewContainer = document.getElementById('preview-container');

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                previewContainer.innerHTML = '';

                if (this.files.length > 0) {
                    const file = this.files[0];
                    const col = document.createElement('div');
                    col.className = 'col-12 mb-3';

                    const img = document.createElement('img');
                    img.className = 'img-thumbnail';
                    img.style.maxHeight = '150px';
                    img.src = URL.createObjectURL(file);

                    col.appendChild(img);
                    previewContainer.appendChild(col);
                }
            });
        }
    });
</script>
@endpush
