<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('kamar_fasilitas')) {
            Schema::table('kamar_fasilitas', function (Blueprint $table) {
                if (Schema::hasColumn('kamar_fasilitas', 'fasilitas_id')) {
                    $table->dropForeign(['fasilitas_id']);
                }
                if (Schema::hasColumn('kamar_fasilitas', 'id_kamar')) {
                    $table->dropForeign(['id_kamar']);
                }

                $table->foreign('id_kamar')->references('id')->on('kamar')->onDelete('cascade');
                $table->foreign('fasilitas_id')->references('id')->on('fasilitas')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('fasilitas_master')) {
            Schema::dropIfExists('fasilitas_master');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('fasilitas_master')) {
            Schema::create('fasilitas_master', function (Blueprint $table) {
                $table->id();
                $table->string('nama_fasilitas');
                $table->string('icon_fasilitas')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('kamar_fasilitas')) {
            Schema::table('kamar_fasilitas', function (Blueprint $table) {
                if (Schema::hasColumn('kamar_fasilitas', 'fasilitas_id')) {
                    $table->dropForeign(['fasilitas_id']);
                }
                if (Schema::hasColumn('kamar_fasilitas', 'id_kamar')) {
                    $table->dropForeign(['id_kamar']);
                }

                $table->foreign('id_kamar')->references('id')->on('kamar')->onDelete('cascade');
                $table->foreign('fasilitas_id')->references('id')->on('fasilitas_master')->onDelete('cascade');
            });
        }
    }
};

