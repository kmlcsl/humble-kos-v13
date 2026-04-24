<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class InitialUsersSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama_lengkap' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => 'admin123',
                'no_telepon' => '081111111111',
                'role' => 'admin',
                'status_akun' => 'aktif',
            ]
        );

        User::updateOrCreate(
            ['username' => 'user1'],
            [
                'nama_lengkap' => 'User Pertama',
                'email' => 'user1@example.com',
                'password' => 'user123',
                'no_telepon' => '08123456789',
                'role' => 'user',
                'status_akun' => 'aktif',
            ]
        );

        User::updateOrCreate(
            ['username' => 'pemilik1'],
            [
                'nama_lengkap' => 'Pemilik Kos Pertama',
                'email' => 'pemilik1@example.com',
                'password' => 'pemilik123',
                'no_telepon' => '08129876543',
                'role' => 'pemilik_kos',
                'status_akun' => 'aktif',
            ]
        );
    }
}
