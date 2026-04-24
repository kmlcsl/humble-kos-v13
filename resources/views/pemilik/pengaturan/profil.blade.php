@extends('layouts.pemilik.app')

@section('title', 'Profil Saya')

@section('breadcrumb')
<li class="breadcrumb-item active">Profil Saya</li>
@endsection

@section('page-title', 'Pengaturan Profil')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4">
            <!-- Profile Picture Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 font-weight-bold">Foto Profil</h6>
                </div>
                <div class="card-body text-center">
                    @if(Auth::user()->foto_profil)
                        <img class="img-fluid rounded-circle mb-3" src="{{ asset('storage/' . Auth::user()->foto_profil) }}" alt="Foto Profil" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 150px; height: 150px; font-size: 60px;">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                    
                    <h5 class="card-title">{{ Auth::user()->name }}</h5>
                    <p class="card-text text-muted">{{ Auth::user()->email }}</p>

                    <form action="{{ route('pemilik.pengaturan.update-foto-profil') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <input type="file" class="form-control" id="foto_profil" name="foto_profil" required>
                            <button class="btn btn-primary" type="submit">Unggah</button>
                        </div>
                        @error('foto_profil')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </form>

                    @if (Auth::user()->foto_profil)
                    <form action="{{ route('pemilik.pengaturan.remove-foto-profil') }}" method="POST" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus Foto</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <!-- Edit Profile Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 font-weight-bold">Edit Informasi Profil</h6>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if ($errors->any() && !$errors->has('foto_profil'))
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('pemilik.pengaturan.update-profil') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="no_telepon" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}">
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3">{{ old('alamat', $user->alamat) }}</textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection