<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Drop external foreign keys referencing kamar.id
        try { DB::statement("ALTER TABLE foto_kamar DROP FOREIGN KEY foto_kamar_id_kamar_foreign"); } catch (\Throwable $e) {}
        try { DB::statement("ALTER TABLE kamar_fasilitas DROP FOREIGN KEY kamar_fasilitas_id_kamar_foreign"); } catch (\Throwable $e) {}
        try { DB::statement("ALTER TABLE booking_kosan DROP FOREIGN KEY booking_kosan_id_kamar_foreign"); } catch (\Throwable $e) {}

        // Rename primary key id -> kamar_id (keep BIGINT to match existing FK column types)
        if (Schema::hasColumn('kamar', 'id') && !Schema::hasColumn('kamar', 'kamar_id')) {
            DB::statement("ALTER TABLE kamar CHANGE COLUMN id kamar_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
        }

        // Ensure foreign key column names per spec
        if (Schema::hasColumn('kamar', 'id_kosan') && !Schema::hasColumn('kamar', 'kosan_id')) {
            DB::statement("ALTER TABLE kamar CHANGE COLUMN id_kosan kosan_id BIGINT UNSIGNED NOT NULL");
        }

        // Rename fields to match spec
        if (Schema::hasColumn('kamar', 'ukuran') && !Schema::hasColumn('kamar', 'ukuran_kamar')) {
            DB::statement("ALTER TABLE kamar CHANGE COLUMN ukuran ukuran_kamar VARCHAR(20) NOT NULL");
        }
        if (Schema::hasColumn('kamar', 'harga_bulanan')) {
            DB::statement("ALTER TABLE kamar CHANGE COLUMN harga_bulanan harga_per_bulan DECIMAL(10,2) NOT NULL");
        }

        // Add missing fields
        if (!Schema::hasColumn('kamar', 'tipe_kamar')) {
            DB::statement("ALTER TABLE kamar ADD COLUMN tipe_kamar ENUM('single','double','shared') NULL AFTER nomor_kamar");
        }
        if (!Schema::hasColumn('kamar', 'kapasitas')) {
            DB::statement("ALTER TABLE kamar ADD COLUMN kapasitas INT NULL AFTER ukuran_kamar");
        }
        if (!Schema::hasColumn('kamar', 'foto_kamar')) {
            DB::statement("ALTER TABLE kamar ADD COLUMN foto_kamar VARCHAR(255) NULL AFTER deskripsi");
        }

        // Status rename and values normalization
        // If column 'status' exists, rename to 'status_kamar' and normalize 'pemeliharaan' -> 'maintenance'
        if (Schema::hasColumn('kamar', 'status') && !Schema::hasColumn('kamar', 'status_kamar')) {
            DB::statement("UPDATE kamar SET status = 'maintenance' WHERE status = 'pemeliharaan'");
            DB::statement("ALTER TABLE kamar CHANGE COLUMN status status_kamar ENUM('tersedia','terisi','maintenance') NOT NULL DEFAULT 'tersedia'");
        } elseif (Schema::hasColumn('kamar', 'status_kamar')) {
            DB::statement("UPDATE kamar SET status_kamar = 'maintenance' WHERE status_kamar = 'pemeliharaan'");
        }

        // Drop unused price columns no longer in spec
        try { DB::statement("ALTER TABLE kamar DROP COLUMN harga_tiga_bulan"); } catch (\Throwable $e) {}
        try { DB::statement("ALTER TABLE kamar DROP COLUMN harga_semester"); } catch (\Throwable $e) {}
        try { DB::statement("ALTER TABLE kamar DROP COLUMN harga_tahunan"); } catch (\Throwable $e) {}

        // Recreate unique index with new column names (best-effort)
        try { DB::statement("ALTER TABLE kamar DROP INDEX kamar_id_nomor_kamar_unique"); } catch (\Throwable $e) {}
        try { DB::statement("ALTER TABLE kamar DROP INDEX kamar_id_nomor_kamar_unique"); } catch (\Throwable $e) {}
        DB::statement("ALTER TABLE kamar ADD UNIQUE INDEX uniq_kosan_kamar (kosan_id, nomor_kamar)");

        // Fix foreign key to kosan (we will handle table name changes separately)
        // Drop existing FK if any
        try { DB::statement("ALTER TABLE kamar DROP FOREIGN KEY kamar_id_kosan_foreign"); } catch (\Throwable $e) {}
        // Re-add foreign key to kosans.id or kosan.kosan_id depending which exists
        try {
            DB::statement("ALTER TABLE kamar ADD CONSTRAINT fk_kamar_kosan FOREIGN KEY (kosan_id) REFERENCES kosans(id) ON DELETE CASCADE");
        } catch (\Throwable $e) {
            DB::statement("ALTER TABLE kamar ADD CONSTRAINT fk_kamar_kosan FOREIGN KEY (kosan_id) REFERENCES kosan(kosan_id) ON DELETE CASCADE");
        }

        // Recreate external foreign keys to point to kamar.kamar_id
        try { DB::statement("ALTER TABLE foto_kamar ADD CONSTRAINT fk_fotokamar_kamar FOREIGN KEY (id_kamar) REFERENCES kamar(kamar_id) ON DELETE CASCADE"); } catch (\Throwable $e) {}
        try { DB::statement("ALTER TABLE kamar_fasilitas ADD CONSTRAINT fk_kamarfasilitas_kamar FOREIGN KEY (id_kamar) REFERENCES kamar(kamar_id) ON DELETE CASCADE"); } catch (\Throwable $e) {}
        try { DB::statement("ALTER TABLE booking_kosan ADD CONSTRAINT fk_booking_kamar FOREIGN KEY (id_kamar) REFERENCES kamar(kamar_id) ON DELETE CASCADE"); } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        // Revert to previous structure (best-effort)
        DB::statement("ALTER TABLE kamar CHANGE COLUMN kamar_id id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
        DB::statement("ALTER TABLE kamar CHANGE COLUMN kosan_id id_kosan BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE kamar CHANGE COLUMN ukuran_kamar ukuran VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE kamar CHANGE COLUMN harga_per_bulan harga_bulanan DECIMAL(12,2) NOT NULL");
        DB::statement("ALTER TABLE kamar DROP COLUMN tipe_kamar");
        DB::statement("ALTER TABLE kamar DROP COLUMN kapasitas");
        DB::statement("ALTER TABLE kamar DROP COLUMN foto_kamar");
        DB::statement("ALTER TABLE kamar CHANGE COLUMN status_kamar status ENUM('tersedia','terisi','pemeliharaan') NOT NULL DEFAULT 'tersedia'");
        DB::statement("ALTER TABLE kamar ADD COLUMN harga_tiga_bulan DECIMAL(12,2) NULL");
        DB::statement("ALTER TABLE kamar ADD COLUMN harga_semester DECIMAL(12,2) NULL");
        DB::statement("ALTER TABLE kamar ADD COLUMN harga_tahunan DECIMAL(12,2) NULL");
    }
};
