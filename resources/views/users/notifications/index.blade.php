@extends('layouts.user.app')

@section('title', 'Notifikasi Saya')
@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-header" style="background-color: #d3d3d3;">
                        <h4 class="mb-0">Notifikasi Anda</h4>
                    </div>
                    <div class="card-body" style="background-color: #e6e9ee;">
                        @forelse ($notifications as $notification)
                            <div class="card notifikasi-card mb-3" style="background-color: #e6e9ee;">
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            @if ($notification->is_read)
                                                <span class="badge bg-secondary">Dibaca</span>
                                            @else
                                                <span class="badge bg-primary">Baru</span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="mb-1">
                                                {{ $notification->message ?? 'Pemberitahuan tidak memiliki pesan.' }}
                                            </p>
                                            <small class="text-muted">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <p>Tidak ada notifikasi untuk Anda saat ini.</p>
                            </div>
                        @endforelse

                        <div class="d-flex justify-content-center mt-4">
                            {{ $notifications->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection