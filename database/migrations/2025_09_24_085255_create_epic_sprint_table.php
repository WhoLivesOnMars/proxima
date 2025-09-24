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
        Schema::create('epic_sprint', function (Blueprint $t) {
            $t->foreignId('id_epic')->constrained('epic', 'id_epic')->cascadeOnDelete();
            $t->foreignId('id_sprint')->constrained('sprint', 'id_sprint')->cascadeOnDelete();
            $t->foreignId('id_projet')->constrained('projet', 'id_projet')->cascadeOnDelete();

            $t->primary(['id_epic', 'id_sprint']);
            $t->unique(['id_epic', 'id_sprint', 'id_projet']);
            $t->index(['id_epic', 'id_projet']);
            $t->index(['id_sprint', 'id_projet']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epic_sprint');
    }
};
