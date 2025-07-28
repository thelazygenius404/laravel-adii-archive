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
            $table->string('NO_regle', 10);
            $table->integer('delais_legaux');
            $table->string('nature_dossier', 50);
            $table->text('reference');
            $table->foreignId('plan_classement_id')->constrained('plan_classement')->onDelete('cascade');
            $table->string('sort_final', 5);
            $table->integer('archive_courant');
            $table->integer('archive_intermediaire');
            $table->text('observation');
            $table->timestamps();

            $table->index('NO_regle');
            $table->index('plan_classement_id');
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