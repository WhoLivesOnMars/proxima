<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tache', function (Blueprint $table) {
            $table->dropForeign(['id_epic']);
        });

        DB::statement('ALTER TABLE `tache` MODIFY `id_epic` BIGINT UNSIGNED NULL');

        Schema::table('tache', function (Blueprint $table) {
            $table->foreign('id_epic')
                ->references('id_epic')->on('epic')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tache', function (Blueprint $table) {
            $table->dropForeign(['id_epic']);
        });

        DB::statement('ALTER TABLE `tache` MODIFY `id_epic` BIGINT UNSIGNED NOT NULL');

        Schema::table('tache', function (Blueprint $table) {
            $table->foreign('id_epic')
                ->references('id_epic')->on('epic')
                ->restrictOnDelete();
        });
    }
};
