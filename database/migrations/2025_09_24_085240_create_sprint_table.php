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
        Schema::create('sprint', function (Blueprint $t) {
            $t->id('id_sprint');
            $t->foreignId('id_projet')->constrained('projet', 'id_projet')->cascadeOnDelete();
            $t->string('nom');
            $t->date('start_date');
            $t->integer('duree');

            $t->unique(['id_projet', 'nom']);
            $t->unique(['id_sprint', 'id_projet']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sprint');
    }
};
