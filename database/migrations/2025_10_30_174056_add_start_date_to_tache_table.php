<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tache', function (Blueprint $t) {
            $t->date('start_date')->nullable()->after('description');
            $t->index(['id_sprint','start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tache', function (Blueprint $t) {
            $t->dropIndex(['tache_id_sprint_start_date_index']);
            $t->dropColumn('start_date');
        });
    }
};
