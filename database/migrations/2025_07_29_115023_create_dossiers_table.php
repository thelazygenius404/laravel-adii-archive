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
        Schema::create('dossiers', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('titre');
            $table->date('date_creation');
            $table->string('cote_classement')->nullable();
            $table->text('description')->nullable();
            $table->text('mots_cles')->nullable();
            $table->date('date_elimination_prevue')->nullable();
            $table->enum('statut', ['actif', 'archive', 'elimine', 'en_cours'])->default('actif');
            $table->string('type_piece')->nullable();
            $table->boolean('disponible')->default(true);
            $table->foreignId('boite_id')->constrained('boites')->onDelete('cascade');
            $table->foreignId('calendrier_conservation_id')->constrained('calendrier_conservation')->onDelete('cascade');
            $table->timestamps();

            $table->index(['boite_id', 'statut']);
            $table->index('calendrier_conservation_id');
            $table->index('numero');
            $table->index('date_elimination_prevue');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossiers');
    }
};