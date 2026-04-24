<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // Drop all existing tables except the migrations table
        try {
            $tables = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
            foreach ($tables as $row) {
                $tableName = array_values((array) $row)[0] ?? null;
                if ($tableName && $tableName !== 'migrations') {
                    Schema::dropIfExists($tableName);
                }
            }
        } catch (\Throwable $e) {}

        // 1) USERS
        Schema::create('users', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('nama_lengkap', 100);
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique()->nullable();
            $table->string('password', 250);
            $table->string('no_telepon', 15)->nullable();
            $table->enum('role', ['user','pemilik_kos','admin'])->default('user');
            $table->text('alamat')->nullable();
            $table->string('foto_profil', 255)->nullable();
            $table->enum('status_akun', ['aktif','nonaktif','suspended'])->default('aktif');
            $table->timestamps();
        });

        // 2) KOSAN
        Schema::create('kosan', function (Blueprint $table) {
            $table->increments('kosan_id');
            $table->unsignedInteger('owner_id')->nullable();
            $table->string('nama_kosan', 150);
            $table->text('alamat')->nullable();
            $table->string('kota', 100)->nullable();
            $table->text('deskripsi')->nullable();
            $table->enum('tipe_kosan', ['putra','putri','campur'])->nullable();
            $table->text('peraturan')->nullable();
            $table->string('foto_kosan', 255)->nullable();
            $table->decimal('rating_rata', 3, 2)->nullable();
            $table->enum('status_validasi', ['pending','approved','rejected'])->default('pending');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 10, 8)->nullable();
            $table->timestamps();
            $table->foreign('owner_id')->references('user_id')->on('users')->onDelete('set null');
        });

        // 3) KAMAR
        Schema::create('kamar', function (Blueprint $table) {
            $table->increments('kamar_id');
            $table->unsignedInteger('kosan_id');
            $table->string('nomor_kamar', 50);
            $table->enum('tipe_kamar', ['single','double','shared'])->nullable();
            $table->decimal('harga_per_bulan', 10, 2);
            $table->string('ukuran_kamar', 20);
            $table->integer('kapasitas');
            $table->text('deskripsi')->nullable();
            $table->string('foto_kamar', 255)->nullable();
            $table->enum('status_kamar', ['tersedia','terisi','maintenance'])->default('tersedia');
            $table->timestamps();
            $table->unique(['kosan_id','nomor_kamar']);
            $table->foreign('kosan_id')->references('kosan_id')->on('kosan')->onDelete('cascade');
        });

        // 4) FASILITAS (master)
        Schema::create('fasilitas', function (Blueprint $table) {
            $table->increments('fasilitas_id');
            $table->string('nama_fasilitas', 100);
            $table->string('icon_fasilitas', 255)->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 5) KAMAR_FASILITAS (pivot)
        Schema::create('kamar_fasilitas', function (Blueprint $table) {
            $table->increments('kamar_fasilitas_id');
            $table->unsignedInteger('kamar_id');
            $table->unsignedInteger('fasilitas_id');
            $table->timestamp('created_at')->nullable();
            $table->foreign('kamar_id')->references('kamar_id')->on('kamar')->onDelete('cascade');
            $table->foreign('fasilitas_id')->references('fasilitas_id')->on('fasilitas')->onDelete('cascade');
            $table->index(['kamar_id']);
            $table->index(['fasilitas_id']);
        });

        // 6) BOOKING
        Schema::create('booking', function (Blueprint $table) {
            $table->increments('booking_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('kamar_id')->nullable();
            $table->date('tanggal_checkin');
            $table->date('tanggal_checkout');
            $table->integer('durasi');
            $table->decimal('total_harga', 10, 2);
            $table->string('kode_booking', 50)->nullable();
            $table->enum('status_booking', ['pending','confirmed','cancelled'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('kamar_id')->references('kamar_id')->on('kamar')->onDelete('set null');
        });

        // 7) PEMBAYARAN
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->increments('pembayaran_id');
            $table->unsignedInteger('booking_id');
            $table->enum('tipe_pembayaran', ['manual','gateway'])->nullable();
            $table->enum('metode_pembayaran', ['transfer','e-wallet','kartu_kredit','qris','convenience_store','tunai']);
            $table->string('payment_gateway', 50)->nullable();
            $table->string('transaction_id', 100)->nullable();
            $table->decimal('jumlah_bayar', 10, 2);
            $table->string('bukti_transfer', 255)->nullable();
            $table->enum('status_pembayaran', ['pending','paid','failed','expired'])->default('pending');
            $table->timestamp('tanggal_bayar')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->foreign('booking_id')->references('booking_id')->on('booking')->onDelete('cascade');
        });

        // 8) ULASAN REVIEW
        Schema::create('ulasan_review', function (Blueprint $table) {
            $table->increments('review_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('kosan_id');
            $table->unsignedInteger('booking_id')->nullable();
            $table->integer('rating');
            $table->integer('komentar')->nullable();
            $table->string('foto_review', 255)->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('kosan_id')->references('kosan_id')->on('kosan')->onDelete('cascade');
            $table->foreign('booking_id')->references('booking_id')->on('booking')->onDelete('set null');
        });

        // 9) SESSIONS
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // 10) CACHE
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('ulasan_review');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('booking');
        Schema::dropIfExists('kamar_fasilitas');
        Schema::dropIfExists('fasilitas');
        Schema::dropIfExists('kamar');
        Schema::dropIfExists('kosan');
        Schema::dropIfExists('users');
        Schema::enableForeignKeyConstraints();
    }
};
