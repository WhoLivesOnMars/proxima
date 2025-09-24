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
        Schema::create('membre_projet', function (Blueprint $t) {
            $t->foreignId('id_projet')->constrained('projet', 'id_projet')->cascadeOnDelete();
            $t->foreignId('id_utilisateur')->constrained('utilisateur', 'id_utilisateur')->cascadeOnDelete();
            $t->string('role')->default('user');

            $t->primary(['id_projet', 'id_utilisateur']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membre_projet');
    }
};
