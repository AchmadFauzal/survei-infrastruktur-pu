<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('infrastructures')) {
            return;
        }

        Schema::table('infrastructures', function (Blueprint $table) {
            if (Schema::hasColumn('infrastructures', 'nama_objek')
                && !Schema::hasColumn('infrastructures', 'nama_infrastruktur')) {
                $table->renameColumn('nama_objek', 'nama_infrastruktur');
            }
            if (Schema::hasColumn('infrastructures', '$item->kota')
                && !Schema::hasColumn('infrastructures', 'kota_kabupaten')) {
                $table->renameColumn('$item->kota', 'kota_kabupaten');
            } elseif (Schema::hasColumn('infrastructures', 'kota')
                && !Schema::hasColumn('infrastructures', 'kota_kabupaten')) {
                $table->renameColumn('kota', 'kota_kabupaten');
            }
            if (Schema::hasColumn('infrastructures', 'status_survei')
                && !Schema::hasColumn('infrastructures', 'status_survey')) {
                $table->renameColumn('status_survei', 'status_survey');
            }
        });

        if (DB::getDriverName() === 'pgsql') {
            if (Schema::hasColumn('infrastructures', 'status_survey')) {
                DB::statement("ALTER TABLE infrastructures ALTER COLUMN status_survey TYPE boolean USING CASE WHEN status_survey::text IN ('sudah','true','1','t') THEN true ELSE false END");
                DB::statement("ALTER TABLE infrastructures ALTER COLUMN status_survey SET DEFAULT false");
            }
            if (Schema::hasColumn('infrastructures', 'yolo_detection')) {
                DB::statement("ALTER TABLE infrastructures ALTER COLUMN yolo_detection TYPE boolean USING CASE WHEN yolo_detection::text IN ('true','1','t','ya','yes') THEN true ELSE false END");
            }
            if (Schema::hasColumn('infrastructures', 'is_coordinates_close')) {
                DB::statement("ALTER TABLE infrastructures ALTER COLUMN is_coordinates_close TYPE boolean USING CASE WHEN is_coordinates_close::text IN ('true','1','t','ya','yes') THEN true ELSE false END");
            }
            if (Schema::hasColumn('infrastructures', 'status_lapor_big')) {
                DB::statement("ALTER TABLE infrastructures ALTER COLUMN status_lapor_big TYPE boolean USING CASE WHEN status_lapor_big::text IN ('true','1','t','ya','yes') THEN true ELSE false END");
                DB::statement("ALTER TABLE infrastructures ALTER COLUMN status_lapor_big SET DEFAULT false");
            }
        }

        Schema::table('infrastructures', function (Blueprint $table) {
            if (!Schema::hasColumn('infrastructures', 'kota_kabupaten')) {
                $table->string('kota_kabupaten')->nullable();
            }
            if (!Schema::hasColumn('infrastructures', 'status_survey')) {
                $table->boolean('status_survey')->default(false);
            }
            if (!Schema::hasColumn('infrastructures', 'status_lapor_big')) {
                $table->boolean('status_lapor_big')->default(false);
            }
            if (!Schema::hasColumn('infrastructures', 'is_coordinates_close')) {
                $table->boolean('is_coordinates_close')->nullable();
            }
            if (!Schema::hasColumn('infrastructures', 'yolo_detection')) {
                $table->boolean('yolo_detection')->nullable();
            }
            if (!Schema::hasColumn('infrastructures', 'best_score')) {
                $table->float('best_score')->nullable();
            }
            if (!Schema::hasColumn('infrastructures', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('infrastructures')) {
            return;
        }

        Schema::table('infrastructures', function (Blueprint $table) {
            if (Schema::hasColumn('infrastructures', 'kota_kabupaten')
                && !Schema::hasColumn('infrastructures', 'kota')) {
                $table->renameColumn('kota_kabupaten', 'kota');
            }
        });
    }
};
