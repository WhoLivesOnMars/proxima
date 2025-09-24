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
        Schema::create('notification', function (Blueprint $t) {
            $t->id('id_notification');
            $t->foreignId('id_utilisateur')->constrained('utilisateur', 'id_utilisateur')->cascadeOnDelete();
            $t->foreignId('id_projet')->nullable()->constrained('projet', 'id_projet')->cascadeOnDelete();
            $t->foreignId('id_tache')->nullable()->constrained('tache', 'id_tache')->cascadeOnDelete();
            $t->enum('type', ['deadline_soon','overdue','task_updated','task_submitted','task_approved','task_rejected']);
            $t->text('message')->nullable();
            $t->timestamp('read_at')->nullable();
            $t->timestamp('created_at')->useCurrent();

            $t->index(['id_utilisateur', 'read_at']);
            $t->index(['id_tache', 'type']);
            $t->index(['id_projet', 'type']);
            $t->index(['id_utilisateur', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification');
    }
};
