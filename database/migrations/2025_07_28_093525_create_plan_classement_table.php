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
            $table->integer('code_classement');
            $table->string('objet_classement', 500);
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