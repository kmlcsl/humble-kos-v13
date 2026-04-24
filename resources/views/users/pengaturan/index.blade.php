@extends('layouts.user.app')

@section('title', 'Pengaturan Akun')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Pengaturan Akun</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <!-- Change Password Card -->
            <div class="card shadow mb-4" style="background-color: #e6e9ee;">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ubah Password</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.settings.update-password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group mb-4">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Ubah Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection