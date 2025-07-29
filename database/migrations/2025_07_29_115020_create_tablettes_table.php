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
        Schema::create('tablettes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->foreignId('travee_id')->constrained('travees')->onDelete('cascade');
            $table->timestamps();

            $table->index('travee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tablettes');
    }
};