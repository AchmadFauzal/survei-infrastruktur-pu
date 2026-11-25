<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Infrastructure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Akun Admin
        User::create([
            'name' => 'Admin PU',
            'email' => 'admin@pu.go.id',
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        // Buat Akun Surveyor
        User::create([
            'name' => 'Surveyor Lapangan',
            'email' => 'surveyor@pu.go.id',
            'role' => 'surveyor',
            'password' => Hash::make('password123'),
        ]);

        // Data Titik Contoh 1 (Belum Survei - Marker Merah)
        Infrastructure::create([
            'nama_objek' => 'tes',
            'kategori' => 'CK',
            'kota' => 'Pidie Jaya',
            'provinsi' => 'Aceh',
            'latitude' => 5.1999,
            'longitude' => 96.1107,
            'status_survei' => 'belum',
        ]);

        // Data Titik Contoh 2 (Sudah Survei - Marker Hijau)
        Infrastructure::create([
            'nama_objek' => 'Jembatan Meureudu',
            'kategori' => 'BM',
            'kota' => 'Pidie Jaya',
            'provinsi' => 'Aceh',
            'latitude' => 5.2350,
            'longitude' => 96.2450,
            'status_survei' => 'sudah',
            'jenis_infrastruktur' => 'Jembatan',
        ]);
    }
}