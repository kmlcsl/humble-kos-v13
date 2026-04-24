@extends('layouts.pemilik.app')

@section('title', 'Keamanan Akun')

@section('breadcrumb')
<li class="breadcrumb-item active">Keamanan Akun</li>
@endsection

@section('page-title', 'Keamanan Akun')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 font-weight-bold">Ubah Password</h6>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
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

                    <form action="{{ route('pemilik.pengaturan.update-password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="password_lama" class="form-label">Password Lama</label>
                            <input type="password" class="form-control" id="password_lama" name="password_lama" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_baru" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="password_baru" name="password_baru" required>
                        </div>

                        <div class="mb-3">
                            <label for="password_baru_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_baru_confirmation" name="password_baru_confirmation" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Ubah Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection