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
        Schema::create('boites', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('code_thematique')->nullable();
            $table->string('code_topo')->nullable();
            $table->integer('capacite');
            $table->integer('nbr_dossiers')->default(0);
            $table->boolean('detruite')->default(false);
            $table->foreignId('position_id')->constrained('positions')->onDelete('cascade');
            $table->timestamps();

            $table->index(['position_id', 'detruite']);
            $table->index('numero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boites');
    }
};