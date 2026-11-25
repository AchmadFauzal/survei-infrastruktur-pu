<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infrastructures', function (Blueprint $table) {
            $table->id();

            // 1. Kolom Identitas & Sumber Data Asli (Dipertahankan)
            $table->string('kode_infrastruktur')->nullable();
            $table->string('source_table')->nullable();
            $table->string('source_id')->nullable();

            // 2. Kolom Utama (Disamakan dengan tableContent)
            $table->string('nama_infrastruktur');          // Sebelumnya: nama_infrastruktur
            $table->string('kategori', 20)->nullable();    // BM, CK, PS, SDA, SETJEN
            $table->string('jenis_infrastruktur')->nullable();
            $table->string('kota_kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->decimal('latitude', 11, 8)->default(0);
            $table->decimal('longitude', 11, 8)->default(0);

            // 3. Kolom Verifikasi Google Maps & Spasial (Gabungan Lama + Baru)
            $table->decimal('g_latitude', 11, 8)->nullable();
            $table->decimal('g_longitude', 11, 8)->nullable();
            $table->string('g_name')->nullable();
            $table->text('gmaps_url')->nullable();
            $table->float('distance_diff_m')->nullable();
            $table->boolean('is_coordinates_close')->nullable();
            $table->string('name_verification')->nullable();
            $table->string('verification')->nullable();

            // 4. Kolom Hasil Deteksi OCR, YOLO & Processing (Gabungan Lama + Baru)
            $table->text('text_found')->nullable();
            $table->text('best_ocr_text')->nullable();
            $table->float('best_score')->nullable();
            $table->boolean('yolo_detection')->nullable();
            $table->string('type_detected')->nullable();
            $table->string('processing_status')->nullable();

            // 5. Status Survei & Pelaporan BIG (Boolean)
            $table->boolean('status_survey')->default(false);    // Sebelumnya: status_survei
            $table->boolean('status_lapor_big')->default(false); // Kolom baru BIG

            // 6. Kolom Hasil Survei Lapangan & Penugasan (Dipertahankan)
            $table->decimal('survey_latitude', 11, 8)->nullable();
            $table->decimal('survey_longitude', 11, 8)->nullable();
            $table->string('nama_hasil_survey')->nullable();
            $table->text('alasan_verifikasi')->nullable();
            $table->foreignId('surveyed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('surveyed_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infrastructures');
    }
};