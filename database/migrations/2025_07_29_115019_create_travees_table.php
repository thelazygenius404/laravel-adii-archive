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
        Schema::create('travees', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->foreignId('salle_id')->constrained('salles')->onDelete('cascade');
            $table->timestamps();

            $table->index('salle_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travees');
    }
};