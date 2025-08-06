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
        Schema::create('plan_classement', function (Blueprint $table) {
            $table->id();
            $table->string('code_classement', 20)->unique(); // Le numéro de règle Excel (ex: 100.10.1, 520.1)
            $table->string('objet_classement', 255); // Le type de dossiers du fichier Excel
            $table->text('description')->nullable(); // Description détaillée
            $table->timestamps();

            $table->index('code_classement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_classement');
    }
};