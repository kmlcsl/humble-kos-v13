<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('kamar')) {
            try { DB::statement("ALTER TABLE kamar DROP FOREIGN KEY fk_kamar_kosan"); } catch (\Throwable $e) {}
            try { DB::statement("ALTER TABLE kamar DROP FOREIGN KEY kamar_id_kosan_foreign"); } catch (\Throwable $e) {}
            try { DB::statement("ALTER TABLE kamar ADD CONSTRAINT fk_kamar_kosan FOREIGN KEY (kosan_id) REFERENCES kosan(kosan_id) ON DELETE CASCADE"); } catch (\Throwable $e) {}
        }
        if (Schema::hasTable('fasilitas')) {
            try { DB::statement("ALTER TABLE fasilitas DROP FOREIGN KEY fasilitas_id_kosan_foreign"); } catch (\Throwable $e) {}
        }
        if (Schema::hasTable('foto_kosan')) {
            try { DB::statement("ALTER TABLE foto_kosan DROP FOREIGN KEY foto_kosan_id_kosan_foreign"); } catch (\Throwable $e) {}
        }
        if (Schema::hasTable('ulasan_kosan')) {
            try { DB::statement("ALTER TABLE ulasan_kosan DROP FOREIGN KEY ulasan_kosan_id_kosan_foreign"); } catch (\Throwable $e) {}
        }
        if (Schema::hasTable('kosan_favorit')) {
            try { DB::statement("ALTER TABLE kosan_favorit DROP FOREIGN KEY kosan_favorit_id_kosan_foreign"); } catch (\Throwable $e) {}
        }
        if (Schema::hasTable('booking_kosan')) {
            try { DB::statement("ALTER TABLE booking_kosan DROP FOREIGN KEY booking_kosan_id_kosan_foreign"); } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('kosans')) {
            Schema::dropIfExists('kosans');
        }
    }

    public function down(): void
    {
    }
};
