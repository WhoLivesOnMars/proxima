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
        Schema::create('notification_pref', function (Blueprint $t) {
            $t->foreignId('id_utilisateur')->constrained('utilisateur', 'id_utilisateur')->primary()->cascadeOnDelete();
            $t->boolean('email_enabled')->default(true);
            $t->boolean('in_app_enabled')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_pref');
    }
};
