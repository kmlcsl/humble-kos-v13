<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Kolom ID otomatis
            $table->string('name');
            $table->string('username')->unique(); // Username unik untuk login
            $table->binary('password'); // Password disimpan sebagai BINARY untuk AES_ENCRYPT
            $table->string('remember_token', 100)->nullable();
            $table->string('no_hp')->nullable(); // Nomor HP, nullable karena Admin tidak wajib mengisi
            $table->enum('role', ['user', 'admin'])->default('user'); // Role: user atau admin,
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('users'); // Drop tabel jika rollback
    }
}
