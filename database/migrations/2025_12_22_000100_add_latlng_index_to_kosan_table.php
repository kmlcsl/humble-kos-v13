<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Add individual indexes; composite may not help for haversine but improves filters
        Schema::table('kosan', function (Blueprint $table) {
            $table->index('latitude', 'kosan_latitude_index');
            $table->index('longitude', 'kosan_longitude_index');
        });
    }

    public function down(): void
    {
        Schema::table('kosan', function (Blueprint $table) {
            $table->dropIndex('kosan_latitude_index');
            $table->dropIndex('kosan_longitude_index');
        });
    }
};
