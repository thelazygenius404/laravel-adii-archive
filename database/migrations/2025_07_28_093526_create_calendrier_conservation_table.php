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
        Schema::create('calendrier_conservation', function (Blueprint $table) {
            $table->id();
            $table->text('pieces_constituant')->nullable(); // Pièces constituant le dossier
            $table->char('principal_secondaire', 1)->nullable(); // P ou S
            $table->string('delai_legal', 50)->nullable(); // Délai légal (peut être texte ou nombre)
            $table->text('reference_juridique')->nullable(); // Référence juridique
            $table->string('archives_courantes', 100); // Archive courante (peut être du texte comme "Validité du texte")
            $table->string('archives_intermediaires', 50); // Archive intermédiaire 
            $table->char('sort_final', 1); // C, D, T (Conservation, Destruction, Tri)
            $table->text('observation')->nullable();
            $table->string('plan_classement_code', 20); // Référence au code du plan de classement
            $table->foreign('plan_classement_code')->references('code_classement')->on('plan_classement')->onDelete('cascade');
            $table->timestamps();

            $table->index('plan_classement_code');
            $table->index('sort_final');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendrier_conservation');
    }
};