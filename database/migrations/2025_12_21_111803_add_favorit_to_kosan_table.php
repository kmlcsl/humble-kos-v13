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
        Schema::table('kosan', function (Blueprint $table) {
            $table->json('favorit')->nullable()->after('foto_kosan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kosan', function (Blueprint $table) {
            $table->dropColumn('favorit');
        });
    }
};