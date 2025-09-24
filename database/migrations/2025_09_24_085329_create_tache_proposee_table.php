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
        Schema::create('tache_proposee', function (Blueprint $t) {
            $t->id('id_tache_prop');
            $t->foreignId('id_projet')->constrained('projet', 'id_projet')->cascadeOnDelete();
            $t->foreignId('created_by')->constrained('utilisateur', 'id_utilisateur');
            $t->foreignId('id_utilisateur')->nullable()->constrained('utilisateur', 'id_utilisateur');
            $t->foreignId('id_epic')->nullable()->constrained('epic', 'id_epic');
            $t->foreignId('id_sprint')->nullable()->constrained('sprint', 'id_sprint');
            $t->string('titre');
            $t->text('description')->nullable();
            $t->date('deadline')->nullable();
            $t->enum('approval', ['pending','approved','rejected'])->default('pending');
            $t->foreignId('decided_by')->nullable()->constrained('utilisateur', 'id_utilisateur');
            $t->timestamp('decided_at')->nullable();
            $t->text('decided_comment')->nullable();
            $t->timestamp('created_at')->useCurrent();
            $t->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $t->index(['id_projet', 'approval']);
            $t->index(['created_by', 'id_projet']);
            $t->index(['id_projet', 'id_sprint']);
            $t->index(['id_projet', 'id_epic']);
            $t->index(['id_projet', 'approval', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tache_proposee');
    }
};
