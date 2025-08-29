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
        Schema::create('parametres', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idCompagnie')->unique()->nullable();
            $table->string('logo')->nullable();
            $table->string('couleur_principale')->default('#0066CC');
            $table->string('couleur_secondaire')->default('#FF9900');
            $table->string('slogan')->nullable();
            $table->timestamps();

            $table->foreign('idCompagnie')->references('id')->on('compagnies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametres');
    }
};
