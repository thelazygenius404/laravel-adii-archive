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
        Schema::create('entite_productrices', function (Blueprint $table) {
            $table->id();
            $table->string('nom_entite');
            $table->unsignedBigInteger('entite_parent')->nullable();
            $table->string('code_entite')->unique();
            $table->foreignId('id_organisme')->constrained('organismes')->onDelete('cascade');
            $table->timestamps();

            // Foreign key for parent entity (self-referential)
            $table->foreign('entite_parent')->references('id')->on('entite_productrices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entite_productrices');
    }
};
