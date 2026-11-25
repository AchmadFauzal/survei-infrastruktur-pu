<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
