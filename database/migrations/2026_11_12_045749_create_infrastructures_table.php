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
            $table->string('kode_infrastruktur')->nullable();
            $table->string('source_table')->nullable();
            $table->string('source_id')->nullable();
            $table->string('nama_objek');
            $table->string('kategori', 10)->default('CK');
            $table->string('jenis_infrastruktur')->nullable();
            $table->string('kota')->default('-');
            $table->string('provinsi')->default('-');
            $table->decimal('latitude', 11, 8)->default(0);
            $table->decimal('longitude', 11, 8)->default(0);

            // Kolom tambahan dari data ekspor CSV/JSON
            $table->decimal('g_latitude', 11, 8)->nullable();
            $table->decimal('g_longitude', 11, 8)->nullable();
            $table->string('g_name')->nullable();
            $table->text('gmaps_url')->nullable();
            $table->string('distance_diff_m')->nullable();
            $table->string('yolo_detection')->nullable();
            $table->string('type_detected')->nullable();
            $table->string('name_verification')->nullable();
            $table->string('verification')->nullable();
            $table->text('best_ocr_text')->nullable();
            $table->text('text_found')->nullable();
            $table->string('processing_status')->nullable();

            // Kolom status & hasil survei lapangan
            $table->enum('status_survei', ['belum', 'sudah'])->default('belum');
            $table->decimal('survey_latitude', 11, 8)->nullable();
            $table->decimal('survey_longitude', 11, 8)->nullable();
            $table->string('nama_hasil_survei')->nullable();
            $table->text('alasan_verifikasi')->nullable();
            $table->foreignId('surveyed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('surveyed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infrastructures');
    }
};