@extends('layouts.user.app')

@section('title', 'Pembayaran Manual - Booking #' . $booking->booking_id)

@section('content')
    <div class="container-fluid mb-5">
        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <!-- Informasi Rekening -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-university me-2"></i>Informasi Rekening Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="bank-info p-3 border rounded">
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="{{ asset('images/bca-logo.png') }}" alt="BCA" height="30"
                                            class="me-2" onerror="this.style.display='none'">
                                        <strong>Bank BCA</strong>
                                    </div>
                                    <p class="mb-1">No. Rekening: <strong>1234567890</strong></p>
                                    <p class="mb-0">Atas Nama: <strong>HUMBLE KOS INDONESIA</strong></p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="bank-info p-3 border rounded">
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="{{ asset('images/mandiri-logo.png') }}" alt="Mandiri" height="30"
                                            class="me-2" onerror="this.style.display='none'">
                                        <strong>Bank Mandiri</strong>
                                    </div>
                                    <p class="mb-1">No. Rekening: <strong>0987654321</strong></p>
                                    <p class="mb-0">Atas Nama: <strong>HUMBLE KOS INDONESIA</strong></p>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Jumlah yang harus ditransfer:</strong> {{ $pembayaran->formatted_jumlah }}
                        </div>
                    </div>
                </div>

                <!-- Form Upload Bukti -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-upload me-2"></i>Upload Bukti Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('users.pembayaran.upload-bukti', $booking->booking_id) }}" method="POST"
                            enctype="multipart/form-data" id="uploadForm">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama_pengirim" class="form-label">Nama Pengirim <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nama_pengirim" name="nama_pengirim"
                                            value="{{ old('nama_pengirim', Auth::user()->name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="bank_pengirim" class="form-label">Bank Pengirim <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="bank_pengirim" name="bank_pengirim" required>
                                            <option value="">Pilih Bank</option>
                                            <option value="BCA">BCA</option>
                                            <option value="Mandiri">Mandiri</option>
                                            <option value="BRI">BRI</option>
                                            <option value="BNI">BNI</option>
                                            <option value="CIMB">CIMB Niaga</option>
                                            <option value="Danamon">Danamon</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nomor_rekening_pengirim" class="form-label">Nomor Rekening Pengirim
                                            <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nomor_rekening_pengirim"
                                            name="nomor_rekening_pengirim" value="{{ old('nomor_rekening_pengirim') }}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jumlah_transfer" class="form-label">Jumlah Transfer <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="jumlah_transfer"
                                            name="jumlah_transfer" value="{{ old('jumlah_transfer', $pembayaran->jumlah_bayar) }}"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="tanggal_transfer" class="form-label">Tanggal Transfer <span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="tanggal_transfer"
                                    name="tanggal_transfer"
                                    value="{{ old('tanggal_transfer', now()->format('Y-m-d\TH:i')) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="file_bukti" class="form-label">File Bukti Transfer <span
                                        class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="file_bukti" name="file_bukti"
                                    accept="image/*,.pdf" required>
                                <div class="form-text">
                                    Format yang diterima: JPG, PNG, PDF. Maksimal 2MB.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="catatan" class="form-label">Catatan (Opsional)</label>
                                <textarea class="form-control" id="catatan" name="catatan" rows="3">{{ old('catatan') }}</textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                    <i class="fas fa-upload me-2"></i>Upload Bukti Pembayaran
                                </button>
                                <a href="{{ route('users.pembayaran.index', $booking->booking_id) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Detail Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Booking ID</strong></td>
                                <td>: {{ $booking->booking_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kode Pembayaran</strong></td>
                                <td>: {{ $pembayaran->transaction_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total</strong></td>
                                <td>: {{ $pembayaran->formatted_jumlah }}</td>
                            </tr>
                            <tr>
                                <td><strong>Metode</strong></td>
                                <td>: Transfer Manual</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Petunjuk Transfer</h5>
                    </div>
                    <div class="card-body">
                        <ol class="ps-3">
                            <li class="mb-2">Transfer sesuai nominal yang tertera</li>
                            <li class="mb-2">Screenshot/foto bukti transfer</li>
                            <li class="mb-2">Upload bukti transfer di form ini</li>
                            <li class="mb-2">Tunggu verifikasi admin (maks 1x24 jam)</li>
                            <li class="mb-2">Booking akan dikonfirmasi setelah verifikasi</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('uploadForm');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengupload...';
            });

            // Validasi file
            const fileInput = document.getElementById('file_bukti');
            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) { // 2MB
                        alert('Ukuran file maksimal 2MB');
                        this.value = '';
                    }
                }
            });
        });
    </script>
@endpush
