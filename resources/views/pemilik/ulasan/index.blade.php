@extends('layouts.pemilik.app')

@section('title', 'Ulasan & Rating')

@section('breadcrumb')
<li class="breadcrumb-item active">Ulasan & Rating</li>
@endsection

@section('page-title', 'Ulasan & Rating Kosan Anda')

@section('content')
<div class="container-fluid">
    <!-- Stat Cards -->
    <div class="row">
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Ulasan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUlasan }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-comments fa-2x text-primary"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Rata-Rata Rating</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-star text-warning me-1"></i> {{ number_format($rataRataRating, 2) }} / 5.00
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-star-half-alt fa-2x text-warning"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Filter & Review List Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 font-weight-bold">Daftar Ulasan</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('pemilik.ulasan.index') }}" method="GET" class="mb-4">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Cari komentar/user..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="kosan_id" class="form-select">
                                    <option value="">Semua Kosan</option>
                                    @foreach($kosanList as $kosan)
                                        <option value="{{ $kosan->kosan_id }}" @if(request('kosan_id') == $kosan->kosan_id) selected @endif>{{ $kosan->nama_kosan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="rating" class="form-select">
                                    <option value="">Semua Rating</option>
                                    @for ($i = 5; $i >= 1; $i--)
                                        <option value="{{ $i }}" @if(request('rating') == $i) selected @endif>{{ $i }} Bintang</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </div>
                    </form>

                    @forelse($ulasanList as $ulasan)
                        <div class="d-flex mb-4 border-bottom pb-3">
                            <div class="shrink-0 me-3">
                                <img class="rounded-circle" src="{{ $ulasan->user->profile_photo_url ?? asset('images/default-avatar.svg') }}" alt="{{ $ulasan->user->name }}" style="width: 50px; height: 50px; object-fit: cover;">
                            </div>
                            <div class="w-100">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="fw-bold mb-0">{{ $ulasan->user->name ?? 'User Anonim' }}</h5>
                                        <small class="text-muted">{{ $ulasan->kosan->nama_kosan }}</small>
                                    </div>
                                    <div class="text-nowrap">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $ulasan->rating ? 'text-warning' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="mt-2 mb-1">{{ $ulasan->komentar }}</p>
                                <small class="text-muted">{{ $ulasan->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-comment-slash fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-muted">Tidak ada ulasan ditemukan.</h5>
                            <p>Coba ubah filter Anda atau tunggu ulasan baru masuk.</p>
                        </div>
                    @endforelse
                    
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $ulasanList->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <!-- Rating Distribution Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white py-3">
                    <h6 class="m-0 font-weight-bold">Distribusi Rating</h6>
                </div>
                <div class="card-body">
                    @php $totalRatingsForPercent = array_sum($ratingDistribution); @endphp
                    @foreach($ratingDistribution as $star => $count)
                        @php
                            $percentage = $totalRatingsForPercent > 0 ? ($count / $totalRatingsForPercent) * 100 : 0;
                        @endphp
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>{{ $star }} Bintang</span>
                                <span class="text-gray-600">{{ $count }} Ulasan</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

