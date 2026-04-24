@extends('layouts.pemilik.app')

@section('title', 'Laporan Okupansi')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pemilik.laporan.index') }}">Laporan</a></li>
<li class="breadcrumb-item active">Okupansi</li>
@endsection

@section('page-title', 'Laporan Okupansi Kamar')

@section('page-actions')
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('pemilik.laporan.export.okupansi_excel') }}" class="btn btn-success">
            <i class="fas fa-file-excel me-1"></i> Export Excel
        </a>
        <a href="{{ route('pemilik.laporan.export.okupansi_pdf') }}" class="btn btn-danger">
            <i class="fas fa-file-pdf me-1"></i> Export PDF
        </a>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Summary Cards -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Kamar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalKamarSemua }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-door-closed fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Kamar Terisi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalKamarTerisi }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bed fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Rata-Rata Okupansi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($rataRataOkupansi, 2) }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Tingkat Okupansi per Kosan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover" id="dataTable">
                    <thead class="bg-success-subtle">
                        <tr>
                            <th>Nama Kosan</th>
                            <th class="text-center">Total Kamar</th>
                            <th class="text-center">Kamar Terisi</th>
                            <th class="text-center">Kamar Kosong</th>
                            <th style="width: 25%;">Tingkat Okupansi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($okupansiData as $data)
                            <tr>
                                <td>
                                    <strong>{{ $data['kosan']->nama_kosan }}</strong>
                                    <div class="small text-muted">{{ $data['kosan']->alamat }}</div>
                                </td>
                                <td class="text-center align-middle">{{ $data['total_kamar'] }}</td>
                                <td class="text-center align-middle">{{ $data['kamar_terisi'] }}</td>
                                <td class="text-center align-middle">{{ $data['kamar_kosong'] }}</td>
                                <td class="align-middle">
                                    <div class="progress" style="height: 25px; font-size: 14px;">
                                        <div class="progress-bar progress-bar-striped bg-primary" role="progressbar" style="width: {{ $data['tingkat_okupansi'] }}%;" aria-valuenow="{{ $data['tingkat_okupansi'] }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ number_format($data['tingkat_okupansi'], 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Anda belum memiliki data kosan atau kamar untuk ditampilkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

