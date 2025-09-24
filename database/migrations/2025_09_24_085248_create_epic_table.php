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
        Schema::create('epic', function (Blueprint $t) {
            $t->id('id_epic');
            $t->foreignId('id_projet')->constrained('projet', 'id_projet')->cascadeOnDelete();
            $t->string('nom');

            $t->unique(['id_projet', 'nom']);
            $t->unique(['id_epic', 'id_projet']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epic');
    }
};
