<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wilayah_boundaries', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique()->index(); // Contoh: '33.11' (Kab. Sukoharjo) atau '33' (Jawa Tengah)
            $table->string('nama');                        // Nama Wilayah (contoh: 'Kabupaten Sukoharjo')
            $table->tinyInteger('level')->index();         // 1 = Provinsi, 2 = Kabupaten/Kota
            $table->decimal('lat', 10, 7)->nullable();   // Titik tengah (centroid) Latitude
            $table->decimal('lng', 10, 7)->nullable();   // Titik tengah (centroid) Longitude
            $table->jsonb('geojson');                      // Geometry GeoJSON (Polygon / MultiPolygon)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah_boundaries');
    }
};