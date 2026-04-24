@extends('layouts.admin.app')

@section('title', 'Pengaturan Tampilan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Pengaturan</li>
    <li class="breadcrumb-item active">Tampilan</li>
@endsection

@section('page-title', 'Pengaturan Tampilan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-palette me-2"></i>Pengaturan Tampilan</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Tema Warna</label>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="theme" id="theme_default" checked>
                                        <label class="form-check-label" for="theme_default">
                                            Default (Hijau)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="theme" id="theme_blue">
                                        <label class="form-check-label" for="theme_blue">
                                            Biru
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="theme" id="theme_dark">
                                        <label class="form-check-label" for="theme_dark">
                                            Gelap
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="logo" class="form-label">Logo Aplikasi</label>
                            <input type="file" class="form-control" id="logo" accept="image/*" disabled>
                            <small class="text-muted">Format: PNG, JPG. Ukuran maksimal: 2MB</small>
                        </div>

                        <div class="mb-3">
                            <label for="favicon" class="form-label">Favicon</label>
                            <input type="file" class="form-control" id="favicon" accept="image/*" disabled>
                            <small class="text-muted">Format: ICO, PNG. Ukuran: 32x32px</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="show_footer" checked disabled>
                                <label class="form-check-label" for="show_footer">
                                    Tampilkan Footer
                                </label>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Info:</strong> Fitur pengaturan tampilan masih dalam pengembangan.
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
