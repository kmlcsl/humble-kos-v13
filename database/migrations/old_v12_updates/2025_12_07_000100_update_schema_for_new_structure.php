<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Users: switch password to VARCHAR, add optional fields, extend role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN password VARCHAR(255)");

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email')) {
                $table->string('email')->nullable()->after('username');
            }
            if (!Schema::hasColumn('users', 'nama_lengkap')) {
                $table->string('nama_lengkap')->nullable()->after('name');
            }
            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }
            if (!Schema::hasColumn('users', 'alamat')) {
                $table->text('alamat')->nullable()->after('no_hp');
            }
            if (!Schema::hasColumn('users', 'foto_profil')) {
                $table->string('foto_profil')->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('users', 'status_akun')) {
                $table->enum('status_akun', ['aktif', 'nonaktif', 'suspend'])->default('aktif')->after('role');
            }
            if (!Schema::hasColumn('users', 'no_telepon')) {
                $table->string('no_telepon')->nullable()->after('no_hp');
            }
        });

        // Ensure role enum includes 'pemilik_kos' (if not already handled by previous migration)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user','admin','pemilik_kos') NOT NULL DEFAULT 'user'");

        // Kosans: add validation status and rules; add optional type alias
        Schema::table('kosans', function (Blueprint $table) {
            if (!Schema::hasColumn('kosans', 'status_validasi')) {
                $table->enum('status_validasi', ['pending', 'approved', 'rejected'])->default('pending')->after('status_aktif');
            }
            if (!Schema::hasColumn('kosans', 'peraturan')) {
                $table->text('peraturan')->nullable()->after('deskripsi');
            }
            if (!Schema::hasColumn('kosans', 'tipe_kos')) {
                $table->enum('tipe_kos', ['putra','putri','campur'])->nullable()->after('jenis_kos');
            }
            if (!Schema::hasColumn('kosans', 'id_pemilik')) {
                $table->unsignedBigInteger('id_pemilik')->nullable()->after('persentase_diskon');
                $table->foreign('id_pemilik')->references('id')->on('users')->onDelete('set null');
            }
        });

        // Kamar: add room details
        Schema::table('kamar', function (Blueprint $table) {
            if (!Schema::hasColumn('kamar', 'tipe_kamar')) {
                $table->string('tipe_kamar')->nullable()->after('nomor_kamar');
            }
            if (!Schema::hasColumn('kamar', 'ukuran_kamar')) {
                $table->string('ukuran_kamar')->nullable()->after('ukuran');
            }
            if (!Schema::hasColumn('kamar', 'kapasitas')) {
                $table->integer('kapasitas')->nullable()->after('ukuran_kamar');
            }
            if (!Schema::hasColumn('kamar', 'status_kamar')) {
                $table->enum('status_kamar', ['tersedia','terisi','pemeliharaan'])->nullable()->after('status');
            }
        });

        // Ulasan: add booking relation and optional photo
        Schema::table('ulasan_kosan', function (Blueprint $table) {
            if (!Schema::hasColumn('ulasan_kosan', 'booking_id')) {
                $table->unsignedBigInteger('booking_id')->nullable()->after('id_pengguna');
                $table->foreign('booking_id')->references('id')->on('booking_kosan')->onDelete('set null');
            }
            if (!Schema::hasColumn('ulasan_kosan', 'foto_review')) {
                $table->string('foto_review')->nullable()->after('rating');
            }
        });

        // Pembayaran: add gateway and external references
        Schema::table('pembayaran', function (Blueprint $table) {
            if (!Schema::hasColumn('pembayaran', 'payment_gateway')) {
                $table->string('payment_gateway')->nullable()->after('metode_pembayaran');
            }
            if (!Schema::hasColumn('pembayaran', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('kode_pembayaran');
            }
            if (!Schema::hasColumn('pembayaran', 'bukti_transfer')) {
                $table->string('bukti_transfer')->nullable()->after('no_referensi');
            }
        });

        // Master fasilitas (baru) untuk mendukung pivot kamar_fasilitas
        if (!Schema::hasTable('fasilitas_master')) {
            Schema::create('fasilitas_master', function (Blueprint $table) {
                $table->id();
                $table->string('nama_fasilitas');
                $table->string('icon_fasilitas')->nullable();
                $table->timestamps();
            });
        }

        // Pivot kamar_fasilitas (baru)
        if (!Schema::hasTable('kamar_fasilitas')) {
            Schema::create('kamar_fasilitas', function (Blueprint $table) {
                $table->unsignedBigInteger('id_kamar');
                $table->unsignedBigInteger('fasilitas_id');
                $table->timestamps();
                $table->primary(['id_kamar', 'fasilitas_id']);
                $table->foreign('id_kamar')->references('id')->on('kamar')->onDelete('cascade');
                $table->foreign('fasilitas_id')->references('id')->on('fasilitas_master')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        // Rollback pivot and master fasilitas
        if (Schema::hasTable('kamar_fasilitas')) {
            Schema::dropIfExists('kamar_fasilitas');
        }
        if (Schema::hasTable('fasilitas_master')) {
            Schema::dropIfExists('fasilitas_master');
        }

        // Revert pembayaran additions
        Schema::table('pembayaran', function (Blueprint $table) {
            if (Schema::hasColumn('pembayaran', 'payment_gateway')) {
                $table->dropColumn('payment_gateway');
            }
            if (Schema::hasColumn('pembayaran', 'transaction_id')) {
                $table->dropColumn('transaction_id');
            }
            if (Schema::hasColumn('pembayaran', 'bukti_transfer')) {
                $table->dropColumn('bukti_transfer');
            }
        });

        // Revert ulasan additions
        Schema::table('ulasan_kosan', function (Blueprint $table) {
            if (Schema::hasColumn('ulasan_kosan', 'booking_id')) {
                $table->dropForeign(['booking_id']);
                $table->dropColumn('booking_id');
            }
            if (Schema::hasColumn('ulasan_kosan', 'foto_review')) {
                $table->dropColumn('foto_review');
            }
        });

        // Revert kamar additions
        Schema::table('kamar', function (Blueprint $table) {
            if (Schema::hasColumn('kamar', 'tipe_kamar')) {
                $table->dropColumn('tipe_kamar');
            }
            if (Schema::hasColumn('kamar', 'ukuran_kamar')) {
                $table->dropColumn('ukuran_kamar');
            }
            if (Schema::hasColumn('kamar', 'kapasitas')) {
                $table->dropColumn('kapasitas');
            }
            if (Schema::hasColumn('kamar', 'status_kamar')) {
                $table->dropColumn('status_kamar');
            }
        });

        // Revert kosans additions
        Schema::table('kosans', function (Blueprint $table) {
            if (Schema::hasColumn('kosans', 'status_validasi')) {
                $table->dropColumn('status_validasi');
            }
            if (Schema::hasColumn('kosans', 'peraturan')) {
                $table->dropColumn('peraturan');
            }
            if (Schema::hasColumn('kosans', 'tipe_kos')) {
                $table->dropColumn('tipe_kos');
            }
            if (Schema::hasColumn('kosans', 'id_pemilik')) {
                $table->dropForeign(['id_pemilik']);
                $table->dropColumn('id_pemilik');
            }
        });

        // Revert users additions and type change
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status_akun')) {
                $table->dropColumn('status_akun');
            }
            if (Schema::hasColumn('users', 'foto_profil')) {
                $table->dropColumn('foto_profil');
            }
            if (Schema::hasColumn('users', 'alamat')) {
                $table->dropColumn('alamat');
            }
            if (Schema::hasColumn('users', 'email')) {
                $table->dropColumn('email');
            }
        });

        // Restore role enum without pemilik_kos
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user','admin') NOT NULL DEFAULT 'user'");

        // Restore password column to BINARY
        DB::statement("ALTER TABLE users MODIFY COLUMN password BINARY(255)");

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nama_lengkap')) {
                $table->dropColumn('nama_lengkap');
            }
            if (Schema::hasColumn('users', 'no_telepon')) {
                $table->dropColumn('no_telepon');
            }
            // Restore 'name' column
            if (!Schema::hasColumn('users', 'name')) {
                $table->string('name');
            }
        });
    }
};
