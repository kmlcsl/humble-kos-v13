<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Ubah kolom menjadi VARCHAR dulu untuk bisa update data
        DB::statement("ALTER TABLE users MODIFY COLUMN status_akun VARCHAR(50) NOT NULL DEFAULT 'aktif'");

        // Step 2: Update semua data existing menjadi 'aktif'
        DB::table('users')->update(['status_akun' => 'aktif']);

        // Step 3: Ubah kolom ke enum yang benar
        DB::statement("ALTER TABLE users MODIFY COLUMN status_akun ENUM('aktif', 'nonaktif', 'suspended') NOT NULL DEFAULT 'aktif'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke struktur lama (jika rollback)
        DB::statement("ALTER TABLE users MODIFY COLUMN status_akun ENUM('user', 'pemilik_kos', 'admin') NOT NULL DEFAULT 'user'");

        // Sinkronkan kembali status_akun dengan role
        DB::statement("UPDATE users SET status_akun = role");
    }
};
