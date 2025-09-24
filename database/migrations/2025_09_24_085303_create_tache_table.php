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
        Schema::create('tache', function (Blueprint $t) {
            $t->id('id_tache');
            $t->foreignId('id_projet')->constrained('projet', 'id_projet')->cascadeOnDelete();
            $t->foreignId('id_epic')->constrained('epic', 'id_epic');
            $t->foreignId('id_sprint')->constrained('sprint', 'id_sprint');
            $t->foreignId('id_utilisateur')->constrained('utilisateur', 'id_utilisateur');
            $t->string('titre');
            $t->text('description')->nullable();
            $t->date('deadline')->nullable();
            $t->enum('status', ['todo', 'in_progress', 'done'])->default('todo');
            $t->timestamp('created_at')->useCurrent();
            $t->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $t->index(['id_epic', 'id_projet']);
            $t->index(['id_sprint', 'id_projet']);
            $t->index(['id_epic', 'id_sprint']);

            $t->index(['id_projet', 'id_utilisateur']);
            $t->index(['id_projet', 'status']);
            $t->index(['id_projet', 'deadline']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tache');
    }
};
