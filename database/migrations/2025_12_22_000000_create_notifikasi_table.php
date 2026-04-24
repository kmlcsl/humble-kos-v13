<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('notifikasi_id');
            $table->unsignedBigInteger('user_id');
            $table->string('type', 50);
            $table->string('title', 150);
            $table->text('message')->nullable();
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index('user_id');
            $table->index('is_read');
            $table->index(['user_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
