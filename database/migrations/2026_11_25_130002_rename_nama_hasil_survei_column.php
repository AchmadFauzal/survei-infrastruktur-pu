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

        $hasOldColumn = Schema::hasColumn('infrastructures', 'nama_hasil_survei');
        $hasNewColumn = Schema::hasColumn('infrastructures', 'nama_hasil_survey');

        if ($hasOldColumn && !$hasNewColumn) {
            Schema::table('infrastructures', function (Blueprint $table) {
                $table->renameColumn('nama_hasil_survei', 'nama_hasil_survey');
            });
        } elseif (!$hasNewColumn) {
            Schema::table('infrastructures', function (Blueprint $table) {
                $table->string('nama_hasil_survey')->nullable();
            });
        } elseif ($hasOldColumn) {
            DB::table('infrastructures')
                ->whereNull('nama_hasil_survey')
                ->update(['nama_hasil_survey' => DB::raw('nama_hasil_survei')]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('infrastructures')
            || !Schema::hasColumn('infrastructures', 'nama_hasil_survey')
            || Schema::hasColumn('infrastructures', 'nama_hasil_survei')) {
            return;
        }

        Schema::table('infrastructures', function (Blueprint $table) {
            $table->renameColumn('nama_hasil_survey', 'nama_hasil_survei');
        });
    }
};
