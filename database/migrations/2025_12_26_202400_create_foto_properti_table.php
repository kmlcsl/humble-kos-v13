<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('foto_properti', function (Blueprint $table) {
            $table->increments('foto_id');

            // Polymorphic relationship
            $table->string('properti_type', 50); // 'kosan' atau 'kamar'
            $table->unsignedInteger('properti_id'); // kosan_id atau kamar_id

            // Foto details
            $table->string('path_foto', 255);
            $table->tinyInteger('urutan')->default(1); // 1=utama, 2-4=tambahan
            $table->boolean('is_utama')->default(false);

            // Metadata (optional untuk future enhancement)
            $table->string('caption', 255)->nullable();
            $table->integer('ukuran_file')->nullable(); // in bytes

            $table->timestamps();

            // Indexes untuk performance
            $table->index(['properti_type', 'properti_id']);
            $table->index('is_utama');
            $table->index('urutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foto_properti');
    }
};
