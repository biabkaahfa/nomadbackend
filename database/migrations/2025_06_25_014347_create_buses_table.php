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
        Schema::create('buses', function (Blueprint $table) {
    $table->id();
    $table->integer('numeroBus');
    $table->integer('nombrePlaces');
    $table->integer('nombrePlaceDispo');

    $table->unsignedBigInteger('idCompagnie');
    $table->foreign('idCompagnie')
          ->references('id')
          ->on('compagnies')
          ->onDelete('cascade');

    $table->enum('status', ['Actif', 'Inactif']);
    $table->timestamps();
});

    //     'nombrePlaces',
    //     'idVoyage',
    //     'nombrePlaceDispo'
     }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
