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
        Schema::create('projet', function (Blueprint $t) {
            $t->id('id_projet');
            $t->string('nom');
            $t->text('description')->nullable();
            $t->foreignId('owner_id')->constrained('utilisateur', 'id_utilisateur');
            $t->enum('status', ['active', 'completed'])->default('active');
            $t->enum('visibility', ['private', 'shared', 'public'])->default('private');
            $t->uuid('share_token')->unique()->nullable();
            $t->timestamp('created_at')->useCurrent();
            $t->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projet');
    }
};
