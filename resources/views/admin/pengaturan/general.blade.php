@extends('layouts.admin.app')

@section('title', 'Pengaturan Umum')

@section('breadcrumb')
    <li class="breadcrumb-item active">Pengaturan</li>
    <li class="breadcrumb-item active">Umum</li>
@endsection

@section('page-title', 'Pengaturan Umum')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Pengaturan Umum Aplikasi</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label for="app_name" class="form-label">Nama Aplikasi</label>
                            <input type="text" class="form-control" id="app_name" value="HumbleKos">
                        </div>

                        <div class="mb-3">
                            <label for="app_description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="app_description" rows="3">Sistem Manajemen Kos Terpadu</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="contact_email" class="form-label">Email Kontak</label>
                            <input type="email" class="form-control" id="contact_email" value="admin@humblekos.com">
                        </div>

                        <div class="mb-3">
                            <label for="contact_phone" class="form-label">Telepon Kontak</label>
                            <input type="text" class="form-control" id="contact_phone" value="+62 812-3456-7890">
                        </div>

                        <div class="mb-3">
                            <label for="timezone" class="form-label">Zona Waktu</label>
                            <select class="form-select" id="timezone">
                                <option value="Asia/Jakarta" selected>Asia/Jakarta (WIB)</option>
                                <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                            </select>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Info:</strong> Fitur pengaturan ini masih dalam pengembangan. Saat ini hanya untuk tampilan.
                        </div>

                        <button type="button" class="btn btn-primary" disabled>
                            <i class="fas fa-save me-2"></i>Simpan Pengaturan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
