@extends('layouts.admin.app')

@section('title', 'Pengaturan Email')

@section('breadcrumb')
    <li class="breadcrumb-item active">Pengaturan</li>
    <li class="breadcrumb-item active">Email</li>
@endsection

@section('page-title', 'Pengaturan Email')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Pengaturan Email SMTP</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label for="mail_driver" class="form-label">Mail Driver</label>
                            <select class="form-select" id="mail_driver" disabled>
                                <option value="smtp" selected>SMTP</option>
                                <option value="sendmail">Sendmail</option>
                                <option value="mailgun">Mailgun</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="mail_host" class="form-label">SMTP Host</label>
                            <input type="text" class="form-control" id="mail_host" value="smtp.gmail.com" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="mail_port" class="form-label">SMTP Port</label>
                            <input type="number" class="form-control" id="mail_port" value="587" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="mail_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="mail_username" placeholder="your-email@gmail.com" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="mail_password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="mail_password" placeholder="••••••••" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="mail_encryption" class="form-label">Encryption</label>
                            <select class="form-select" id="mail_encryption" disabled>
                                <option value="tls" selected>TLS</option>
                                <option value="ssl">SSL</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="mail_from_address" class="form-label">From Address</label>
                            <input type="email" class="form-control" id="mail_from_address" value="noreply@humblekos.com" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="mail_from_name" class="form-label">From Name</label>
                            <input type="text" class="form-control" id="mail_from_name" value="HumbleKos" disabled>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Info:</strong> Pengaturan email SMTP saat ini dikonfigurasi melalui file .env
                        </div>

                        <button type="button" class="btn btn-secondary me-2" disabled>
                            <i class="fas fa-paper-plane me-2"></i>Test Koneksi
                        </button>
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
