<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Header Log (1 baris = 1 kali surveyor menekan "Selesaikan & Lapor Tugas")
        Schema::create('survey_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedInteger('total_infrastructures')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps(); // created_at = waktu penyelesaian laporan
        });

        // Tabel Detail Daftar Infrastruktur dalam 1 Batch Log tersebut
        Schema::create('survey_log_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_log_id')->constrained('survey_logs')->onDelete('cascade');
            $table->foreignId('infrastructure_id')->constrained('infrastructures')->onDelete('cascade');
            $table->decimal('survey_latitude', 11, 8)->nullable();
            $table->decimal('survey_longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_log_items');
        Schema::dropIfExists('survey_logs');
    }
};