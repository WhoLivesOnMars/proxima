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
        Schema::create('project_invitations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_projet');
            $table->string('email');
            $table->string('token')->unique();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->foreign('id_projet')->references('id_projet')->on('projet')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_invitations');
    }
};
