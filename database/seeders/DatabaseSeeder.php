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
        // 1. Buat Akun Admin
        $admin = User::create([
            'name'     => 'Admin PU',
            'email'    => 'admin@pu.go.id',
            'role'     => 'admin',
            'password' => Hash::make('password123'),
        ]);

        // 2. Buat Akun Surveyor
        $surveyor = User::create([
            'name'     => 'Surveyor Lapangan',
            'email'    => 'surveyor@pu.go.id',
            'role'     => 'surveyor',
            'password' => Hash::make('password123'),
        ]);

        // 3. Data Titik Contoh 1 (Belum Survei - Marker Merah, Ditugaskan ke Surveyor)
        Infrastructure::create([
            // Identitas & Sumber Data
            'kode_infrastruktur'   => 'PU-CK-001',
            'source_table'         => 'data_eksisting_ck',
            'source_id'            => 'SRC-1001',

            // Kolom Utama
            'nama_infrastruktur'   => 'Kantor Camat Bandar Baru',
            'kategori'             => 'CK',
            'jenis_infrastruktur'  => 'Gedung Negara',
            'kota_kabupaten'   => 'Pidie Jaya',
            'provinsi'             => 'Aceh',
            'latitude'             => 5.19990000,
            'longitude'            => 96.11070000,

            // Verifikasi Google Maps & Spasial
            'g_latitude'           => 5.20050000,
            'g_longitude'          => 96.11120000,
            'g_name'               => 'Kantor Camat Bandar Baru',
            'gmaps_url'            => 'https://maps.google.com/?q=5.2005,96.1112',
            'distance_diff_m'      => 86.4,
            'is_coordinates_close' => true,
            'name_verification'    => 'Cocok',
            'verification'         => 'Verified by System',

            // Deteksi OCR, YOLO & Processing
            'text_found'           => 'KANTOR CAMAT BANDAR BARU KABUPATEN PIDIE JAYA',
            'best_ocr_text'        => 'KANTOR CAMAT BANDAR BARU',
            'best_score'           => 0.88,
            'yolo_detection'       => false,
            'type_detected'        => null,
            'processing_status'    => 'completed',

            // Status Survei & BIG (Boolean)
            'status_survey'        => false,
            'status_lapor_big'     => false,

            // Operasional & Penugasan
            'assigned_to'          => $surveyor->id,
        ]);

        // 4. Data Titik Contoh 2 (Sudah Survei - Marker Hijau, Lengkap dengan Hasil Lapangan)
        Infrastructure::create([
            // Identitas & Sumber Data
            'kode_infrastruktur'   => 'PU-BM-002',
            'source_table'         => 'data_eksisting_bm',
            'source_id'            => 'SRC-2001',

            // Kolom Utama
            'nama_infrastruktur'   => 'Jembatan Meureudu',
            'kategori'             => 'BM',
            'jenis_infrastruktur'  => 'Jembatan',
            'kota_kabupaten'   => 'Pidie Jaya',
            'provinsi'             => 'Aceh',
            'latitude'             => 5.23500000,
            'longitude'            => 96.24500000,

            // Verifikasi Google Maps & Spasial
            'g_latitude'           => 5.23520000,
            'g_longitude'          => 96.24510000,
            'g_name'               => 'Jembatan Meureudu',
            'gmaps_url'            => 'https://maps.google.com/?q=5.2352,96.2451',
            'distance_diff_m'      => 24.8,
            'is_coordinates_close' => true,
            'name_verification'    => 'Cocok',
            'verification'         => 'Verified by Surveyor',

            // Deteksi OCR, YOLO & Processing
            'text_found'           => 'JEMBATAN MEUREUDU KEMENTERIAN PU',
            'best_ocr_text'        => 'JEMBATAN MEUREUDU',
            'best_score'           => 0.96,
            'yolo_detection'       => true,
            'type_detected'        => 'Jembatan',
            'processing_status'    => 'completed',

            // Status Survei & BIG (Boolean)
            'status_survey'        => true,
            'status_lapor_big'     => true,

            // Hasil Survei Lapangan
            'survey_latitude'      => 5.23510000,
            'survey_longitude'     => 96.24500000,
            'nama_hasil_survey'    => 'Jembatan Meureudu (Lajur Nasional)',
            'alasan_verifikasi'    => 'Kondisi struktur jembatan baik dan sesuai papan nama.',
            'surveyed_by'          => $surveyor->id,
            'surveyed_at'          => now(),
        ]);
    }
}